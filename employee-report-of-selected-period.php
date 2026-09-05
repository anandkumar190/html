<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

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

	
	
	
    include("connect.php");
    if(isset
	($_POST['reservation']))
     {
		$name="";$Period="";$totalCount=0;
	   $daterange=$_POST['reservation'];
       $dates=explode("-",$daterange);
	   $start=strtotime(trim($dates[0]));
	   $end=strtotime(trim($dates[1]));
	   $Period=date("m-d-Y",$start).' To '.date("m-d-Y",$end);
	   $start=date("Y-m-d",$start);
	   $end=date("Y-m-d",$end);
	   $employee=$_POST['employee'];



	//    $res=mysqli_query($con,"select e.name from employees e where e.usertype='1' and e.id='$employee'");
	   
	//    while($row=mysqli_fetch_array($res))
	//    {
	// 	  $name=$row["name"];
		
	//    }
$dsVisitList=[];
$dsVisitTimes=[];




			$visit = $con->prepare("
					SELECT 
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

			$visit->bind_param("iss", $employee, $start, $end);

			$visit->execute();

			$dsVisits = $visit->get_result();


			while ($dsVisit = $dsVisits->fetch_assoc()) {
				$vDate = $dsVisit['visit_date'];
				$vTime = $dsVisit['visit_time'];
				
				$visitInfo = [
					'distributor_name' => $dsVisit['distributor_name'],
					'city' => $dsVisit['distributor_city'],
					'time' => $vTime,
					'reason_type' => $dsVisit['reason_type'],
					'reason' => $dsVisit['reason']
				];
				
				$dsVisitList[$employee][$vDate][] = $visitInfo;
				
				// Track min and max time for attendance/working hours
				if (!isset($dsVisitTimes[$employee][$vDate])) {
					$dsVisitTimes[$employee][$vDate] = [
						'min' => $vTime,
						'max' => $vTime
					];
				} else {
					if ($vTime < $dsVisitTimes[$employee][$vDate]['min']) {
						$dsVisitTimes[$employee][$vDate]['min'] = $vTime;
					}
					if ($vTime > $dsVisitTimes[$employee][$vDate]['max']) {
						$dsVisitTimes[$employee][$vDate]['max'] = $vTime;
					}
				}
			}

			// Query Administrative Visits
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
				AND DATE(av.in_time) BETWEEN ? AND ?
				ORDER BY av.in_time ASC
			");
			$adminStmt->bind_param("iss", $employee, $start, $end);
			$adminStmt->execute();
			$adminVisitsRes = $adminStmt->get_result();

			while ($av = $adminVisitsRes->fetch_assoc()) {
				$vDate = $av['visit_date'];
				$adminVisitList[$employee][$vDate][] = $av;

				$inTs = !empty($av['in_time']) ? strtotime($av['in_time']) : 0;
				$outTs = !empty($av['out_time']) ? strtotime($av['out_time']) : $inTs;

				if (!isset($adminVisitTimes[$employee][$vDate])) {
					$adminVisitTimes[$employee][$vDate] = [
						'min' => $inTs,
						'max' => $outTs
					];
				} else {
					if ($inTs > 0 && ($adminVisitTimes[$employee][$vDate]['min'] == 0 || $inTs < $adminVisitTimes[$employee][$vDate]['min'])) {
						$adminVisitTimes[$employee][$vDate]['min'] = $inTs;
					}
					if ($outTs > 0 && ($adminVisitTimes[$employee][$vDate]['max'] == 0 || $outTs > $adminVisitTimes[$employee][$vDate]['max'])) {
						$adminVisitTimes[$employee][$vDate]['max'] = $outTs;
					}
				}
			}
			$adminStmt->close();

		$employeeName = '';

		$stmt = $con->prepare("
			SELECT name,usertype
			FROM employees
			WHERE id=?
			AND usertype IN (1,3)
			LIMIT 1
		");

		$stmt->bind_param("i", $employee);
		$stmt->execute();

		$result = $stmt->get_result();

		if ($row = $result->fetch_assoc()) {
			$employeeName = $row['name'];
		}

		$stmt->close();

		if (empty($employeeName)) {
			die("Employee Not Found");
		}

		$name=$employeeName;


	   $dates=getDatesFromRange($start,$end);
	   $totalCount=count($dates);
	   
	    
	   
			  $starttimearray=array();
			  $endtimearray=array();
			  $totalMinuts=0;
			  $totaldistance=0;
			  $totalold=0;
			  $totalnew=0;
			  $totalss=0;
			  $totaldistributor=0;
			  $productivOutlets=$totalothervisit=0;
			 $totalProductivePercentage= $totaloutletsNotVisited=0;
			  $totalProductivValueOrders=$totalallvisits=0;
			  $totalmilkbooth=0;
			  $totalmts=0;
			  $totalmtl=0;
			  $totalgt=0;
			  $totalhoreca=0;
			  $sunday=0;
			  $workingday=0;
			  $leave=0;
			  $rowData='';



		   	foreach ($dates as $dd) {
			$selectdate = date('Y-m-d', strtotime($dd));
			
			// Reset for each day
			$starttime = 0;
			$endtime = 0;
			$totalWorkingdays=$workingdays=$workinghours = 0;
			$workingTime = '-';
            $workingMinuts  = 0.0;

			$visitDetails = [];
			$outletActivities = [];
			$distance = 0;

			// Get outlet activity for the day
			$res = mysqli_query($con, "SELECT * FROM outletactivity WHERE userid='$employee' AND activitydate='$selectdate' ORDER BY id ASC");
			$count = mysqli_num_rows($res);

			for ($i = 0; $i < $count; $i++) {
				$row = mysqli_fetch_array($res);
				if ($i == 0) $starttime = $row["activitytime"];
				if ($i == $count - 1) $endtime = $row["activitytime"];
			}

			// Convert to timestamps
			$starttimeStamp = $starttime ? strtotime($starttime) : 0;
			$endtimeStamp = $endtime ? strtotime($endtime) : 0;

			// Check for distributor visit times on this date
			if (isset($dsVisitTimes[$employee][$selectdate])) {
				$dsMin = $dsVisitTimes[$employee][$selectdate]['min'];
				$dsMax = $dsVisitTimes[$employee][$selectdate]['max'];


				if (!empty($dsMin)) {
						$dsStart = strtotime($dsMin);

						if ($dsStart !== false && ($starttimeStamp == 0 || $dsStart < $starttimeStamp)) {
							$starttimeStamp = $dsStart;
						}
					}

					if (!empty($dsMax)) {
						$dsEnd = strtotime($dsMax);

						if ($dsEnd !== false && ($endtimeStamp == 0 || $dsEnd > $endtimeStamp)) {
							$endtimeStamp = $dsEnd;
						}
					}
			}

			// Check for admin visit times on this date
			if (isset($adminVisitTimes[$employee][$selectdate])) {
				$admMin = $adminVisitTimes[$employee][$selectdate]['min'];
				$admMax = $adminVisitTimes[$employee][$selectdate]['max'];

				if ($admMin > 0 && ($starttimeStamp == 0 || $admMin < $starttimeStamp)) {
					$starttimeStamp = $admMin;
				}
				if ($admMax > 0 && ($endtimeStamp == 0 || $admMax > $endtimeStamp)) {
					$endtimeStamp = $admMax;
				}
			}

			if ($starttimeStamp > 0 && $endtimeStamp > 0) {
				$diffSeconds = abs($endtimeStamp - $starttimeStamp);
				$hours       = floor($diffSeconds / 3600);
				$minutes     = floor(($diffSeconds % 3600) / 60);

				// Compact format (e.g. 4h 30m)
				$workingTime = sprintf('%dh %dm', $hours, $minutes);

				// Decimal for summing
				$workingMinuts = ($hours*60) + $minutes ;
			}

			// Fetch area details
				$areaQuery = "
				SELECT DISTINCT
					o.areaid,
					area.area AS areaName
				FROM outletactivity a
				JOIN outlets o ON a.outletid = o.id
				JOIN area ON o.areaid = area.id
				WHERE a.userid='$employee'
					AND a.activitydate='$selectdate'
					AND a.visittype='0'
				ORDER BY areaName ASC
				";
			$areaRes = mysqli_query($con, $areaQuery);
			$areas = [];

			while ($row = mysqli_fetch_array($areaRes)) {
				$area = $row["areaid"];
				$areaName = $row["areaName"];
				$areas[] = $area;

				// Queries per area
				$res1 = mysqli_query($con, "
					SELECT o.locality, o.name, o.outlettype, a.activitytype 
					FROM outletactivity a 
					JOIN outlets o ON a.outletid = o.id 
					WHERE a.userid = '$employee' AND a.activitydate = '$selectdate' AND a.visittype = '0' AND o.areaid = '$area'
				");
				while ($activityRow = mysqli_fetch_assoc($res1)) {
					$outletActivities[] = $activityRow;
				}

				// Total outlets
				$totalOutelate = mysqli_fetch_assoc(mysqli_query($con, "
					SELECT count(id) as total_outelate 
					FROM outlets 
					WHERE areaid='$area' AND DATE(creationdate) < '$selectdate'
				"))['total_outelate'];

				// New outlets
				$newTotalOutelate = mysqli_fetch_assoc(mysqli_query($con, "
					SELECT count(DISTINCT o.id) as new_total_outelate 
					FROM outletactivity a 
					JOIN outlets o ON a.outletid = o.id 
					WHERE a.userid='$employee' AND a.activitydate='$selectdate' AND a.activitytype='New Outlet Create' AND o.areaid='$area'
				"))['new_total_outelate'];

				// Visited outlets
				$totalVistingOutlate = mysqli_fetch_assoc(mysqli_query($con, "
					SELECT count(DISTINCT o.id) as total_visting_outlate 
					FROM outletactivity a 
					JOIN outlets o ON a.outletid = o.id 
					WHERE a.userid='$employee' AND a.activitydate='$selectdate' AND a.activitytype IN ('Outlet Visit', 'New Outlet Create') AND o.areaid='$area'
				"))['total_visting_outlate'];

				// Bookings per area
				$bRes = mysqli_fetch_assoc(mysqli_query($con, "
					SELECT count(DISTINCT b.outlet_id) as productive_outlets, sum(b.total_amount) as total_value_orders 
					FROM booking b
					INNER JOIN outlets o ON b.outlet_id = o.id
					WHERE b.user_id='$employee' AND DATE(b.booking_time)='$selectdate' AND o.areaid='$area'
				"));

				$newTotal = $totalOutelate + $newTotalOutelate;
				$notVisited = max(0, $newTotal - $totalVistingOutlate);
				$productivePct = ($newTotal > 0)
					? round(($bRes['productive_outlets'] / $newTotal) * 100, 2)
					: 0;

				$visitDetails[] = [
					'area_name'                 => $areaName,
					'total_outlets_on_route'    => (int)$totalOutelate,
					'new_outlet_made'           => (int)$newTotalOutelate,
					'new_total_oulets'          => (int)$newTotal,
					'No_of_outlets_visited'     => (int)$totalVistingOutlate,
					'productive_outlets'        => (int)($bRes['productive_outlets'] ?? 0),
					'outlets_not_visited'       => (int)$notVisited,
					'productive_percentage'     => $productivePct,
					'total_value_orders'        => (float)($bRes['total_value_orders'] ?? 0)
				];
			}

			// If no specific routes were visited, show single-row day summary
			if (empty($visitDetails)) {
				$visitDetails[] = [
					'area_name'                 => '',
					'total_outlets_on_route'    => 0,
					'new_outlet_made'           => 0,
					'new_total_oulets'          => 0,
					'No_of_outlets_visited'     => 0,
					'productive_outlets'        => 0,
					'outlets_not_visited'       => 0,
					'productive_percentage'     => 0,
					'total_value_orders'        => 0
				];
			}

			// Build HTML for the date row
			if (!empty($visitDetails)) {
				$rowData .= "<tr>";
				$rowData .= "<td class='col-nowrap'>";
				$isToday = ($selectdate == date('Y-m-d'));
				$formattedDate = date('d-M', strtotime($dd));
				if ($isToday) {
					$rowData .= "<a href='daly-report-os?id=" . urlencode($employee) . "'><strong>" . $formattedDate . "</strong></a>";
				} else {
					$rowData .= $formattedDate;
				}
				$rowData .= "</td>";
				
				$day = date('l', strtotime($dd));
				$dayShort = date('D', strtotime($dd));
				$rowData .= "<td class='col-nowrap'> $dayShort </td>";

				// Start time
				$rowData .= "<td class='col-nowrap'>";
				if ($starttimeStamp > 0) {
					$starttimearray[] = $starttimeStamp;
					$workingday++;
					$rowData .= date('h:i A', $starttimeStamp);
				} elseif (isset($dsVisitList[$employee][$selectdate])) {
					$workingday++;
					$rowData .= "<span class='label label-info' style='font-size:9.5px; padding:1px 3px;'>Dist Visit</span>";
				} elseif (isset($adminVisitList[$employee][$selectdate])) {
					$workingday++;
					$rowData .= "<span class='label label-primary' style='font-size:9.5px; padding:1px 3px;'>Admin Visit</span>";
				} else {
					if ($day != "Sunday") $leave++;
					$rowData .= "<span class='text-muted' style='font-size:10px;'>Leave</span>";
				}
				$rowData .= "</td>";

				// End time
				$rowData .= "<td class='col-nowrap'>";
				if ($endtimeStamp > 0) {
					$endtimearray[] = $endtimeStamp;
					$rowData .= date('h:i A', $endtimeStamp);
				} elseif (isset($dsVisitList[$employee][$selectdate])) {
					$rowData .= "<span class='label label-info' style='font-size:9.5px; padding:1px 3px;'>Dist Visit</span>";
				} elseif (isset($adminVisitList[$employee][$selectdate])) {
					$rowData .= "<span class='label label-primary' style='font-size:9.5px; padding:1px 3px;'>Admin Visit</span>";
				} else {
					$rowData .= "<span class='text-muted' style='font-size:10px;'>Leave</span>";
				}
				$rowData .= "</td>";

				$rowData .= "<td class='col-nowrap'>{$workingTime}</td>";

				// Sum using the decimal value
				$totalMinuts += $workingMinuts;

				// 1. Area Name(s)
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= (!empty($vv['area_name']) ? htmlspecialchars($vv['area_name']) : '-') . "<br>";
				}
				$rowData .= "</td>";

				// 2. Total Outlets on Route
				$rowData .= "<td class='col-num'>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['total_outlets_on_route'] . "<br>";
					$totalold += $vv['total_outlets_on_route'];
				}
				$rowData .= "</td>";

				// 3. New Outlet Made
				$rowData .= "<td class='col-num'>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['new_outlet_made'] . "<br>";
					$totalnew += $vv['new_outlet_made'];
				}
				$rowData .= "</td>";

				// 4. Total Outlets
				$rowData .= "<td class='col-num'>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['new_total_oulets'] . "<br>";
				}
				$rowData .= "</td>";

				// 5. Visited Outlets
				$rowData .= "<td class='col-num'>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['No_of_outlets_visited'] . "<br>";
					$totalothervisit += $vv['No_of_outlets_visited'];
				}
				$rowData .= "</td>";

				// 6. Productive Outlets
				$rowData .= "<td class='col-num'>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['productive_outlets'] . "<br>";
					$productivOutlets += $vv['productive_outlets'];
				}
				$rowData .= "</td>";

				// 7. Outlets Not Visited
				$rowData .= "<td class='col-num'>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['outlets_not_visited'] . "<br>";
					$totaloutletsNotVisited += $vv['outlets_not_visited'];
				}
				$rowData .= "</td>";

				// 8. Productive Percentage
				$rowData .= "<td class='col-num'>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['productive_percentage'] . "%<br>";
					$totalProductivePercentage += $vv['productive_percentage'];
				}
				$rowData .= "</td>";

				// 9. Total Order Value
				$rowData .= "<td class='col-num'>";
				foreach ($visitDetails as $vv) {
					$rowData .= ($vv['total_value_orders'] > 0 ? number_format($vv['total_value_orders'], 2) : '-') . "<br>";
					$totalProductivValueOrders += $vv['total_value_orders'];
				}
				$rowData .= "</td>";

				// 10. Distributor Visits
				$dsVistedStatus = "";
				if (isset($dsVisitList[$employee][$selectdate])) {
					$dsVistedStatus .= "<div class='compact-cell-item'>";
					foreach ($dsVisitList[$employee][$selectdate] as $index => $v) {
						if ($index > 0) {
							$dsVistedStatus .= "<hr style='margin:3px 0; border-color:#eee;'/>";
						}
						$formattedTime = $v['time'] ? date('h:i A', strtotime($v['time'])) : '';
						$dsVistedStatus .= "<b>" . htmlspecialchars($v['distributor_name'] ?? 'N/A') . "</b>";
						if (!empty($formattedTime)) {
							$dsVistedStatus .= " <span class='text-muted' style='font-size:9.5px;'>(" . htmlspecialchars($formattedTime) . ")</span>";
						}
						$purpose = trim(($v['reason_type'] ?: '') . ($v['reason'] ? ' - ' . $v['reason'] : ''));
						if (!empty($purpose)) {
							$dsVistedStatus .= "<div style='color:#555; font-size:9.5px;'>" . htmlspecialchars($purpose) . "</div>";
						}
					}
					$dsVistedStatus .= "</div>";
				}
				$rowData .= "<td>" . $dsVistedStatus . "</td>";

				// 11. Admin Visits
				$adminVisitStatus = "";
				if (isset($adminVisitList[$employee][$selectdate])) {
					$adminVisitStatus .= "<div class='compact-cell-item'>";
					foreach ($adminVisitList[$employee][$selectdate] as $aIdx => $av) {
						if ($aIdx > 0) {
							$adminVisitStatus .= "<hr style='margin:3px 0; border-color:#eee;'/>";
						}
						$catId = (int)$av['reason_category'];
						$inTimeFormatted = !empty($av['in_time']) ? date('h:i A', strtotime($av['in_time'])) : 'N/A';
						$outTimeFormatted = !empty($av['out_time']) ? date('h:i A', strtotime($av['out_time'])) : 'N/A';

						if ($catId === 1) {
							$prod = !empty($av['products_currently_distributed']) ? $av['products_currently_distributed'] : (!empty($av['areas_covered']) ? $av['areas_covered'] : '');
							$adminVisitStatus .= "<span class='label label-success' style='font-size:9px; padding:1px 3px;'>Search</span> <b>" . htmlspecialchars($av['company_name'] ?: 'N/A') . "</b>";
							if (!empty($av['city'])) {
								$adminVisitStatus .= " <span class='text-muted' style='font-size:9.5px;'>(" . htmlspecialchars($av['city']) . ")</span>";
							}
							if (!empty($prod)) {
								$adminVisitStatus .= "<div style='color:#555; font-size:9.5px;'><b>Prod:</b> " . htmlspecialchars($prod) . "</div>";
							}
							$adminVisitStatus .= "<div style='color:#777; font-size:9px;'>In: " . htmlspecialchars($inTimeFormatted) . " | Out: " . htmlspecialchars($outTimeFormatted) . "</div>";
						} elseif ($catId === 3) {
							$adminVisitStatus .= "<span class='label label-warning' style='font-size:9px; padding:1px 3px;'>Misc</span> <b>" . htmlspecialchars($av['company_name'] ?: 'N/A') . "</b>";
							if (!empty($av['city'])) {
								$adminVisitStatus .= " <span class='text-muted' style='font-size:9.5px;'>(" . htmlspecialchars($av['city']) . ")</span>";
							}
							if (!empty($av['visit_reason'])) {
								$adminVisitStatus .= "<div style='color:#555; font-size:9.5px;'><b>Reason:</b> " . htmlspecialchars($av['visit_reason']) . "</div>";
							}
							$adminVisitStatus .= "<div style='color:#777; font-size:9px;'>In: " . htmlspecialchars($inTimeFormatted) . " | Out: " . htmlspecialchars($outTimeFormatted) . "</div>";
						} else {
							$adminVisitStatus .= "<span class='label label-info' style='font-size:9px; padding:1px 3px;'>KYC</span> <b>" . htmlspecialchars($av['company_name'] ?: 'N/A') . "</b>";
							if (!empty($av['city'])) {
								$adminVisitStatus .= " <span class='text-muted' style='font-size:9.5px;'>(" . htmlspecialchars($av['city']) . ")</span>";
							}
							$adminVisitStatus .= "<div style='color:#777; font-size:9px;'>In: " . htmlspecialchars($inTimeFormatted) . " | Out: " . htmlspecialchars($outTimeFormatted) . "</div>";
						}
					}
					$adminVisitStatus .= "</div>";
				}
				$rowData .= "<td>" . $adminVisitStatus . "</td>";
				$rowData .= "</tr>";
			}
		}

		$totalstime = 0;
		$totaletime = 0;
		foreach ($starttimearray as $stime) {
			$totalstime += $stime;
		}
		foreach ($endtimearray as $etime) {
			$totaletime += $etime;
		}

		$avgTotalMinuts = ($totalMinuts > 0 && $workingday > 0) ? floor($totalMinuts / $workingday) : 0;
		$avgstarttime = (count($starttimearray) > 0 && $totalstime > 0) ? round($totalstime / count($starttimearray)) : 0;
		$avgendtime = (count($endtimearray) > 0 && $totaletime > 0) ? round($totaletime / count($endtimearray)) : 0;

		$avgHours = floor($avgTotalMinuts / 60);
		$avgTotalProductivePercentage = ($workingday > 0 && $totalProductivePercentage > 0) ? floor($totalProductivePercentage / $workingday) : 0;
		$avgMins = $avgTotalMinuts % 60;
		$avgWorkingTimeFormatted = ($workingday > 0 && ($avgHours > 0 || $avgMins > 0)) ? ($avgHours . 'h ' . $avgMins . 'm') : '-';
		$avgStartFormatted = ($avgstarttime > 0) ? date('h:i A', $avgstarttime) : '-';
		$avgEndFormatted = ($avgendtime > 0) ? date('h:i A', $avgendtime) : '-';

		$data = "<style>
		.compact-attendance-table {
			width: 100% !important;
			border-collapse: collapse !important;
			font-size: 11px !important;
			line-height: 1.25 !important;
			background: #fff;
		}
		.compact-attendance-table th, 
		.compact-attendance-table td {
			padding: 3px 4px !important;
			border: 1px solid #d2d6de !important;
			vertical-align: middle !important;
		}
		.compact-attendance-table th {
			background-color: #f4f6f9 !important;
			color: #333 !important;
			font-weight: 600 !important;
			text-align: center !important;
			white-space: nowrap !important;
		}
		.compact-attendance-table .col-nowrap {
			white-space: nowrap !important;
			text-align: center !important;
		}
		.compact-attendance-table .col-num {
			white-space: nowrap !important;
			text-align: right !important;
		}
		.compact-attendance-table tr:hover {
			background-color: #f9fbfd !important;
		}
		.compact-cell-item {
			font-size: 10px !important;
			line-height: 1.2 !important;
		}
		@media print {
			@page {
				size: A4 landscape;
				margin: 4mm;
			}
			body {
				margin: 0 !important;
				padding: 0 !important;
				font-size: 8pt !important;
			}
			.compact-attendance-table {
				font-size: 8pt !important;
			}
			.compact-attendance-table th, 
			.compact-attendance-table td {
				padding: 2px 2px !important;
				border: 1px solid #555 !important;
			}
			.compact-attendance-table th {
				background-color: #eee !important;
			}
		}
		</style>
		<table id='userstable' class='table table-bordered table-striped compact-attendance-table' data-processing='true' data-filtering='true' data-sorting='true'>
		<thead>
		  <tr style='background:#eef2f7;'>
			<th colspan='6' style='text-align:left; font-size:12px;'><strong>Employee Name:</strong> $name</th>
			<th colspan='10' style='text-align:right; font-size:12px;'><strong>Working Days:</strong> $workingday | <strong>Period:</strong> $Period</th>
		  </tr>
		  <tr>
			<th title='Date'>Date</th>
			<th title='Day'>Day</th>
			<th title='First Sales Call Time'>First Call</th>
			<th title='Last Sales Call Time'>Last Call</th>
			<th title='Working Time'>Work Hrs</th>
			<th title='Routes / Areas Visited'>Route / Area</th>
			<th title='Total Outlets on Route'>Route Outlets</th>
			<th title='New Outlets Made'>New Outlets</th>
			<th title='Total Outlets (Route + New)'>Total Outlets</th>
			<th title='Outlets Visited'>Visited</th>
			<th title='Productive Outlets'>Productive</th>
			<th title='Outlets Not Visited'>Unvisited</th>
			<th title='Productive Percentage'>Prod %</th>
			<th title='Total Value of Orders'>Order Val (₹)</th>
			<th title='Name of Distributors Visited'>Distributor Visits</th>
			<th title='Administrative Visits'>Admin Visits</th>
		  </tr>
		</thead>
		<tbody>
		$rowData
		</tbody>
		<tfoot>
		  <tr style='background:#f4f6f9; font-weight:bold;'>
			<th class='col-nowrap'>Averages</th>
			<th></th>
			<th class='col-nowrap'>$avgStartFormatted</th>
			<th class='col-nowrap'>$avgEndFormatted</th>
			<th class='col-nowrap'>$avgWorkingTimeFormatted</th>
			<th></th>
			<th class='col-num'>$totalold</th>
			<th class='col-num'>$totalnew</th>
			<th class='col-num'>" . ($totalold + $totalnew) . "</th>
			<th class='col-num'>$totalothervisit</th>
			<th class='col-num'>$productivOutlets</th>
			<th class='col-num'>$totaloutletsNotVisited</th>
			<th class='col-num'>$avgTotalProductivePercentage%</th>
			<th class='col-num'>" . ($totalProductivValueOrders > 0 ? number_format($totalProductivValueOrders, 2) : '0.00') . "</th>
			<th></th>
			<th></th>
		  </tr>
		</tfoot>
		</table>";


			
			// $data.="<table border='1' cellspacing='0' cellpadding='5'>";
			
			// $data.="<tr>
			//          <th></th><th></th>
			//        </tr>";
			// $data.="<tr>
			//          <th></th><th></th>
			//        </tr>";
			
			// $data.="<tr>
			//          <th>Total Working Days</th><th>".$workingday."</th>
			//        </tr>";
			// $data.="<tr>
			//          <th>Total Leave </th><th>".$leave."</th>
			//        </tr>";	   		  
			// $data.="<tr>
			//          <th>Total Sunday</th><th>".$sunday."</th>
			//        </tr>";
			// $data.="</table>";
			header('Content-type: application/excel');
			header("Content-Disposition: attachment; filename=$name Report.html");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo $data;

			exit();
       
   }
    ?>
