<?php
/**
 * API: Employee Monthly Attendance (Current Month & Last Month)
 * 
 * Request Method: GET or POST (JSON or Form Data)
 * Parameters:
 *   - user_id (or userid / employee_id) [Required]: ID of the employee
 * 
 * Returns:
 *   JSON object containing employee details, last month summary & day-by-day records,
 *   and current month summary & day-by-day records with all basic details matching
 *   employee-report-of-selected-period.php.
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
 * Generate date range array between start and end date
 */
function getDatesFromRange($start, $end, $format = 'Y-m-d') {
    $array = [];
    $interval = new DateInterval('P1D');
    $realEnd = new DateTime($end);
    $realEnd->add($interval);
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
    foreach ($period as $date) {
        $array[] = $date->format($format);
    }
    return $array;
}

/**
 * Extract user ID from GET, POST or raw JSON body
 */
$userId = null;

// Prioritize Form Data ($_POST)
if (!empty($_POST['user_id'])) {
    $userId = $_POST['user_id'];
} elseif (!empty($_POST['userid'])) {
    $userId = $_POST['userid'];
} elseif (!empty($_POST['employee_id'])) {
    $userId = $_POST['employee_id'];
} elseif (!empty($_POST['employee'])) {
    $userId = $_POST['employee'];
} elseif (!empty($_POST['id'])) {
    $userId = $_POST['id'];
} elseif (!empty($_GET['user_id'])) {
    $userId = $_GET['user_id'];
} elseif (!empty($_GET['userid'])) {
    $userId = $_GET['userid'];
} elseif (!empty($_GET['employee_id'])) {
    $userId = $_GET['employee_id'];
} elseif (!empty($_GET['employee'])) {
    $userId = $_GET['employee'];
} elseif (!empty($_GET['id'])) {
    $userId = $_GET['id'];
} else {
    // Try raw JSON input (fallback)
    $rawInput = file_get_contents('php://input');
    if (!empty($rawInput)) {
        $jsonData = json_decode($rawInput, true);
        if (is_array($jsonData)) {
            $userId = $jsonData['user_id'] ?? $jsonData['userid'] ?? $jsonData['employee_id'] ?? $jsonData['employee'] ?? $jsonData['id'] ?? null;
        }
    }
}

if (empty($userId) || !is_numeric($userId)) {
    echo json_encode([
        "status" => 0,
        "message" => "Valid user_id is required"
    ], JSON_PRETTY_PRINT);
    exit();
}

$userId = (int)$userId;

