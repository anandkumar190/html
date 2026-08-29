<?php
/**
 * Consolidated API: Administrative Visit (Start Visit, Mark Visit & Dashboard Search)
 * 
 * 1. STEP 1: Start Visit / Punch In (POST):
 *    - user_id [Required]: Employee ID
 *    - gps_latitude [Required]: Latitude coordinate
 *    - gps_longitude [Required]: Longitude coordinate
 *    - reason_category [Required]: 1 (New Search), 2 (KYC), 3 (Misc Visit)
 *    - in_time [Optional]: Defaults to CURRENT_TIMESTAMP
 *    - gps_address_autofilled [Optional]: GPS address
 *    Action:
 *      Inserts into `administrative_visit` with status = 0.
 *      Returns `administrative_visit_id` for Step 2.
 * 
 * 2. STEP 2: Mark Visit / Punch Out (POST):
 *    - administrative_visit_id (or visit_id) [Required]: ID from Step 1
 *    - company_name [Required]: Company / Firm name
 *    - address [Required]: Address
 *    - city [Required]: City
 *    - pin_code [Required]: 6-digit PIN code
 *    - contact_person [Required]: Contact person name
 *    - cell_no [Required]: Phone / Mobile number
 *    - gps_address_autofilled [Optional]: GPS address
 *    - out_time [Optional]: Defaults to CURRENT_TIMESTAMP
 *    - visit_reason [Conditional]: Required for Category 3 (>= 15 words)
 *    
 *    Child Table Parameters (Required only when reason_category == 1):
 *    - areas_covered [Required]: Areas covered text (>= 6 words)
 *    - gt_stores_covered [Optional]: GT stores count (default 0)
 *    - mt_stores_covered [Optional]: MT stores count (default 0)
 *    - wholesalers_covered [Optional]: Wholesalers count (default 0)
 *    - horeca_covered [Optional]: HoReCa stores count (default 0)
 *    Action:
 *      Updates `administrative_visit`: sets status = 1, saves out_time & details.
 *      Inserts/Updates child record in `new_distributor_search_details` if category is 1.
 * 
 * 3. STEP 3: Dashboard Reporting / Search Tool (GET):
 *    - Supports reservation date range, employee, category, city, and keyword filtering.
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_reporting(0);
ini_set('display_errors', '0');

require_once(__DIR__ . "/../connect.php");

if (!$con) {
    echo json_encode([
        "status" => 0,
        "message" => "Database connection failed"
    ]);
    exit();
}

/**
 * Word count helper
 */
function getWordCount($str) {
    $str = trim((string)$str);
    if ($str === '') {
        return 0;
    }
    $words = preg_split('/\s+/', $str, -1, PREG_SPLIT_NO_EMPTY);
    return count($words);
}

