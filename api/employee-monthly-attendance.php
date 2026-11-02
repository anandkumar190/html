<?php
/**
 * API: Employee Monthly Attendance (Current Month & Last Month)
 * 
 * Request Method: GET or POST (JSON or Form Data)
 * Parameters:
 *   - user_id (or userid / employee_id) [Required]: ID of the employee
 *   - month / type [Optional]: 'current', 'last', 'both' (default: 'both')
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
 * Optimized with batch queries to eliminate N+1 query loop bottleneck.
 */
function calculateMonthAttendance($con, $employeeId, $startDate, $endDate, $monthLabel) {
    $startDateTime = $startDate . ' 00:00:00';
    $endDateTime   = $endDate . ' 23:59:59';

    // 1. Fetch all distributor visits in this range (Single query for entire month)
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

    // 1b. Fetch all administrative visits in this range (Index-friendly timestamp range query)
    $adminVisitList = [];
    $adminVisitTimes = [];

    $adminStmt = $con->prepare("
        SELECT 
            av.id,
            av.user_id,
            DATE(av.in_time) AS visit_date,
            av.in_time,
            av.out_time,
            av.company_name,
            av.city,
            av.address,
            av.pin_code,
            av.contact_person,
            av.cell_no,
            av.reason_category,
            av.visit_reason,
            av.status,
            sd.areas_covered,
            sd.products_currently_distributed,
            sd.gt_stores_covered,
            sd.mt_stores_covered,
            sd.wholesalers_covered,
            sd.horeca_covered
        FROM administrative_visit av
        LEFT JOIN new_distributor_search_details sd ON av.id = sd.administrative_visit_id
        WHERE av.user_id = ?
        AND av.in_time >= ? AND av.in_time <= ?
        ORDER BY av.in_time ASC
    ");

    $adminStmt->bind_param("iss", $employeeId, $startDateTime, $endDateTime);
    $adminStmt->execute();
    $adminVisitsRes = $adminStmt->get_result();

    $periodAdminTotal = 0;
    $periodAdminSearch = 0;
    $periodAdminKyc = 0;
    $periodAdminMisc = 0;

    while ($av = $adminVisitsRes->fetch_assoc()) {
        $vDate = $av['visit_date'];
        $adminVisitList[$vDate][] = $av;
        $periodAdminTotal++;

        $catId = (int)$av['reason_category'];
        if ($catId === 1) $periodAdminSearch++;
        elseif ($catId === 2) $periodAdminKyc++;
        elseif ($catId === 3) $periodAdminMisc++;

        $inTs = !empty($av['in_time']) ? strtotime($av['in_time']) : 0;
        $outTs = !empty($av['out_time']) ? strtotime($av['out_time']) : $inTs;

        if (!isset($adminVisitTimes[$vDate])) {
            $adminVisitTimes[$vDate] = [
                'min' => $inTs,
                'max' => $outTs
            ];
        } else {
            if ($inTs > 0 && ($adminVisitTimes[$vDate]['min'] == 0 || $inTs < $adminVisitTimes[$vDate]['min'])) {
                $adminVisitTimes[$vDate]['min'] = $inTs;
            }
            if ($outTs > 0 && ($adminVisitTimes[$vDate]['max'] == 0 || $outTs > $adminVisitTimes[$vDate]['max'])) {
                $adminVisitTimes[$vDate]['max'] = $outTs;
            }
        }
    }
    $adminStmt->close();

    // 2. Batch fetch daily activity start/end times for entire period in 1 query
    $dailyActivityTimes = [];
    $actStmt = $con->prepare("
        SELECT activitydate, activitytime 
        FROM outletactivity 
        WHERE userid = ? AND activitydate BETWEEN ? AND ? 
        ORDER BY id ASC
    ");
    $actStmt->bind_param("iss", $employeeId, $startDate, $endDate);
    $actStmt->execute();
    $actRes = $actStmt->get_result();
    while ($row = $actRes->fetch_assoc()) {
        $d = $row['activitydate'];
        if (!isset($dailyActivityTimes[$d])) {
            $dailyActivityTimes[$d] = [
                'start' => $row['activitytime'],
                'end'   => $row['activitytime']
            ];
        } else {
            $dailyActivityTimes[$d]['end'] = $row['activitytime'];
        }
    }
    $actStmt->close();

    // 3. Batch fetch routes/areas visited across entire period in 1 query
    $areasByDate = [];
    $allAreaIds = [];
    $routeStmt = $con->prepare("
        SELECT DISTINCT
            a.activitydate,
            o.areaid,
            area.area AS areaName
        FROM outletactivity a
        JOIN outlets o ON a.outletid = o.id
        JOIN area ON o.areaid = area.id
        WHERE a.userid = ?
            AND a.activitydate BETWEEN ? AND ?
            AND a.visittype = '0'
        ORDER BY a.activitydate ASC, areaName ASC
    ");
    $routeStmt->bind_param("iss", $employeeId, $startDate, $endDate);
    $routeStmt->execute();
    $routeRes = $routeStmt->get_result();
    while ($row = $routeRes->fetch_assoc()) {
        $d = $row['activitydate'];
        $aid = (int)$row['areaid'];
        $areasByDate[$d][] = [
            'areaid'   => $aid,
            'areaName' => $row['areaName']
        ];
        $allAreaIds[$aid] = true;
    }
    $routeStmt->close();

    // 4. Batch fetch area bookings across entire period in 1 index-friendly query
    $areaBookings = [];
    $bStmt = $con->prepare("
        SELECT 
            DATE(b.booking_time) AS b_date,
            o.areaid,
            COUNT(DISTINCT b.outlet_id) AS productive_outlets, 
            SUM(b.total_amount) AS total_value_orders 
        FROM booking b
        INNER JOIN outlets o ON b.outlet_id = o.id
        WHERE b.user_id = ? 
          AND b.booking_time >= ? AND b.booking_time <= ?
        GROUP BY DATE(b.booking_time), o.areaid
    ");
    $bStmt->bind_param("iss", $employeeId, $startDateTime, $endDateTime);
    $bStmt->execute();
    $bRes = $bStmt->get_result();
    while ($row = $bRes->fetch_assoc()) {
        $areaBookings[$row['b_date']][$row['areaid']] = [
            'productive_outlets' => (int)($row['productive_outlets'] ?? 0),
            'total_value_orders' => (float)($row['total_value_orders'] ?? 0)
        ];
    }
    $bStmt->close();

    // 5. Batch fetch global daily bookings across entire period in 1 index-friendly query
    $globalBookings = [];
    $gbStmt = $con->prepare("
        SELECT 
            DATE(booking_time) AS b_date,
            COUNT(DISTINCT outlet_id) AS productive_outlets, 
            SUM(total_amount) AS total_value_orders 
        FROM booking 
        WHERE user_id = ? 
          AND booking_time >= ? AND booking_time <= ?
        GROUP BY DATE(booking_time)
    ");
    $gbStmt->bind_param("iss", $employeeId, $startDateTime, $endDateTime);
    $gbStmt->execute();
    $gbRes = $gbStmt->get_result();
    while ($row = $gbRes->fetch_assoc()) {
        $globalBookings[$row['b_date']] = [
            'productive_outlets' => (int)($row['productive_outlets'] ?? 0),
            'total_value_orders' => (float)($row['total_value_orders'] ?? 0)
        ];
    }
    $gbStmt->close();

    // 6. Batch fetch new outlets created and visited outlets counts by (date, areaid) in 1 query
    $areaActivityStats = [];
    $actStatStmt = $con->prepare("
        SELECT 
            a.activitydate,
            o.areaid,
            COUNT(DISTINCT CASE WHEN a.activitytype = 'New Outlet Create' THEN o.id END) AS new_outlets_count,
            COUNT(DISTINCT CASE WHEN a.activitytype IN ('Outlet Visit', 'New Outlet Create') THEN o.id END) AS visited_outlets_count
        FROM outletactivity a 
        JOIN outlets o ON a.outletid = o.id 
        WHERE a.userid = ? 
          AND a.activitydate BETWEEN ? AND ?
          AND a.activitytype IN ('Outlet Visit', 'New Outlet Create')
        GROUP BY a.activitydate, o.areaid
    ");
    $actStatStmt->bind_param("iss", $employeeId, $startDate, $endDate);
    $actStatStmt->execute();
    $actStatRes = $actStatStmt->get_result();
    while ($row = $actStatRes->fetch_assoc()) {
        $areaActivityStats[$row['activitydate']][$row['areaid']] = [
            'new'     => (int)$row['new_outlets_count'],
            'visited' => (int)$row['visited_outlets_count']
        ];
    }
    $actStatStmt->close();

    // 7. Batch precompute cumulative total outlets on route for all visited areas
    $outletsPriorCount = [];
    $outletsDailyCreation = [];

    if (!empty($allAreaIds)) {
        $areaIdList = implode(',', array_map('intval', array_keys($allAreaIds)));

        // Prior to start date
        $priorRes = mysqli_query($con, "
            SELECT areaid, COUNT(id) AS cnt 
            FROM outlets 
            WHERE areaid IN ($areaIdList) AND creationdate < '$startDateTime'
            GROUP BY areaid
        ");
        if ($priorRes) {
            while ($pRow = mysqli_fetch_assoc($priorRes)) {
                $outletsPriorCount[(int)$pRow['areaid']] = (int)$pRow['cnt'];
            }
        }

        // Daily creations during the month
        $dailyRes = mysqli_query($con, "
            SELECT areaid, DATE(creationdate) AS c_date, COUNT(id) AS cnt 
            FROM outlets 
            WHERE areaid IN ($areaIdList) 
              AND creationdate >= '$startDateTime' 
              AND creationdate <= '$endDateTime'
            GROUP BY areaid, DATE(creationdate)
        ");
        if ($dailyRes) {
            while ($dRow = mysqli_fetch_assoc($dailyRes)) {
                $outletsDailyCreation[(int)$dRow['areaid']][$dRow['c_date']] = (int)$dRow['cnt'];
            }
        }
    }

    $dates = getDatesFromRange($startDate, $endDate);
    $totalDaysCount = count($dates);

    $cumulativeOutletsByArea = [];
    foreach ($allAreaIds as $aid => $_) {
        $cumulativeOutletsByArea[$aid] = $outletsPriorCount[$aid] ?? 0;
    }

    $outletsOnRouteByDateArea = [];
    foreach ($dates as $dd) {
        $d = date('Y-m-d', strtotime($dd));
        foreach ($allAreaIds as $aid => $_) {
            $outletsOnRouteByDateArea[$d][$aid] = $cumulativeOutletsByArea[$aid];
            if (isset($outletsDailyCreation[$aid][$d])) {
                $cumulativeOutletsByArea[$aid] += $outletsDailyCreation[$aid][$d];
            }
        }
    }

    // 8. Iterate dates in PHP memory (ZERO database round-trips inside loop!)
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

        if (isset($dailyActivityTimes[$selectdate])) {
            $starttime = $dailyActivityTimes[$selectdate]['start'];
            $endtime   = $dailyActivityTimes[$selectdate]['end'];
        }

        $starttimeStamp = !empty($starttime) ? strtotime("$selectdate $starttime") : 0;
        $endtimeStamp = !empty($endtime) ? strtotime("$selectdate $endtime") : 0;

        // Check for distributor visit timings on this date
        if (isset($dsVisitTimes[$selectdate])) {
            $dsMin = $dsVisitTimes[$selectdate]['min'];
            $dsMax = $dsVisitTimes[$selectdate]['max'];

            if (!empty($dsMin)) {
                $dsStart = strtotime("$selectdate $dsMin");
                if ($dsStart !== false && ($starttimeStamp === 0 || $dsStart < $starttimeStamp)) {
                    $starttimeStamp = $dsStart;
                }
            }

            if (!empty($dsMax)) {
                $dsEnd = strtotime("$selectdate $dsMax");
                if ($dsEnd !== false && ($endtimeStamp === 0 || $dsEnd > $endtimeStamp)) {
                    $endtimeStamp = $dsEnd;
                }
            }
        }

        // Check for administrative visit timings on this date
        if (isset($adminVisitTimes[$selectdate])) {
            $admMinTs = $adminVisitTimes[$selectdate]['min'];
            $admMaxTs = $adminVisitTimes[$selectdate]['max'];

            $admStart = ($admMinTs > 0) ? strtotime("$selectdate " . date('H:i:s', $admMinTs)) : 0;
            $admEnd   = ($admMaxTs > 0) ? strtotime("$selectdate " . date('H:i:s', $admMaxTs)) : 0;

            if ($admStart > 0 && ($starttimeStamp === 0 || $admStart < $starttimeStamp)) {
                $starttimeStamp = $admStart;
            }
            if ($admEnd > 0 && ($endtimeStamp === 0 || $admEnd > $endtimeStamp)) {
                $endtimeStamp = $admEnd;
            }
        }

        // Calculate working hours
        if ($starttimeStamp > 0 && $endtimeStamp > 0 && $endtimeStamp >= $starttimeStamp) {
            $diffSeconds = $endtimeStamp - $starttimeStamp;
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
            $starttimearray[] = strtotime("1970-01-01 " . date('H:i:s', $starttimeStamp));
            $workingDaysCount++;
            $firstCallFormatted = date('h:i:s A', $starttimeStamp);

            if ($endtimeStamp > 0) {
                $endtimearray[] = strtotime("1970-01-01 " . date('H:i:s', $endtimeStamp));
                $lastCallFormatted = date('h:i:s A', $endtimeStamp);
            }
            $dayStatus = 'Present';
        } elseif (isset($dsVisitList[$selectdate])) {
            $workingDaysCount++;
            $dayStatus = 'Distributor Visit';
            $firstCallFormatted = 'Distributor Visit';
            $lastCallFormatted = 'Distributor Visit';
        } elseif (isset($adminVisitList[$selectdate])) {
            $workingDaysCount++;
            $dayStatus = 'Admin Visit';
            $firstCallFormatted = 'Admin Visit';
            $lastCallFormatted = 'Admin Visit';
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

        // Route details & bookings for the day
        $routesVisited = [];
        $dayOutletsOnRoute = 0;
        $dayNewOutlets = 0;
        $dayVisitedOutlets = 0;
        $dayProductiveOutlets = 0;
        $dayNotVisitedOutlets = 0;
        $dayOrderValue = 0.0;

        $bookingsByArea = $areaBookings[$selectdate] ?? [];
        $globalDailyBooking = $globalBookings[$selectdate] ?? ['productive_outlets' => 0, 'total_value_orders' => 0];

        $dayRoutes = $areasByDate[$selectdate] ?? [];
        foreach ($dayRoutes as $areaRow) {
            $areaId = $areaRow["areaid"];
            $areaName = $areaRow["areaName"];

            $totalOutletsOnRoute = $outletsOnRouteByDateArea[$selectdate][$areaId] ?? 0;
            $newOutletMade = $areaActivityStats[$selectdate][$areaId]['new'] ?? 0;
            $visitedOutlets = $areaActivityStats[$selectdate][$areaId]['visited'] ?? 0;

            $areaBooking = $bookingsByArea[$areaId] ?? [
                'productive_outlets' => 0,
                'total_value_orders' => 0.0
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

        // Build Administrative Visits for this day
        $dayAdminVisits = [];
        $dayAdminSummary = [];

        if (isset($adminVisitList[$selectdate])) {
            foreach ($adminVisitList[$selectdate] as $av) {
                $catId = (int)$av['reason_category'];
                $inTimeFmt = !empty($av['in_time']) ? date('h:i A', strtotime($av['in_time'])) : 'N/A';
                $outTimeFmt = !empty($av['out_time']) ? date('h:i A', strtotime($av['out_time'])) : 'N/A';

                if ($catId === 1) {
                    $prod = !empty($av['products_currently_distributed']) 
                        ? $av['products_currently_distributed'] 
                        : (!empty($av['areas_covered']) ? $av['areas_covered'] : 'N/A');

                    $outputLine = 'New Distributor Search - "' . $av['company_name'] . '" "' . $av['city'] . '" "' . $prod . '" "In Time : ' . $inTimeFmt . '" "Out Time : ' . $outTimeFmt . '"';

                    $dayAdminVisits[] = [
                        'id'                             => (int)$av['id'],
                        'visit_type'                     => 'New Distributor Search',
                        'reason_category'                => 1,
                        'company_name'                   => $av['company_name'],
                        'city'                           => $av['city'],
                        'products_currently_distributed' => $prod,
                        'areas_covered'                  => $av['areas_covered'] ?? '',
                        'in_time'                        => $av['in_time'],
                        'in_time_formatted'              => $inTimeFmt,
                        'out_time'                       => $av['out_time'],
                        'out_time_formatted'             => $outTimeFmt,
                        'gt_stores_covered'              => (int)($av['gt_stores_covered'] ?? 0),
                        'mt_stores_covered'              => (int)($av['mt_stores_covered'] ?? 0),
                        'wholesalers_covered'            => (int)($av['wholesalers_covered'] ?? 0),
                        'horeca_covered'                 => (int)($av['horeca_covered'] ?? 0),
                        'output_text'                    => $outputLine
                    ];
                    $dayAdminSummary[] = $outputLine;
                } elseif ($catId === 3) {
                    $reason = !empty($av['visit_reason']) ? $av['visit_reason'] : 'N/A';
                    $outputLine = 'Miscellaneous Visit - "' . $av['company_name'] . '" "' . $av['city'] . '" "Reason for Visit: ' . $reason . '" "In Time : ' . $inTimeFmt . '" "Out Time : ' . $outTimeFmt . '"';

                    $dayAdminVisits[] = [
                        'id'                 => (int)$av['id'],
                        'visit_type'         => 'Miscellaneous Visit',
                        'reason_category'    => 3,
                        'company_name'       => $av['company_name'],
                        'city'               => $av['city'],
                        'reason_for_visit'   => $reason,
                        'visit_reason'       => $reason,
                        'in_time'            => $av['in_time'],
                        'in_time_formatted'  => $inTimeFmt,
                        'out_time'           => $av['out_time'],
                        'out_time_formatted' => $outTimeFmt,
                        'output_text'        => $outputLine
                    ];
                    $dayAdminSummary[] = $outputLine;
                } else {
                    $outputLine = 'New Distributor KYC - "' . $av['company_name'] . '" "' . $av['city'] . '" "In Time : ' . $inTimeFmt . '" "Out Time : ' . $outTimeFmt . '"';

                    $dayAdminVisits[] = [
                        'id'                 => (int)$av['id'],
                        'visit_type'         => 'New Distributor KYC',
                        'reason_category'    => 2,
                        'company_name'       => $av['company_name'],
                        'city'               => $av['city'],
                        'in_time'            => $av['in_time'],
                        'in_time_formatted'  => $inTimeFmt,
                        'out_time'           => $av['out_time'],
                        'out_time_formatted' => $outTimeFmt,
                        'output_text'        => $outputLine
                    ];
                    $dayAdminSummary[] = $outputLine;
                }
            }
        }

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
            'admin_visits'       => $dayAdminVisits,
            'admin_visit_output' => !empty($dayAdminSummary) ? implode(' ~ ', $dayAdminSummary) : '',
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

    // 9. Compute Averages & Summary
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
            'total_order_value'           => round($totalOrderValue, 2),
            'total_admin_visits'          => $periodAdminTotal,
            'admin_new_search_visits'     => $periodAdminSearch,
            'admin_kyc_visits'            => $periodAdminKyc,
            'admin_misc_visits'           => $periodAdminMisc
        ],
        'attendance_records' => $dailyRecords
    ];
}

// -------------------------------------------------------------
// Calculate Date Ranges for Last Month and Current Month
// -------------------------------------------------------------

// Optional 'month' or 'type' parameter: 'current', 'last', 'both' (default: 'both')
$monthParam = strtolower(trim($_POST['month'] ?? $_GET['month'] ?? $_POST['type'] ?? $_GET['type'] ?? 'both'));
if (empty($monthParam)) {
    $monthParam = 'both';
}

$includeLastMonth    = ($monthParam === 'both' || $monthParam === 'all' || $monthParam === 'last');
$includeCurrentMonth = ($monthParam === 'both' || $monthParam === 'all' || $monthParam === 'current');

$lastMonthData = null;
$currentMonthData = null;

if ($includeLastMonth) {
    // Last Month Range: 1st of last month to Last day of last month
    $lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
    $lastMonthEnd   = date('Y-m-t', strtotime('last day of last month'));
    $lastMonthLabel = date('F Y', strtotime('first day of last month'));
    $lastMonthData  = calculateMonthAttendance($con, $userId, $lastMonthStart, $lastMonthEnd, $lastMonthLabel);
}

if ($includeCurrentMonth) {
    // Current Month Range: 1st of current month to Today (or full month)
    $currentMonthStart = date('Y-m-01');
    $currentMonthEnd   = date('Y-m-d'); // up to today
    $currentMonthLabel = date('F Y');
    $currentMonthData  = calculateMonthAttendance($con, $userId, $currentMonthStart, $currentMonthEnd, $currentMonthLabel);
}

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
