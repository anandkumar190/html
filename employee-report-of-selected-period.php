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
			$workingTime = '0 Hrs 0 Mins';
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

				// Human–readable
				$workingTime = sprintf('%d Hrs %d Mins', $hours, $minutes);

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
				$rowData .= "<td>";
				$isToday = ($selectdate == date('Y-m-d'));
				if ($isToday) {
					$rowData .= "<a href='daly-report-os?id=" . urlencode($employee) . "'>" . date('d-M-Y', strtotime($dd)) . "</a>";
				} else {
					$rowData .= date('d-M-Y', strtotime($dd));
				}
				$rowData .= "</td>";
				
				$day = date('l', strtotime($dd));
				$rowData .= "<td> $day </td>";

				// Start time
				$rowData .= "<td>";
				if ($starttimeStamp > 0) {
					$starttimearray[] = $starttimeStamp;
					$workingday++;
					$rowData .= date('h:i:s A', $starttimeStamp);
				} elseif (isset($dsVisitList[$employee][$selectdate])) {
					$workingday++;
					$rowData .= "Distributor Visit";
				} elseif (isset($adminVisitList[$employee][$selectdate])) {
					$workingday++;
					$rowData .= "Admin Visit";
				} else {
					if ($day != "Sunday") $leave++;
					$rowData .= "Leave";
				}
				$rowData .= "</td>";

				// End time
				$rowData .= "<td>";
				if ($endtimeStamp > 0) {
					$endtimearray[] = $endtimeStamp;
					$rowData .= date('h:i:s A', $endtimeStamp);
				} elseif (isset($dsVisitList[$employee][$selectdate])) {
					$rowData .= "Distributor Visit";
				} elseif (isset($adminVisitList[$employee][$selectdate])) {
					$rowData .= "Admin Visit";
				} else {
					$rowData .= "Leave";
				}
				$rowData .= "</td>";

				$rowData .= "<td>{$workingTime}</td>";

				// Sum using the decimal value
				$totalMinuts += $workingMinuts;
				// Visit details table inside a cell

				// 1. Area Name(s)
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['area_name'] . "<br>";
				}
				$rowData .= "  </td>";

				// 2. Total Outlets on Route
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['total_outlets_on_route'] . "<br>";
					$totalold+=$vv['total_outlets_on_route'];
				}
				$rowData .= " </td>";

				// 3. New Outlet Made
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['new_outlet_made'] . "<br>";
					$totalnew+=$vv['new_outlet_made'];
				}
				$rowData .= "  </td>";

				// 4. New Total Outlets
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['new_total_oulets'] . "<br>";
				}
				$rowData .= "  </td>";


					// 4. New Total Outlets
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['No_of_outlets_visited'] . "<br>";
					$totalothervisit+=$vv['No_of_outlets_visited'];
				}
				$rowData .= "  </td>";



				// 5. Productive Outlets
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['productive_outlets'] . "<br>";
					$productivOutlets+=$vv['productive_outlets'];
				}
				$rowData .= " </td>";



				
				// 6. Outlets Not Visited
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['outlets_not_visited'] . "<br>";
					$totaloutletsNotVisited+=$vv['outlets_not_visited'];
				}
				$rowData .= "  </td>";

				// 7. Productive Percentage
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['productive_percentage'] . "%<br>";
					$totalProductivePercentage+=$vv['productive_percentage'];
				}
				$rowData .= "</td>";

				// 8. Total Order Value
				$rowData .= "<td>";
				foreach ($visitDetails as $vv) {
					$rowData .= $vv['total_value_orders'] . "<br>";
					$totalProductivValueOrders+=$vv['total_value_orders'];
				}
				$rowData .= "</td>";
				$dsVistedStatus = "";
				if (isset($dsVisitList[$employee][$selectdate])) {
					$dsVistedStatus .= "<div style='font-size: 12px; line-height: 1.4; text-align: left;'>";
					foreach ($dsVisitList[$employee][$selectdate] as $index => $v) {
						if ($index > 0) {
							$dsVistedStatus .= "<hr style='margin: 6px 0; border-color: #ddd;'/>";
						}
						$formattedTime = $v['time'] ? date('h:i A', strtotime($v['time'])) : 'N/A';
						$dsVistedStatus .= "<strong>Distributor:</strong> " . htmlspecialchars($v['distributor_name'] ?? 'N/A') . "<br/>";
						$dsVistedStatus .= "<strong>Time:</strong> " . htmlspecialchars($formattedTime) . "<br/>";
						$dsVistedStatus .= "<strong>Purpose:</strong> " . htmlspecialchars(($v['reason_type'] ?: '') . ($v['reason'] ? ' - ' . $v['reason'] : ''));
					}
					$dsVistedStatus .= "</div>";
				}
							
				$rowData .= "<td>" .$dsVistedStatus. "</td>";

				$adminVisitStatus = "";
				if (isset($adminVisitList[$employee][$selectdate])) {
					$adminVisitStatus .= "<div style='font-size: 12px; line-height: 1.4; text-align: left;'>";
					foreach ($adminVisitList[$employee][$selectdate] as $aIdx => $av) {
						if ($aIdx > 0) {
							$adminVisitStatus .= "<hr style='margin: 6px 0; border-color: #ddd;'/>";
						}
						$catId = (int)$av['reason_category'];
						$inTimeFormatted = !empty($av['in_time']) ? date('h:i A', strtotime($av['in_time'])) : 'N/A';
						$outTimeFormatted = !empty($av['out_time']) ? date('h:i A', strtotime($av['out_time'])) : 'N/A';

						if ($catId === 1) {
							$prod = !empty($av['products_currently_distributed']) ? $av['products_currently_distributed'] : (!empty($av['areas_covered']) ? $av['areas_covered'] : 'N/A');
							$adminVisitStatus .= "<strong>New Distributor Search</strong><br/>";
							$adminVisitStatus .= "<strong>Company Name:</strong> " . htmlspecialchars($av['company_name'] ?: 'N/A') . "<br/>";
							$adminVisitStatus .= "<strong>City:</strong> " . htmlspecialchars($av['city'] ?: 'N/A') . "<br/>";
							$adminVisitStatus .= "<strong>Products Currently Distributed:</strong> " . htmlspecialchars($prod) . "<br/>";
							$adminVisitStatus .= "<strong>In Time :</strong> " . htmlspecialchars($inTimeFormatted) . "<br/>";
							$adminVisitStatus .= "<strong>Out Time :</strong> " . htmlspecialchars($outTimeFormatted);
						} elseif ($catId === 3) {
							$adminVisitStatus .= "<strong>Miscellaneous Visit</strong><br/>";
							$adminVisitStatus .= "<strong>Company Name:</strong> " . htmlspecialchars($av['company_name'] ?: 'N/A') . "<br/>";
							$adminVisitStatus .= "<strong>City:</strong> " . htmlspecialchars($av['city'] ?: 'N/A') . "<br/>";
							$adminVisitStatus .= "<strong>Reason for Visit:</strong> " . htmlspecialchars($av['visit_reason'] ?: 'N/A') . "<br/>";
							$adminVisitStatus .= "<strong>In Time :</strong> " . htmlspecialchars($inTimeFormatted) . "<br/>";
							$adminVisitStatus .= "<strong>Out Time :</strong> " . htmlspecialchars($outTimeFormatted);
						} else {
							$adminVisitStatus .= "<strong>New Distributor KYC</strong><br/>";
							$adminVisitStatus .= "<strong>Company Name:</strong> " . htmlspecialchars($av['company_name'] ?: 'N/A') . "<br/>";
							$adminVisitStatus .= "<strong>City:</strong> " . htmlspecialchars($av['city'] ?: 'N/A') . "<br/>";
							$adminVisitStatus .= "<strong>In Time :</strong> " . htmlspecialchars($inTimeFormatted) . "<br/>";
							$adminVisitStatus .= "<strong>Out Time :</strong> " . htmlspecialchars($outTimeFormatted);
						}
					}
					$adminVisitStatus .= "</div>";
				}
				$rowData .= "<td>" . $adminVisitStatus . "</td>";
				$rowData .= "</tr>";
			}
		}





			  $totalstime=0;
			  $totaletime=0;
			  foreach($starttimearray as $stime)
			  {
				  $totalstime+=$stime;
				  
			  }
			  
			  foreach($endtimearray as $etime)
			  {
				  $totaletime+=$etime;
				  
			  }
			  
			  $avgTotalMinuts=($totalMinuts>0 and $workingday >0)?floor($totalMinuts/$workingday):0;
			  $avgstarttime= (count($starttimearray)>0 and  $totalstime>0) ? round($totalstime/count($starttimearray)):0;
			  $avgendtime= (count($endtimearray)>0 and  $totaletime>0) ? round($totaletime/count($endtimearray)):0;
			  

       		$data="<table id='userstable' border='1' cellpadding='10' cellspacing='0' class='table'  data-processing='true' data-filtering='true' data-sorting='true'>
           
              <tr>
                <th colspan='6'>Employee Name : $name </th><th colspan='12'> Total Days Reported for Work : $workingday </th>
              </tr>
              <tr>
			   <th colspan='6'> Selected Period : $Period  </th> <th colspan='12'>  </th>
			  </tr>
		
			  <tr>
			   <th colspan='6'>  </th><th colspan='4'></th> <th colspan='8'></th>
			  </tr>                                          
              <tr>
			    <th>Date</th>
			    <th>Day</th>
			    <th>First Sales Call Time</th>
			    <th>Last Sales Call Time</th>
				<th>Working Time (Hrs.) </th>
			    <th>Routes Visited</th>
				<th>Total Outlets on Route</th>
			    <th>New Outlet Made</th>
			    <th>New Total Oulets</th>
			    <th>No. of Outlets Visited</th>
			    <th>Productive Outlets</th>
				<th>Outlets Not Visited</th>
				<th>Productive Call %</th>
				<th>Total Value of Orders</th>
				<th>Name of Distributors Visited</th>
				<th>Admin Visit</th>

			  </tr>";

			$data.=$rowData;
						
			$avgHours = floor($avgTotalMinuts / 60);
			$avgTotalProductivePercentage= ($workingday>0 and $totalProductivePercentage>0)?floor($totalProductivePercentage/$workingday):0;
			$avgMins = $avgTotalMinuts % 60;

	


			$data.="<tr>
					<th>Averages</th> <th> </th> 
					<th>".date('H:i:s',$avgstarttime)."</th>
					<th>".date('H:i:s',$avgendtime)."</th>
					<th>".$avgHours . " Hrs " . $avgMins . " Mins </th>
					<th>  </th> 
					<th>".$totalold."</th>
					<th>".$totalnew."</th>
					<th>".($totalold+$totalnew)."</th>
					<th>".$totalothervisit."</th>
					<th>".$productivOutlets."</th>
					<th>".$totaloutletsNotVisited."</th>
					<th>".$avgTotalProductivePercentage."%</th>
					<th>".$totalProductivValueOrders."</th>
					<th> </th>
					<th> </th>
				</tr>";		  
			$data.="</table>";


			
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
