<?php
/**
 * API: Mark Visit / Complete Administrative Visit
 * 
 * Purpose:
 *   Updates an existing administrative visit record (previously started with status=0)
 *   by saving all remaining visit details, setting status=1, and updating out_time.
 *   If reason_category is 1 (New Distributor Search), it also saves the child
 *   record in `new_distributor_search_details`.
 * 
 * Request Method: POST (Form Data or JSON)
 * Parameters:
 *   - administrative_visit_id (or visit_id / id) [Required]: Visit ID from start-visit
 *   - company_name [Required]: Company / Distributor / Entity Name
 *   - address [Required]: Address text
 *   - city [Required]: City
 *   - pin_code [Required]: 6-digit postal PIN code
 *   - contact_person [Required]: Contact person name
 *   - cell_no [Required]: Phone / Mobile number
 *   - gps_address_autofilled [Optional]: GPS address
 *   - out_time [Optional]: Out-time (defaults to CURRENT_TIMESTAMP)
 *   - visit_reason [Conditional]: Required for Category 3 (>= 15 words)
 * 
 *   Child Table Parameters (Required only when reason_category == 1):
 *   - areas_covered [Required]: Areas covered text (>= 6 words)
 *   - gt_stores_covered [Optional]: GT stores count (default 0)
 *   - mt_stores_covered [Optional]: MT stores count (default 0)
 *   - wholesalers_covered [Optional]: Wholesalers count (default 0)
 *   - horeca_covered [Optional]: HoReCa stores count (default 0)
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

// Read input: prioritize $_POST (Form Data), fallback to raw JSON
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

// 1. Validate Visit ID
$visitId = $input['administrative_visit_id'] ?? $input['visit_id'] ?? $input['id'] ?? $input['entryid'] ?? null;
if (empty($visitId) || !is_numeric($visitId)) {
    echo json_encode([
        "status" => 0,
        "message" => "administrative_visit_id (or visit_id) is required and must be numeric"
    ], JSON_PRETTY_PRINT);
    exit();
}
$visitId = (int)$visitId;

// 2. Fetch existing visit record
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

// If client passed reason_category in mark-visit, allow updating or use existing
if (!empty($input['reason_category']) && is_numeric($input['reason_category'])) {
    $reasonCategory = (int)$input['reason_category'];
}

$categoryName = $categoryMap[$reasonCategory] ?? 'Unknown';

// 3. Validate mandatory fields
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

// 4. Database Transaction: Update Parent and Upsert Child
mysqli_begin_transaction($con);

try {
    // Update administrative_visit: set status = 1 and all remaining fields
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
            // Update
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
            // Insert
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

    // Build Success Response
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
