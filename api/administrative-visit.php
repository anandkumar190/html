<?php
/**
 * API: Administrative Visit & New Distributor Search Details
 * 
 * Endpoints & Usage:
 * 1. Save Visit (POST Form Data or JSON):
 *    - user_id (required): Employee ID
 *    - company_name (required): Company / Distributor / Entity Name
 *    - address (required): Address text
 *    - city (required): City
 *    - pin_code (required): 6-digit postal PIN code
 *    - contact_person (required): Contact person name
 *    - cell_no (required): Mobile / Phone number
 *    - gps_address_autofilled (required): GPS address autofilled
 *    - gps_latitude (required): Latitude coordinate
 *    - gps_longitude (required): Longitude coordinate
 *    - in_time (optional): In time (defaults to current timestamp)
 *    - out_time (optional): Out time
 *    - status (optional): Visit status (default 0)
 *    - visit_type (optional): Default 'ADMINISTRATIVE'
 *    - reason_category (required):
 *        1 = 'New Distributor Search'
 *        2 = 'New Distributor KYC'
 *        3 = 'Miscellaneous Visit'
 *    - visit_reason (conditional):
 *        Required if reason_category == 3 (Must be at least 15 words)
 * 
 *    Child Table Fields (Required only when reason_category == 1):
 *    - areas_covered (required): Areas covered text (Must be at least 6 words)
 *    - gt_stores_covered (optional): Number of General Trade stores (default 0)
 *    - mt_stores_covered (optional): Number of Modern Trade stores (default 0)
 *    - wholesalers_covered (optional): Number of Wholesalers (default 0)
 *    - horeca_covered (optional): Number of HoReCa stores (default 0)
 * 
 * 2. Get Visits (GET):
 *    - user_id (optional): Filter by employee ID
 *    - date (optional): Filter by date (YYYY-MM-DD)
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
 * Word count helper supporting all character sets and spaces
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
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// -------------------------------------------------------------
// POST Request: Save / Start / Mark Visit Record
// -------------------------------------------------------------
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

    $reqAction = $action ?? $input['action'] ?? null;
    $visitIdInput = $input['administrative_visit_id'] ?? $input['visit_id'] ?? $input['id'] ?? null;

    // Check if this is a "mark-visit" / update request
    if ($reqAction === 'mark_visit' || $reqAction === 'mark-visit' || (!empty($visitIdInput) && empty($input['start_visit']))) {
        // Forward directly to mark-visit logic
        require_once(__DIR__ . "/mark-visit.php");
        exit();
    }

    // Check if this is a "start-visit" / punch-in request (status=0)
    if ($reqAction === 'start_visit' || $reqAction === 'start-visit' || isset($input['start_visit']) || (isset($input['status']) && (int)$input['status'] === 0 && empty($input['company_name']))) {
        require_once(__DIR__ . "/start-visit.php");
        exit();
    }

    // 1. Validate user_id
    $userId = $input['user_id'] ?? $input['userid'] ?? $input['employee_id'] ?? null;
    if (empty($userId) || !is_numeric($userId)) {
        echo json_encode([
            "status" => 0,
            "message" => "user_id is required and must be numeric"
        ]);
        exit();
    }
    $userId = (int)$userId;

    // Check if user exists in employees table
    $empCheck = $con->prepare("SELECT id, name FROM employees WHERE id = ? LIMIT 1");
    $empCheck->bind_param("i", $userId);
    $empCheck->execute();
    $empRes = $empCheck->get_result();
    if (!$empRes || $empRes->num_rows === 0) {
        echo json_encode([
            "status" => 0,
            "message" => "Employee not found with user_id: " . $userId
        ]);
        $empCheck->close();
        exit();
    }
    $employeeInfo = $empRes->fetch_assoc();
    $empCheck->close();

    // 2. Validate mandatory fields for administrative_visit
    $companyName = trim((string)($input['company_name'] ?? ''));
    if (empty($companyName)) {
        echo json_encode([
            "status" => 0,
            "message" => "company_name is required"
        ]);
        exit();
    }

    $address = trim((string)($input['address'] ?? ''));
    if (empty($address)) {
        echo json_encode([
            "status" => 0,
            "message" => "address is required"
        ]);
        exit();
    }

    $city = trim((string)($input['city'] ?? ''));
    if (empty($city)) {
        echo json_encode([
            "status" => 0,
            "message" => "city is required"
        ]);
        exit();
    }

    $pinCode = trim((string)($input['pin_code'] ?? $input['pincode'] ?? ''));
    if (!preg_match('/^[0-9]{6}$/', $pinCode)) {
        echo json_encode([
            "status" => 0,
            "message" => "pin_code must be exactly 6 numeric digits"
        ]);
        exit();
    }

    $contactPerson = trim((string)($input['contact_person'] ?? ''));
    if (empty($contactPerson)) {
        echo json_encode([
            "status" => 0,
            "message" => "contact_person is required"
        ]);
        exit();
    }

    $cellNo = trim((string)($input['cell_no'] ?? $input['contact'] ?? $input['mobile'] ?? ''));
    if (empty($cellNo)) {
        echo json_encode([
            "status" => 0,
            "message" => "cell_no is required"
        ]);
        exit();
    }

    $gpsAddress = trim((string)($input['gps_address_autofilled'] ?? $input['gps_address'] ?? ''));
    if (empty($gpsAddress)) {
        // Default to address if gps_address_autofilled is not provided
        $gpsAddress = $address;
    }

    $gpsLatitude = $input['gps_latitude'] ?? $input['latitude'] ?? $input['lat'] ?? null;
    $gpsLongitude = $input['gps_longitude'] ?? $input['longitude'] ?? $input['lng'] ?? null;

    if ($gpsLatitude === null || $gpsLongitude === null || !is_numeric($gpsLatitude) || !is_numeric($gpsLongitude)) {
        echo json_encode([
            "status" => 0,
            "message" => "Valid gps_latitude and gps_longitude are required"
        ]);
        exit();
    }
    $gpsLatitude = (float)$gpsLatitude;
    $gpsLongitude = (float)$gpsLongitude;

    // Optional / Default fields
    $inTime = !empty($input['in_time']) ? date('Y-m-d H:i:s', strtotime($input['in_time'])) : date('Y-m-d H:i:s');
    $outTime = !empty($input['out_time']) ? date('Y-m-d H:i:s', strtotime($input['out_time'])) : null;
    $status = isset($input['status']) && is_numeric($input['status']) ? (int)$input['status'] : 0;
    $visitType = !empty($input['visit_type']) ? trim((string)$input['visit_type']) : 'ADMINISTRATIVE';

    // 3. Reason Category Validation
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
        ]);
        exit();
    }

    $categoryName = $categoryMap[$reasonCategory];
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
            ]);
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
            ]);
            exit();
        }

        $gtStores = isset($input['gt_stores_covered']) ? (int)$input['gt_stores_covered'] : (int)($input['gt_stores'] ?? 0);
        $mtStores = isset($input['mt_stores_covered']) ? (int)$input['mt_stores_covered'] : (int)($input['mt_stores'] ?? 0);
        $wholesalers = isset($input['wholesalers_covered']) ? (int)$input['wholesalers_covered'] : (int)($input['wholesalers'] ?? 0);
        $horeca = isset($input['horeca_covered']) ? (int)$input['horeca_covered'] : (int)($input['horeca'] ?? 0);
    }

    // 4. Database Transaction: Insert into Parent and Child table
    mysqli_begin_transaction($con);

    try {
        // Insert into administrative_visit
        $stmtParent = $con->prepare("
            INSERT INTO administrative_visit (
                user_id, company_name, address, city, pin_code, 
                contact_person, cell_no, gps_address_autofilled, 
                gps_latitude, gps_longitude, in_time, out_time, 
                status, visit_type, reason_category, visit_reason, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmtParent->bind_param(
            "isssssssddssisis",
            $userId,
            $companyName,
            $address,
            $city,
            $pinCode,
            $contactPerson,
            $cellNo,
            $gpsAddress,
            $gpsLatitude,
            $gpsLongitude,
            $inTime,
            $outTime,
            $status,
            $visitType,
            $reasonCategory,
            $visitReason
        );

        if (!$stmtParent->execute()) {
            throw new Exception("Failed to insert into administrative_visit: " . $stmtParent->error);
        }

        $adminVisitId = $stmtParent->insert_id;
        $stmtParent->close();

        $searchDetailsId = null;

        // If Category 1: Insert into new_distributor_search_details
        if ($reasonCategory === 1) {
            $stmtChild = $con->prepare("
                INSERT INTO new_distributor_search_details (
                    administrative_visit_id, areas_covered, gt_stores_covered, 
                    mt_stores_covered, wholesalers_covered, horeca_covered, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmtChild->bind_param(
                "isiiii",
                $adminVisitId,
                $areasCovered,
                $gtStores,
                $mtStores,
                $wholesalers,
                $horeca
            );

            if (!$stmtChild->execute()) {
                throw new Exception("Failed to insert into new_distributor_search_details: " . $stmtChild->error);
            }

            $searchDetailsId = $stmtChild->insert_id;
            $stmtChild->close();
        }

        // Commit Transaction
        mysqli_commit($con);

        // Success Response
        $response = [
            "status" => 1,
            "message" => "Administrative visit recorded successfully",
            "data" => [
                "administrative_visit_id" => $adminVisitId,
                "user_id" => $userId,
                "employee_name" => $employeeInfo['name'],
                "company_name" => $companyName,
                "address" => $address,
                "city" => $city,
                "pin_code" => $pinCode,
                "contact_person" => $contactPerson,
                "cell_no" => $cellNo,
                "gps_latitude" => $gpsLatitude,
                "gps_longitude" => $gpsLongitude,
                "in_time" => $inTime,
                "out_time" => $outTime,
                "visit_type" => $visitType,
                "reason_category" => $reasonCategory,
                "reason_category_name" => $categoryName,
                "visit_reason" => $visitReason ?: null
            ]
        ];

        if ($reasonCategory === 1) {
            $response["data"]["new_distributor_search_details"] = [
                "id" => $searchDetailsId,
                "administrative_visit_id" => $adminVisitId,
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

// -------------------------------------------------------------
// GET Request: Retrieve Visit Records with Rich Filtering
// -------------------------------------------------------------
$filterUser = $_GET['user_id'] ?? $_GET['userid'] ?? $_GET['employee'] ?? null;
$filterDate = $_GET['date'] ?? null;
$filterStartDate = $_GET['start_date'] ?? null;
$filterEndDate = $_GET['end_date'] ?? null;
$filterCategory = $_GET['reason_category'] ?? $_GET['category'] ?? null;
$filterCity = $_GET['city'] ?? null;
$keyword = $_GET['keyword'] ?? $_GET['search'] ?? null;

// Parse date range if reservation format is sent (e.g., "MM/DD/YYYY - MM/DD/YYYY" or "YYYY-MM-DD - YYYY-MM-DD")
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
