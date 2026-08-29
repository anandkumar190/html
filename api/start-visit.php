<?php
/**
 * API: Start Administrative Visit (Punch In)
 * 
 * Purpose:
 *   Initial step to start a visit. Saves user_id, in_time, gps coordinates,
 *   reason_category, and sets status = 0.
 *   Returns the newly created administrative_visit_id for the subsequent mark-visit call.
 * 
 * Request Method: POST (Form Data or JSON)
 * Parameters:
 *   - user_id (or userid / employee_id) [Required]: Employee ID
 *   - gps_latitude (or lat / latitude) [Required]: Latitude
 *   - gps_longitude (or lng / longitude / log) [Required]: Longitude
 *   - reason_category (or category) [Required]:
 *       1 = 'New Distributor Search'
 *       2 = 'New Distributor KYC'
 *       3 = 'Miscellaneous Visit'
 *   - gps_address_autofilled (or address) [Optional]: GPS address
 *   - in_time [Optional]: Defaults to CURRENT_TIMESTAMP
 *   - visit_type [Optional]: Default 'ADMINISTRATIVE'
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

// 1. Validate user_id
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

// 2. Validate GPS coordinates
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

// 3. Validate reason_category
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

// 4. Insert Initial Visit Record (status = 0)
$stmt = $con->prepare("
    INSERT INTO administrative_visit (
        user_id, gps_latitude, gps_longitude, gps_address_autofilled,
        in_time, status, visit_type, reason_category, created_at
    ) VALUES (?, ?, ?, ?, ?, 0, ?, ?, NOW())
");

$stmt->bind_param(
    "iddsssi",
    $userId,
    $gpsLatitude,
    $gpsLongitude,
    $gpsAddress,
    $inTime,
    $visitType,
    $reasonCategory
);

if ($stmt->execute()) {
    $visitId = $stmt->insert_id;
    $stmt->close();

    echo json_encode([
        "status" => 1,
        "message" => "Visit started successfully. Proceed with mark-visit when finished.",
        "administrative_visit_id" => $visitId,
        "visit_id" => $visitId,
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
    $err = $stmt->error;
    $stmt->close();
    echo json_encode([
        "status" => 0,
        "message" => "Failed to start visit: " . $err
    ], JSON_PRETTY_PRINT);
    exit();
}