// Fetch Employee Details
$stmtEmp = $con->prepare("
    SELECT id, name, empid, usertype, email, contact, city, state 
    FROM employees 
    WHERE id = ? AND usertype IN (1, 3) 
    LIMIT 1
");
$stmtEmp->bind_param("i", $userId);
$stmtEmp->execute();
$empRes = $stmtEmp->get_result();

if (!$empRes || $empRes->num_rows === 0) {
    echo json_encode([
        "status" => 0,
        "message" => "Employee not found with ID: " . $userId
    ], JSON_PRETTY_PRINT);
    $stmtEmp->close();
    exit();
}

$employeeData = $empRes->fetch_assoc();
$stmtEmp->close();

/**
 * Helper function to calculate attendance, activity, and sales report for a specific period
 */
function calculateMonthAttendance($con, $employeeId, $startDate, $endDate, $monthLabel) {
    // 1. Fetch all distributor visits in this range
    $dsVisitList = [];
    $dsVisitTimes = [];

    $visitStmt = $con->prepare("
        SELECT 
            dv.distributor_id,
            dv.user_id,
            dv.visit_date,
            dv.visit_time,
            dv.reason_type,
            dv.reason,
            e.name AS distributor_name,
            e.city AS distributor_city
        FROM distributor_visits dv
        LEFT JOIN employees e ON dv.distributor_id = e.id
        WHERE dv.user_id = ?
        AND dv.visit_date BETWEEN ? AND ?
        ORDER BY dv.visit_date ASC, dv.visit_time ASC
    ");

    $visitStmt->bind_param("iss", $employeeId, $startDate, $endDate);
    $visitStmt->execute();
    $dsVisits = $visitStmt->get_result();

    while ($dsVisit = $dsVisits->fetch_assoc()) {
        $vDate = $dsVisit['visit_date'];
        $vTime = $dsVisit['visit_time'];

        $visitInfo = [
            'distributor_id'   => $dsVisit['distributor_id'] ? (int)$dsVisit['distributor_id'] : null,
            'distributor_name' => $dsVisit['distributor_name'] ?? 'N/A',
            'city'             => $dsVisit['distributor_city'] ?? 'N/A',
            'time'             => $vTime,
            'formatted_time'   => $vTime ? date('h:i A', strtotime($vTime)) : 'N/A',
            'reason_type'      => $dsVisit['reason_type'] ?? '',
            'reason'           => $dsVisit['reason'] ?? ''
        ];

        $dsVisitList[$vDate][] = $visitInfo;

        if (!isset($dsVisitTimes[$vDate])) {
            $dsVisitTimes[$vDate] = [
                'min' => $vTime,
                'max' => $vTime
            ];
        } else {
            if ($vTime < $dsVisitTimes[$vDate]['min']) {
                $dsVisitTimes[$vDate]['min'] = $vTime;
            }
            if ($vTime > $dsVisitTimes[$vDate]['max']) {
                $dsVisitTimes[$vDate]['max'] = $vTime;
            }
        }
    }
    $visitStmt->close();

    // 2. Iterate dates
    $dates = getDatesFromRange($startDate, $endDate);
    $totalDaysCount = count($dates);

    $starttimearray = [];
    $endtimearray = [];
    $totalMinutes = 0;
    $totalOldOutlets = 0;
    $totalNewOutlets = 0;
    $totalVisitedOutlets = 0;
    $totalProductiveOutlets = 0;
    $totalOutletsNotVisited = 0;
    $totalProductivePercentage = 0;
    $totalOrderValue = 0.0;
    $sundayCount = 0;
    $workingDaysCount = 0;
    $leaveCount = 0;

    $dailyRecords = [];

    foreach ($dates as $dd) {
        $selectdate = date('Y-m-d', strtotime($dd));
        $dayName = date('l', strtotime($dd));
        $formattedDate = date('d-M-Y', strtotime($dd));
        $isSunday = ($dayName === 'Sunday');

        $starttime = 0;
        $endtime = 0;
        $workingTime = '0 Hrs 0 Mins';
        $workingMinutes = 0;

        // Fetch outlet activities for the day
        $actRes = mysqli_query($con, "
            SELECT activitytime 
            FROM outletactivity 
            WHERE userid = '$employeeId' AND activitydate = '$selectdate' 
            ORDER BY id ASC
        ");

        $count = mysqli_num_rows($actRes);
        for ($i = 0; $i < $count; $i++) {
            $row = mysqli_fetch_assoc($actRes);
            if ($i === 0) $starttime = $row["activitytime"];
            if ($i === $count - 1) $endtime = $row["activitytime"];
        }

        $starttimeStamp = $starttime ? strtotime($starttime) : 0;
        $endtimeStamp = $endtime ? strtotime($endtime) : 0;

        // Check for distributor visit timings on this date
        if (isset($dsVisitTimes[$selectdate])) {
            $dsMin = $dsVisitTimes[$selectdate]['min'];
            $dsMax = $dsVisitTimes[$selectdate]['max'];

            if (!empty($dsMin)) {
                $dsStart = strtotime($dsMin);
                if ($dsStart !== false && ($starttimeStamp === 0 || $dsStart < $starttimeStamp)) {
                    $starttimeStamp = $dsStart;
                }
            }

            if (!empty($dsMax)) {
                $dsEnd = strtotime($dsMax);
                if ($dsEnd !== false && ($endtimeStamp === 0 || $dsEnd > $endtimeStamp)) {
                    $endtimeStamp = $dsEnd;
                }
            }
        }

        // Calculate working hours
        if ($starttimeStamp > 0 && $endtimeStamp > 0) {
            $diffSeconds = abs($endtimeStamp - $starttimeStamp);
            $hours = floor($diffSeconds / 3600);
            $minutes = floor(($diffSeconds % 3600) / 60);
            $workingTime = sprintf('%d Hrs %d Mins', $hours, $minutes);
            $workingMinutes = ($hours * 60) + $minutes;
        }

        // Determine Day Status & Timings
        $firstCallFormatted = null;
        $lastCallFormatted = null;
        $dayStatus = 'Leave';

        if ($starttimeStamp > 0) {
            $starttimearray[] = $starttimeStamp;
            $workingDaysCount++;
            $firstCallFormatted = date('h:i:s A', $starttimeStamp);

            if ($endtimeStamp > 0) {
                $endtimearray[] = $endtimeStamp;
                $lastCallFormatted = date('h:i:s A', $endtimeStamp);
            }
            $dayStatus = 'Present';
        } elseif (isset($dsVisitList[$selectdate])) {
            $workingDaysCount++;
            $dayStatus = 'Distributor Visit';
            $firstCallFormatted = 'Distributor Visit';
            $lastCallFormatted = 'Distributor Visit';
        } else {
            if ($isSunday) {
                $sundayCount++;
                $dayStatus = 'Sunday';
            } else {
                $leaveCount++;
                $dayStatus = 'Leave';
            }
        }

        $totalMinutes += $workingMinutes;

        // Fetch area & route details
        $areaQuery = "
            SELECT DISTINCT
                o.areaid,
                area.area AS areaName
            FROM outletactivity a
            JOIN outlets o ON a.outletid = o.id
            JOIN area ON o.areaid = area.id
            WHERE a.userid = '$employeeId'
                AND a.activitydate = '$selectdate'
                AND a.visittype = '0'
            ORDER BY areaName ASC
        ";
        $areaRes = mysqli_query($con, $areaQuery);

        $routesVisited = [];
        $dayOutletsOnRoute = 0;
        $dayNewOutlets = 0;
        $dayVisitedOutlets = 0;
        $dayProductiveOutlets = 0;
        $dayNotVisitedOutlets = 0;
        $dayOrderValue = 0.0;

        // Fetch Bookings for this employee on this date grouped by area
        $bookingsByArea = [];
        $bStmt = mysqli_query($con, "
            SELECT 
                o.areaid,
                COUNT(DISTINCT b.outlet_id) AS productive_outlets, 
                SUM(b.total_amount) AS total_value_orders 
            FROM booking b
            INNER JOIN outlets o ON b.outlet_id = o.id
            WHERE b.user_id = '$employeeId' AND DATE(b.booking_time) = '$selectdate'
            GROUP BY o.areaid
        ");
        if ($bStmt) {
            while ($bRow = mysqli_fetch_assoc($bStmt)) {
                $bookingsByArea[$bRow['areaid']] = [
                    'productive_outlets' => (int)($bRow['productive_outlets'] ?? 0),
                    'total_value_orders' => (float)($bRow['total_value_orders'] ?? 0)
                ];
            }
        }

        // Global daily booking in case area matching differs
        $globalDailyBooking = mysqli_fetch_assoc(mysqli_query($con, "
            SELECT COUNT(DISTINCT outlet_id) as productive_outlets, SUM(total_amount) as total_value_orders 
            FROM booking 
            WHERE user_id = '$employeeId' AND DATE(booking_time) = '$selectdate'
        ")) ?: ['productive_outlets' => 0, 'total_value_orders' => 0];

        while ($areaRow = mysqli_fetch_assoc($areaRes)) {
            $areaId = $areaRow["areaid"];
            $areaName = $areaRow["areaName"];

            // 1. Total outlets on route (created before this date)
            $totOutRes = mysqli_fetch_assoc(mysqli_query($con, "
                SELECT COUNT(id) as total_outelate 
                FROM outlets 
                WHERE areaid = '$areaId' AND DATE(creationdate) < '$selectdate'
            ")) ?: ['total_outelate' => 0];
            $totalOutletsOnRoute = (int)$totOutRes['total_outelate'];

            // 2. New outlets made
            $newOutRes = mysqli_fetch_assoc(mysqli_query($con, "
                SELECT COUNT(DISTINCT o.id) as new_total_outelate 
                FROM outletactivity a 
                JOIN outlets o ON a.outletid = o.id 
                WHERE a.userid = '$employeeId' AND a.activitydate = '$selectdate' AND a.activitytype = 'New Outlet Create' AND o.areaid = '$areaId'
            ")) ?: ['new_total_outelate' => 0];
            $newOutletMade = (int)$newOutRes['new_total_outelate'];

            // 3. Outlets visited
            $visOutRes = mysqli_fetch_assoc(mysqli_query($con, "
                SELECT COUNT(DISTINCT o.id) as total_visting_outlate 
                FROM outletactivity a 
                JOIN outlets o ON a.outletid = o.id 
                WHERE a.userid = '$employeeId' AND a.activitydate = '$selectdate' AND a.activitytype IN ('Outlet Visit', 'New Outlet Create') AND o.areaid = '$areaId'
            ")) ?: ['total_visting_outlate' => 0];
            $visitedOutlets = (int)$visOutRes['total_visting_outlate'];

            // 4. Bookings for this area
            $areaBooking = $bookingsByArea[$areaId] ?? [
                'productive_outlets' => (int)($globalDailyBooking['productive_outlets'] ?? 0),
                'total_value_orders' => (float)($globalDailyBooking['total_value_orders'] ?? 0)
            ];

            $newTotalOutlets = $totalOutletsOnRoute + $newOutletMade;
            $outletsNotVisited = max(0, $newTotalOutlets - $visitedOutlets);
            $productivePercentage = ($newTotalOutlets > 0)
                ? round(($areaBooking['productive_outlets'] / $newTotalOutlets) * 100, 2)
                : 0.0;

            $routesVisited[] = [
                'area_id'                => (int)$areaId,
                'area_name'              => $areaName,
                'total_outlets_on_route' => $totalOutletsOnRoute,
                'new_outlet_made'        => $newOutletMade,
                'new_total_oulets'       => $newTotalOutlets,
                'no_of_outlets_visited'  => $visitedOutlets,
                'productive_outlets'     => (int)$areaBooking['productive_outlets'],
                'outlets_not_visited'    => $outletsNotVisited,
                'productive_percentage'  => $productivePercentage,
                'total_value_orders'     => (float)$areaBooking['total_value_orders']
            ];

            $dayOutletsOnRoute   += $totalOutletsOnRoute;
            $dayNewOutlets       += $newOutletMade;
            $dayVisitedOutlets   += $visitedOutlets;
            $dayProductiveOutlets += (int)$areaBooking['productive_outlets'];
            $dayNotVisitedOutlets += $outletsNotVisited;
            $dayOrderValue       += (float)$areaBooking['total_value_orders'];
        }

        // If no specific routes were visited but global bookings exist on that day
        if (empty($routesVisited) && ((float)$globalDailyBooking['total_value_orders'] > 0 || (int)$globalDailyBooking['productive_outlets'] > 0)) {
            $dayProductiveOutlets = (int)$globalDailyBooking['productive_outlets'];
            $dayOrderValue = (float)$globalDailyBooking['total_value_orders'];
        }

        $dayNewTotalOutlets = $dayOutletsOnRoute + $dayNewOutlets;
        $dayProductivePercentage = ($dayNewTotalOutlets > 0)
            ? round(($dayProductiveOutlets / $dayNewTotalOutlets) * 100, 2)
            : 0.0;

        // Sum for period totals
        $totalOldOutlets += $dayOutletsOnRoute;
        $totalNewOutlets += $dayNewOutlets;
        $totalVisitedOutlets += $dayVisitedOutlets;
        $totalProductiveOutlets += $dayProductiveOutlets;
        $totalOutletsNotVisited += $dayNotVisitedOutlets;
        $totalProductivePercentage += $dayProductivePercentage;
        $totalOrderValue += $dayOrderValue;

        $dailyRecords[] = [
            'date'               => $selectdate,
            'formatted_date'     => $formattedDate,
            'day'                => $dayName,
            'is_sunday'          => $isSunday,
            'status'             => $dayStatus,
            'first_call_time'    => $firstCallFormatted,
            'last_call_time'     => $lastCallFormatted,
            'working_time'       => $workingTime,
            'working_minutes'    => $workingMinutes,
            'routes_visited'     => $routesVisited,
            'distributor_visits' => $dsVisitList[$selectdate] ?? [],
            'day_totals'         => [
                'total_outlets_on_route' => $dayOutletsOnRoute,
                'new_outlet_made'        => $dayNewOutlets,
                'new_total_oulets'       => $dayNewTotalOutlets,
                'no_of_outlets_visited'  => $dayVisitedOutlets,
                'productive_outlets'     => $dayProductiveOutlets,
                'outlets_not_visited'    => $dayNotVisitedOutlets,
                'productive_percentage'  => $dayProductivePercentage,
                'total_value_orders'     => round($dayOrderValue, 2)
            ]
        ];
    }

    // 3. Compute Averages & Summary
    $totalStime = array_sum($starttimearray);
    $totalEtime = array_sum($endtimearray);

    $avgTotalMinutes = ($totalMinutes > 0 && $workingDaysCount > 0) ? floor($totalMinutes / $workingDaysCount) : 0;
    $avgStartTimeTs = (count($starttimearray) > 0 && $totalStime > 0) ? round($totalStime / count($starttimearray)) : 0;
    $avgEndTimeTs = (count($endtimearray) > 0 && $totalEtime > 0) ? round($totalEtime / count($endtimearray)) : 0;

    $avgHours = floor($avgTotalMinutes / 60);
    $avgMins = $avgTotalMinutes % 60;
    $avgWorkingTimeStr = sprintf('%d Hrs %d Mins', $avgHours, $avgMins);

    $avgTotalProductivePercentage = ($workingDaysCount > 0 && $totalProductivePercentage > 0)
        ? round($totalProductivePercentage / $workingDaysCount, 2)
        : 0.0;

    return [
        'month_label' => $monthLabel,
        'period'      => date("m-d-Y", strtotime($startDate)) . ' To ' . date("m-d-Y", strtotime($endDate)),
        'start_date'  => $startDate,
        'end_date'    => $endDate,
        'summary'     => [
            'total_days'                  => $totalDaysCount,
            'working_days'                => $workingDaysCount,
            'leave_days'                  => $leaveCount,
            'sunday_count'                => $sundayCount,
            'avg_first_call_time'         => $avgStartTimeTs > 0 ? date('h:i:s A', $avgStartTimeTs) : '00:00:00',
            'avg_last_call_time'          => $avgEndTimeTs > 0 ? date('h:i:s A', $avgEndTimeTs) : '00:00:00',
            'avg_working_time'            => $avgWorkingTimeStr,
            'avg_working_minutes'         => $avgTotalMinutes,
            'total_outlets_on_route'      => $totalOldOutlets,
            'total_new_outlets_made'      => $totalNewOutlets,
            'new_total_outlets'           => ($totalOldOutlets + $totalNewOutlets),
            'total_outlets_visited'       => $totalVisitedOutlets,
            'total_productive_outlets'    => $totalProductiveOutlets,
            'total_outlets_not_visited'   => $totalOutletsNotVisited,
            'avg_productive_percentage'   => $avgTotalProductivePercentage,
            'total_order_value'           => round($totalOrderValue, 2)
        ],
        'attendance_records' => $dailyRecords
    ];
}

// -------------------------------------------------------------
// Calculate Date Ranges for Last Month and Current Month
// -------------------------------------------------------------

// Last Month Range: 1st of last month to Last day of last month
$lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
$lastMonthEnd   = date('Y-m-t', strtotime('last day of last month'));
$lastMonthLabel = date('F Y', strtotime('first day of last month'));

// Current Month Range: 1st of current month to Today (or full month)
$currentMonthStart = date('Y-m-01');
$currentMonthEnd   = date('Y-m-d'); // up to today
$currentMonthLabel = date('F Y');

// Generate Data
$lastMonthData    = calculateMonthAttendance($con, $userId, $lastMonthStart, $lastMonthEnd, $lastMonthLabel);
$currentMonthData = calculateMonthAttendance($con, $userId, $currentMonthStart, $currentMonthEnd, $currentMonthLabel);

// Format Final API Response
$response = [
    "status"    => 1,
    "message"   => "Attendance report fetched successfully",
    "employee"  => [
        "id"       => (int)$employeeData['id'],
        "name"     => $employeeData['name'],
        "empid"    => $employeeData['empid'] ?? '',
        "usertype" => $employeeData['usertype'],
        "email"    => $employeeData['email'] ?? '',
        "contact"  => $employeeData['contact'] ?? '',
        "city"     => $employeeData['city'] ?? '',
        "state"    => $employeeData['state'] ?? ''
    ],
    "last_month"    => $lastMonthData,
    "current_month" => $currentMonthData
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit();