// Category Mapping
$categoryMap = [
    1 => 'New Distributor Search',
    2 => 'New Distributor KYC',
    3 => 'Miscellaneous Visit'
];

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// =============================================================
// POST Requests: Handle Start-Visit (status=0) or Mark-Visit (status=1)
// =============================================================
if ($method === 'POST') {
    // Read input data: prioritize $_POST, fallback to JSON
    $input = $_POST;
    if (empty($input)) {
        $rawInput = file_get_contents('php://input');
        if (!empty($rawInput)) {
            $jsonData = json_decode($rawInput, true);
            if (is_array($jsonData)) {
                $input = $jsonData;
            }
        }
    }

    $action = $_GET['action'] ?? $input['action'] ?? null;
    $visitId = $input['administrative_visit_id'] ?? $input['visit_id'] ?? $input['id'] ?? $input['entryid'] ?? null;

    // ---------------------------------------------------------
    // CASE A: Start Visit / Punch In (status = 0)
    // Triggered when no visitId is sent, or action is start_visit
    // ---------------------------------------------------------
    if (empty($visitId) || $action === 'start_visit' || $action === 'start-visit' || isset($input['start_visit'])) {
        $userId = $input['user_id'] ?? $input['userid'] ?? $input['employee_id'] ?? null;
        if (empty($userId) || !is_numeric($userId)) {
            echo json_encode([
                "status" => 0,
                "message" => "user_id is required and must be numeric"
            ], JSON_PRETTY_PRINT);
            exit();
        }
        $userId = (int)$userId;

        // Verify employee exists
        $empCheck = $con->prepare("SELECT id, name, empid FROM employees WHERE id = ? LIMIT 1");
        $empCheck->bind_param("i", $userId);
        $empCheck->execute();
        $empRes = $empCheck->get_result();

        if (!$empRes || $empRes->num_rows === 0) {
            echo json_encode([
                "status" => 0,
                "message" => "Employee not found with user_id: " . $userId
            ], JSON_PRETTY_PRINT);
            $empCheck->close();
            exit();
        }
        $employee = $empRes->fetch_assoc();
        $empCheck->close();

        // Validate GPS coordinates
        $gpsLatitude = $input['gps_latitude'] ?? $input['latitude'] ?? $input['lat'] ?? null;
        $gpsLongitude = $input['gps_longitude'] ?? $input['longitude'] ?? $input['lng'] ?? $input['log'] ?? null;

        if ($gpsLatitude === null || $gpsLongitude === null || !is_numeric($gpsLatitude) || !is_numeric($gpsLongitude)) {
            echo json_encode([
                "status" => 0,
                "message" => "Valid gps_latitude and gps_longitude are required"
            ], JSON_PRETTY_PRINT);
            exit();
        }
        $gpsLatitude = (float)$gpsLatitude;
        $gpsLongitude = (float)$gpsLongitude;

        // Validate reason_category
        $rawCategory = $input['reason_category'] ?? $input['category'] ?? null;
        $reasonCategory = null;

        if (is_numeric($rawCategory) && isset($categoryMap[(int)$rawCategory])) {
            $reasonCategory = (int)$rawCategory;
        } elseif (is_string($rawCategory)) {
            $flipped = array_flip($categoryMap);
            if (isset($flipped[trim($rawCategory)])) {
                $reasonCategory = $flipped[trim($rawCategory)];
            }
        }

        if ($reasonCategory === null || !in_array($reasonCategory, [1, 2, 3])) {
            echo json_encode([
                "status" => 0,
                "message" => "Invalid reason_category. Valid values: 1 (New Distributor Search), 2 (New Distributor KYC), 3 (Miscellaneous Visit)",
                "allowed_categories" => $categoryMap
            ], JSON_PRETTY_PRINT);
            exit();
        }

        $categoryName = $categoryMap[$reasonCategory];
        $gpsAddress = trim((string)($input['gps_address_autofilled'] ?? $input['gps_address'] ?? $input['address'] ?? ''));
        $inTime = !empty($input['in_time']) ? date('Y-m-d H:i:s', strtotime($input['in_time'])) : date('Y-m-d H:i:s');
        $visitType = !empty($input['visit_type']) ? trim((string)$input['visit_type']) : 'ADMINISTRATIVE';

        // Insert Initial Visit Record (status = 0)
        $stmtStart = $con->prepare("
            INSERT INTO administrative_visit (
                user_id, gps_latitude, gps_longitude, gps_address_autofilled,
                in_time, status, visit_type, reason_category, created_at
            ) VALUES (?, ?, ?, ?, ?, 0, ?, ?, NOW())
        ");

        $stmtStart->bind_param(
            "iddsssi",
            $userId,
            $gpsLatitude,
            $gpsLongitude,
            $gpsAddress,
            $inTime,
            $visitType,
            $reasonCategory
        );

        if ($stmtStart->execute()) {
            $newVisitId = $stmtStart->insert_id;
            $stmtStart->close();

            echo json_encode([
                "status" => 1,
                "message" => "Visit started successfully. Proceed with mark-visit when finished.",
                "administrative_visit_id" => $newVisitId,
                "visit_id" => $newVisitId,
                "user_id" => $userId,
                "employee_name" => $employee['name'],
                "in_time" => $inTime,
                "gps_latitude" => $gpsLatitude,
                "gps_longitude" => $gpsLongitude,
                "gps_address_autofilled" => $gpsAddress,
                "reason_category" => $reasonCategory,
                "reason_category_name" => $categoryName,
                "visit_status" => 0,
                "status_label" => "In Progress (Pending Mark Visit)"
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit();
        } else {
            $err = $stmtStart->error;
            $stmtStart->close();
            echo json_encode([
                "status" => 0,
                "message" => "Failed to start visit: " . $err
            ], JSON_PRETTY_PRINT);
            exit();
        }
    }

    // ---------------------------------------------------------
    // CASE B: Mark Visit / Complete Visit (status = 1)
    // Triggered when administrative_visit_id is provided
    // ---------------------------------------------------------
    $visitId = (int)$visitId;

    // 1. Fetch existing visit record
    $stmtFetch = $con->prepare("
        SELECT id, user_id, reason_category, in_time, status, visit_type, gps_latitude, gps_longitude, gps_address_autofilled 
        FROM administrative_visit 
        WHERE id = ? 
        LIMIT 1
    ");
    $stmtFetch->bind_param("i", $visitId);
    $stmtFetch->execute();
    $resFetch = $stmtFetch->get_result();

    if (!$resFetch || $resFetch->num_rows === 0) {
        echo json_encode([
            "status" => 0,
            "message" => "Visit record not found with ID: " . $visitId
        ], JSON_PRETTY_PRINT);
        $stmtFetch->close();
        exit();
    }

    $existingVisit = $resFetch->fetch_assoc();
    $stmtFetch->close();

    $reasonCategory = (int)$existingVisit['reason_category'];
    if (!empty($input['reason_category']) && is_numeric($input['reason_category'])) {
        $reasonCategory = (int)$input['reason_category'];
    }
    $categoryName = $categoryMap[$reasonCategory] ?? 'Unknown';

    // 2. Validate mandatory fields for Mark-Visit
    $companyName = trim((string)($input['company_name'] ?? ''));
    if (empty($companyName)) {
        echo json_encode([
            "status" => 0,
            "message" => "company_name is required"
        ], JSON_PRETTY_PRINT);
        exit();
    }

    $address = trim((string)($input['address'] ?? ''));
    if (empty($address)) {
        echo json_encode([
            "status" => 0,
            "message" => "address is required"
        ], JSON_PRETTY_PRINT);
        exit();
    }

    $city = trim((string)($input['city'] ?? ''));
    if (empty($city)) {
        echo json_encode([
            "status" => 0,
            "message" => "city is required"
        ], JSON_PRETTY_PRINT);
        exit();
    }

    $pinCode = trim((string)($input['pin_code'] ?? $input['pincode'] ?? ''));
    if (!preg_match('/^[0-9]{6}$/', $pinCode)) {
        echo json_encode([
            "status" => 0,
            "message" => "pin_code must be exactly 6 numeric digits"
        ], JSON_PRETTY_PRINT);
        exit();
    }

    $contactPerson = trim((string)($input['contact_person'] ?? ''));
    if (empty($contactPerson)) {
        echo json_encode([
            "status" => 0,
            "message" => "contact_person is required"
        ], JSON_PRETTY_PRINT);
        exit();
    }

    $cellNo = trim((string)($input['cell_no'] ?? $input['contact'] ?? $input['mobile'] ?? ''));
    if (empty($cellNo)) {
        echo json_encode([
            "status" => 0,
            "message" => "cell_no is required"
        ], JSON_PRETTY_PRINT);
        exit();
    }

    $gpsAddress = trim((string)($input['gps_address_autofilled'] ?? $input['gps_address'] ?? ''));
    if (empty($gpsAddress)) {
        $gpsAddress = !empty($existingVisit['gps_address_autofilled']) ? $existingVisit['gps_address_autofilled'] : $address;
    }

    $outTime = !empty($input['out_time']) ? date('Y-m-d H:i:s', strtotime($input['out_time'])) : date('Y-m-d H:i:s');
    $visitReason = trim((string)($input['visit_reason'] ?? $input['reason'] ?? ''));

    // Category 3 Validation: Miscellaneous Visit requires >= 15 words
    if ($reasonCategory === 3) {
        $reasonWordCount = getWordCount($visitReason);
        if ($reasonWordCount < 15) {
            echo json_encode([
                "status" => 0,
                "message" => "visit_reason must be at least 15 words for 'Miscellaneous Visit'. Current word count: " . $reasonWordCount,
                "current_word_count" => $reasonWordCount,
                "required_min_words" => 15
            ], JSON_PRETTY_PRINT);
            exit();
        }
    }

    // Category 1 Validation: New Distributor Search requires child table fields
    $areasCovered = '';
    $gtStores = 0;
    $mtStores = 0;
    $wholesalers = 0;
    $horeca = 0;

    if ($reasonCategory === 1) {
        $areasCovered = trim((string)($input['areas_covered'] ?? ''));
        $areasWordCount = getWordCount($areasCovered);
        if ($areasWordCount < 6) {
            echo json_encode([
                "status" => 0,
                "message" => "areas_covered is required and must be at least 6 words for 'New Distributor Search'. Current word count: " . $areasWordCount,
                "current_word_count" => $areasWordCount,
                "required_min_words" => 6
            ], JSON_PRETTY_PRINT);
            exit();
        }

        $gtStores = isset($input['gt_stores_covered']) ? (int)$input['gt_stores_covered'] : (int)($input['gt_stores'] ?? 0);
        $mtStores = isset($input['mt_stores_covered']) ? (int)$input['mt_stores_covered'] : (int)($input['mt_stores'] ?? 0);
        $wholesalers = isset($input['wholesalers_covered']) ? (int)$input['wholesalers_covered'] : (int)($input['wholesalers'] ?? 0);
        $horeca = isset($input['horeca_covered']) ? (int)$input['horeca_covered'] : (int)($input['horeca'] ?? 0);
    }

    // 3. Database Transaction: Update Parent (status = 1) and Upsert Child
    mysqli_begin_transaction($con);

    try {
        $stmtUpdate = $con->prepare("
            UPDATE administrative_visit SET
                company_name = ?,
                address = ?,
                city = ?,
                pin_code = ?,
                contact_person = ?,
                cell_no = ?,
                gps_address_autofilled = ?,
                out_time = ?,
                status = 1,
                reason_category = ?,
                visit_reason = ?
            WHERE id = ?
        ");

        $stmtUpdate->bind_param(
            "ssssssssisi",
            $companyName,
            $address,
            $city,
            $pinCode,
            $contactPerson,
            $cellNo,
            $gpsAddress,
            $outTime,
            $reasonCategory,
            $visitReason,
            $visitId
        );

        if (!$stmtUpdate->execute()) {
            throw new Exception("Failed to update administrative_visit: " . $stmtUpdate->error);
        }
        $stmtUpdate->close();

        $childId = null;

        // If Category 1: Insert or Update new_distributor_search_details
        if ($reasonCategory === 1) {
            $stmtChildCheck = $con->prepare("SELECT id FROM new_distributor_search_details WHERE administrative_visit_id = ? LIMIT 1");
            $stmtChildCheck->bind_param("i", $visitId);
            $stmtChildCheck->execute();
            $resChildCheck = $stmtChildCheck->get_result();
            $existingChild = $resChildCheck->fetch_assoc();
            $stmtChildCheck->close();

            if ($existingChild) {
                $childId = (int)$existingChild['id'];
                $stmtChild = $con->prepare("
                    UPDATE new_distributor_search_details SET
                        areas_covered = ?,
                        gt_stores_covered = ?,
                        mt_stores_covered = ?,
                        wholesalers_covered = ?,
                        horeca_covered = ?
                    WHERE id = ?
                ");
                $stmtChild->bind_param("siiiii", $areasCovered, $gtStores, $mtStores, $wholesalers, $horeca, $childId);
            } else {
                $stmtChild = $con->prepare("
                    INSERT INTO new_distributor_search_details (
                        administrative_visit_id, areas_covered, gt_stores_covered,
                        mt_stores_covered, wholesalers_covered, horeca_covered, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmtChild->bind_param("isiiii", $visitId, $areasCovered, $gtStores, $mtStores, $wholesalers, $horeca);
            }

            if (!$stmtChild->execute()) {
                throw new Exception("Failed to save new_distributor_search_details: " . $stmtChild->error);
            }

            if (!$childId) {
                $childId = $stmtChild->insert_id;
            }
            $stmtChild->close();
        }

        // Commit Transaction
        mysqli_commit($con);

        // Success Response
        $response = [
            "status" => 1,
            "message" => "Visit marked and completed successfully",
            "data" => [
                "administrative_visit_id" => $visitId,
                "user_id" => (int)$existingVisit['user_id'],
                "status" => 1,
                "status_label" => "Completed",
                "company_name" => $companyName,
                "address" => $address,
                "city" => $city,
                "pin_code" => $pinCode,
                "contact_person" => $contactPerson,
                "cell_no" => $cellNo,
                "gps_latitude" => (float)$existingVisit['gps_latitude'],
                "gps_longitude" => (float)$existingVisit['gps_longitude'],
                "gps_address_autofilled" => $gpsAddress,
                "in_time" => $existingVisit['in_time'],
                "out_time" => $outTime,
                "reason_category" => $reasonCategory,
                "reason_category_name" => $categoryName,
                "visit_reason" => $visitReason ?: null
            ]
        ];

        if ($reasonCategory === 1) {
            $response["data"]["new_distributor_search_details"] = [
                "id" => $childId,
                "administrative_visit_id" => $visitId,
                "areas_covered" => $areasCovered,
                "gt_stores_covered" => $gtStores,
                "mt_stores_covered" => $mtStores,
                "wholesalers_covered" => $wholesalers,
                "horeca_covered" => $horeca
            ];
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode([
            "status" => 0,
            "message" => "Transaction error: " . $e->getMessage()
        ], JSON_PRETTY_PRINT);
        exit();
    }
}

// =============================================================
// STEP 3: GET Request (Retrieve Visit Records for Dashboard)
// =============================================================
$filterUser = $_GET['user_id'] ?? $_GET['userid'] ?? $_GET['employee'] ?? null;
$filterDate = $_GET['date'] ?? null;
$filterStartDate = $_GET['start_date'] ?? null;
$filterEndDate = $_GET['end_date'] ?? null;
$filterCategory = $_GET['reason_category'] ?? $_GET['category'] ?? null;
$filterCity = $_GET['city'] ?? null;
$keyword = $_GET['keyword'] ?? $_GET['search'] ?? null;

// Parse date range if reservation format is sent
if (!empty($_GET['reservation'])) {
    $parts = explode('-', $_GET['reservation']);
    if (count($parts) >= 2) {
        $filterStartDate = date('Y-m-d', strtotime(trim($parts[0])));
        $filterEndDate = date('Y-m-d', strtotime(trim($parts[1])));
    }
}

$query = "
    SELECT 
        av.*,
        e.name AS employee_name,
        e.empid AS employee_empid,
        sd.id AS search_details_id,
        sd.areas_covered,
        sd.gt_stores_covered,
        sd.mt_stores_covered,
        sd.wholesalers_covered,
        sd.horeca_covered
    FROM administrative_visit av
    LEFT JOIN employees e ON av.user_id = e.id
    LEFT JOIN new_distributor_search_details sd ON av.id = sd.administrative_visit_id
    WHERE 1=1
";

$params = [];
$types = "";

if (!empty($filterUser)) {
    $query .= " AND av.user_id = ?";
    $params[] = (int)$filterUser;
    $types .= "i";
}

if (!empty($filterCategory)) {
    $query .= " AND av.reason_category = ?";
    $params[] = (int)$filterCategory;
    $types .= "i";
}

if (!empty($filterCity)) {
    $query .= " AND av.city = ?";
    $params[] = trim($filterCity);
    $types .= "s";
}

if (!empty($filterStartDate) && !empty($filterEndDate)) {
    $query .= " AND DATE(av.in_time) BETWEEN ? AND ?";
    $params[] = $filterStartDate;
    $params[] = $filterEndDate;
    $types .= "ss";
} elseif (!empty($filterDate)) {
    $query .= " AND DATE(av.in_time) = ?";
    $params[] = $filterDate;
    $types .= "s";
}

if (!empty($keyword)) {
    $likeKw = "%" . trim($keyword) . "%";
    $query .= " AND (av.company_name LIKE ? OR av.contact_person LIKE ? OR av.cell_no LIKE ? OR av.city LIKE ? OR av.pin_code LIKE ?)";
    $params[] = $likeKw;
    $params[] = $likeKw;
    $params[] = $likeKw;
    $params[] = $likeKw;
    $params[] = $likeKw;
    $types .= "sssss";
}

$query .= " ORDER BY av.id DESC";

$stmt = $con->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

$visits = [];
$cat1Count = 0;
$cat2Count = 0;
$cat3Count = 0;

while ($row = $res->fetch_assoc()) {
    $catId = (int)$row['reason_category'];
    if ($catId === 1) $cat1Count++;
    elseif ($catId === 2) $cat2Count++;
    elseif ($catId === 3) $cat3Count++;

    $item = [
        "id" => (int)$row['id'],
        "user_id" => (int)$row['user_id'],
        "employee_name" => $row['employee_name'] ?? 'N/A',
        "employee_empid" => $row['employee_empid'] ?? '',
        "company_name" => $row['company_name'],
        "address" => $row['address'],
        "city" => $row['city'],
        "pin_code" => $row['pin_code'],
        "contact_person" => $row['contact_person'],
        "cell_no" => $row['cell_no'],
        "gps_address_autofilled" => $row['gps_address_autofilled'],
        "gps_latitude" => (float)$row['gps_latitude'],
        "gps_longitude" => (float)$row['gps_longitude'],
        "in_time" => $row['in_time'],
        "formatted_in_time" => date('d-M-Y h:i A', strtotime($row['in_time'])),
        "out_time" => $row['out_time'],
        "formatted_out_time" => $row['out_time'] ? date('d-M-Y h:i A', strtotime($row['out_time'])) : '-',
        "status" => (int)$row['status'],
        "visit_type" => $row['visit_type'],
        "reason_category" => $catId,
        "reason_category_name" => $categoryMap[$catId] ?? 'Unknown',
        "visit_reason" => $row['visit_reason'] ?? ''
    ];

    if ($catId === 1 && !empty($row['search_details_id'])) {
        $item["new_distributor_search_details"] = [
            "id" => (int)$row['search_details_id'],
            "areas_covered" => $row['areas_covered'] ?? '',
            "gt_stores_covered" => (int)($row['gt_stores_covered'] ?? 0),
            "mt_stores_covered" => (int)($row['mt_stores_covered'] ?? 0),
            "wholesalers_covered" => (int)($row['wholesalers_covered'] ?? 0),
            "horeca_covered" => (int)($row['horeca_covered'] ?? 0)
        ];
    } else {
        $item["new_distributor_search_details"] = null;
    }

    $visits[] = $item;
}
$stmt->close();

echo json_encode([
    "status" => 1,
    "total" => count($visits),
    "summary" => [
        "total_visits" => count($visits),
        "new_distributor_search" => $cat1Count,
        "new_distributor_kyc" => $cat2Count,
        "miscellaneous_visit" => $cat3Count
    ],
    "data" => $visits
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit();
