<?php 
 include("../connect.php");
    function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
      
    // Declare an empty array 
    $array = array(); 
      
    // Variable that store the date interval 
    // of period 1 day 
    $interval = new DateInterval('P1D'); 
  
    $realEnd = new DateTime($end); 
    $realEnd->add($interval); 
  
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
  
    // Use loop to store date into array 
    foreach($period as $date) {                  
        $array[] = $date->format($format);  
    } 
  
    // Return the array elements 
    return $array; 
} 
    //date function close
	
	
function distance($lat1, $lon1, $lat2, $lon2, $unit) {
  if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    return 0;
  }
  else {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
      return ($miles * 1.609344);
    } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
      return $miles;
    }
  }
}



function exportEmployeeReport(mysqli $con): void
{
    // -------------------------------------------------
    // Current Date
    // -------------------------------------------------
    $reportDate = date('Y-m-d');

    $period = date('d-M-Y');

    // -------------------------------------------------
    // Fetch All Salesmen
    // -------------------------------------------------
    $employeeQuery = "
        SELECT id, name
        FROM employees
        WHERE usertype = 1
        ORDER BY name ASC
    ";

    $employeeResult = $con->query($employeeQuery);

    if (!$employeeResult || $employeeResult->num_rows == 0) {
        die('No employees found.');
    }

    // -------------------------------------------------
    // Outlet Counts By Area
    // -------------------------------------------------
    $outletResult = $con->query("
        SELECT
            areaid,
            COUNT(id) AS total_outlets
        FROM outlets
        GROUP BY areaid
    ");

    $outletsByArea = [];

    while ($outlet = $outletResult->fetch_assoc()) {

        $outletsByArea[$outlet['areaid']] =
            (int)$outlet['total_outlets'];
    }

    // -------------------------------------------------
    // Report Variables
    // -------------------------------------------------
    $rows = '';

    $grandTotalOutlets = 0;
    $grandTotalNewOutlets = 0;
    $grandTotalVisitedOutlets = 0;
    $grandTotalProductiveOutlets = 0;
    $grandTotalNotVisited = 0;
    $grandTotalOrderValue = 0;

    // -------------------------------------------------
    // Loop All Employees
    // -------------------------------------------------
    while ($employee = $employeeResult->fetch_assoc()) {

        $employeeId = (int)$employee['id'];
        $employeeName = $employee['name'];

        // -------------------------------------------------
        // Fetch Activities
        // -------------------------------------------------
        $activityStmt = $con->prepare("
            SELECT
                oa.activitydate,
                oa.activitytime,
                oa.activitytype,
                oa.outletid,
                oa.visittype,

                o.id AS outlet_id,
                o.name AS outlet_name,
                o.locality,
                o.areaid,
                o.outlettype,

                a.area AS area_name

            FROM outletactivity oa

            INNER JOIN outlets o
                ON oa.outletid = o.id

            INNER JOIN area a
                ON o.areaid = a.id

            WHERE oa.userid = ?
            AND oa.activitydate = ?

            ORDER BY oa.activitytime ASC
        ");

        $activityStmt->bind_param(
            "is",
            $employeeId,
            $reportDate
        );

        $activityStmt->execute();

        $activityResult = $activityStmt->get_result();

        $dayActivities = [];

        while ($activity = $activityResult->fetch_assoc()) {
            $dayActivities[] = $activity;
        }

        // -------------------------------------------------
        // Fetch Bookings
        // -------------------------------------------------
        $bookingStmt = $con->prepare("
            SELECT
                o.areaid,

                COUNT(DISTINCT b.outlet_id) AS productive_outlets,
                SUM(b.total_amount) AS total_order_value

            FROM booking b

            INNER JOIN outlets o
                ON b.outlet_id = o.id

            WHERE b.user_id = ?
            AND DATE(b.booking_time) = ?

            GROUP BY o.areaid
        ");

        $bookingStmt->bind_param(
            "is",
            $employeeId,
            $reportDate
        );

        $bookingStmt->execute();

        $bookingResult = $bookingStmt->get_result();

        $bookings = [];

        while ($booking = $bookingResult->fetch_assoc()) {

            $areaId = $booking['areaid'];

            $bookings[$areaId] = $booking;
        }

        // -------------------------------------------------
        // Day Name
        // -------------------------------------------------
        $dayName = date('l', strtotime($reportDate));

        // -------------------------------------------------
        // Start / End Time
        // -------------------------------------------------
        $startTime = '';
        $endTime = '';

        $workingMinutes = 0;

        if (!empty($dayActivities)) {

            $startTime = $dayActivities[0]['activitytime'];

            $endTime = end($dayActivities)['activitytime'];

            $startStamp = strtotime($startTime);

            $endStamp = strtotime($endTime);

            if ($startStamp && $endStamp) {

                $diff = abs($endStamp - $startStamp);

                $hours = floor($diff / 3600);

                $mins = floor(($diff % 3600) / 60);

                $workingMinutes = ($hours * 60) + $mins;
            }
        }

        // -------------------------------------------------
        // Area Calculations
        // -------------------------------------------------
        $areas = [];

        foreach ($dayActivities as $activity) {

            if ($activity['visittype'] != 0) {
                continue;
            }

            $areaId = $activity['areaid'];

            if (!isset($areas[$areaId])) {

                $areas[$areaId] = [
                    'area_name' => $activity['area_name'],
                    'visited' => [],
                    'new_outlets' => 0,
                ];
            }

            $areas[$areaId]['visited'][$activity['outletid']] = true;

            if (
                $activity['activitytype'] === 'New Outlet Create'
            ) {
                $areas[$areaId]['new_outlets']++;
            }
        }

        // -------------------------------------------------
        // Prepare Columns
        // -------------------------------------------------
        $routeNames = '';
        $routeOutletCounts = '';
        $newOutletCounts = '';
        $newTotals = '';
        $visitedCounts = '';
        $productiveCounts = '';
        $notVisitedCounts = '';
        $productivePercentages = '';
        $orderValues = '';

        $employeeTotalOutlets = 0;
        $employeeTotalNewOutlets = 0;
        $employeeTotalVisited = 0;
        $employeeTotalProductive = 0;
        $employeeTotalNotVisited = 0;
        $employeeTotalOrderValue = 0;

        foreach ($areas as $areaId => $areaData) {

            $routeName = $areaData['area_name'];

            $existingOutlets =
                $outletsByArea[$areaId] ?? 0;

            $newOutlets =
                $areaData['new_outlets'];

            $finalOutlets =
                $existingOutlets + $newOutlets;

            $visited =
                count($areaData['visited']);

            $booking =
                $bookings[$areaId] ?? [
                    'productive_outlets' => 0,
                    'total_order_value' => 0
                ];

            $productive =
                (int)$booking['productive_outlets'];

            $orderValue =
                (float)$booking['total_order_value'];

            $notVisited =
                max($finalOutlets - $visited, 0);

            $productivePercentage =
                $finalOutlets > 0
                    ? round(($productive / $finalOutlets) * 100, 2)
                    : 0;

            // Employee Totals
            $employeeTotalOutlets += $existingOutlets;
            $employeeTotalNewOutlets += $newOutlets;
            $employeeTotalVisited += $visited;
            $employeeTotalProductive += $productive;
            $employeeTotalNotVisited += $notVisited;
            $employeeTotalOrderValue += $orderValue;

            // Grand Totals
            $grandTotalOutlets += $existingOutlets;
            $grandTotalNewOutlets += $newOutlets;
            $grandTotalVisitedOutlets += $visited;
            $grandTotalProductiveOutlets += $productive;
            $grandTotalNotVisited += $notVisited;
            $grandTotalOrderValue += $orderValue;

            // Output
            $routeNames .= $routeName . '<br>';

            $routeOutletCounts .=
                $existingOutlets . '<br>';

            $newOutletCounts .=
                $newOutlets . '<br>';

            $newTotals .=
                $finalOutlets . '<br>';

            $visitedCounts .=
                $visited . '<br>';

            $productiveCounts .=
                $productive . '<br>';

            $notVisitedCounts .=
                $notVisited . '<br>';

            $productivePercentages .=
                $productivePercentage . "%<br>";

            $orderValues .=
                number_format($orderValue, 2) . '<br>';
        }

        // -------------------------------------------------
        // Working Time Text
        // -------------------------------------------------
        $workingHours = floor($workingMinutes / 60);

        $workingMins = $workingMinutes % 60;

        $workingTimeText =
            $workingMinutes > 0
                ? "{$workingHours} Hrs {$workingMins} Mins"
                : "Leave";

        // -------------------------------------------------
        // Append Row
        // -------------------------------------------------
        $rows .= "
        <tr>

            <td>{$employeeName}</td>

            <td>" . date('d-M-Y', strtotime($reportDate)) . "</td>

            <td>{$dayName}</td>

            <td>" .
                ($startTime
                    ? date('h:i:s A', strtotime($startTime))
                    : 'Leave') .
            "</td>

            <td>" .
                ($endTime
                    ? date('h:i:s A', strtotime($endTime))
                    : 'Leave') .
            "</td>

            <td>{$workingTimeText}</td>

            <td>{$routeNames}</td>

            <td>{$routeOutletCounts}</td>

            <td>{$newOutletCounts}</td>

            <td>{$newTotals}</td>

            <td>{$visitedCounts}</td>

            <td>{$productiveCounts}</td>

            <td>{$notVisitedCounts}</td>

            <td>{$productivePercentages}</td>

            <td>{$orderValues}</td>

            <td></td>

        </tr>
        ";
    }

    // -------------------------------------------------
    // Overall Productive %
    // -------------------------------------------------
    $overallProductivePercentage =
        $grandTotalOutlets > 0
            ? round(
                ($grandTotalProductiveOutlets / $grandTotalOutlets) * 100,
                2
            )
            : 0;

    // -------------------------------------------------
    // Generate HTML
    // -------------------------------------------------
    $html = "
    <table border='1' cellpadding='8' cellspacing='0'>

        <tr>
            <th colspan='16'>
                All Salesmen Daily Report
            </th>
        </tr>

        <tr>
            <th colspan='16'>
                Report Date : {$period}
            </th>
        </tr>

        <tr>
            <th>Employee</th>
            <th>Date</th>
            <th>Day</th>
            <th>First Sales Call Time</th>
            <th>Last Sales Call Time</th>
            <th>Working Time</th>
            <th>Routes Visited</th>
            <th>Total Outlets</th>
            <th>New Outlet Made</th>
            <th>Total Outlets After Add</th>
            <th>Visited Outlets</th>
            <th>Productive Outlets</th>
            <th>Not Visited</th>
            <th>Productive %</th>
            <th>Total Order Value</th>
            <th>Distributors</th>
        </tr>

        {$rows}

        <tr>

            <th colspan='7'>Grand Total</th>

            <th>{$grandTotalOutlets}</th>

            <th>{$grandTotalNewOutlets}</th>

            <th>" . ($grandTotalOutlets + $grandTotalNewOutlets) . "</th>

            <th>{$grandTotalVisitedOutlets}</th>

            <th>{$grandTotalProductiveOutlets}</th>

            <th>{$grandTotalNotVisited}</th>

            <th>{$overallProductivePercentage}%</th>

            <th>" . number_format($grandTotalOrderValue, 2) . "</th>

            <th></th>

        </tr>

    </table>
    ";

    // -------------------------------------------------
    // Export Excel
    // -------------------------------------------------
    header("Content-Type: application/vnd.ms-excel");

    header(
        "Content-Disposition: attachment; filename=\"All_Salesmen_Report_" .
        date('d_M_Y') .
        ".xls\""
    );

    header("Pragma: no-cache");
    header("Expires: 0");

    echo $html;

   // exit;
}




?>