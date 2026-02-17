<?php 

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
	   $res=mysqli_query($con,"select e.name from employees e where e.usertype='1' and e.id='$employee'");
	   
	   while($row=mysqli_fetch_array($res))
	   {
		  $name=$row["name"];
		
	   }
	   
	   $dates=getDatesFromRange($start,$end);
	   $totalCount=count($dates);
	   
	    
	   
			  $starttimearray=array();
			  $endtimearray=array();
			  $totalhours=0;
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
            $workingHoursDecimal  = 0.0;

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
			$starttimeStamp = strtotime($starttime);
			$endtimeStamp = strtotime($endtime);

			if ($starttimeStamp && $endtimeStamp) {
				$diffSeconds = abs($endtimeStamp - $starttimeStamp);
				$hours       = floor($diffSeconds / 3600);
				$minutes     = floor(($diffSeconds % 3600) / 60);

				// Human–readable
				$workingTime = sprintf('%d Hrs %d Mins', $hours, $minutes);

				// Decimal for summing
				$workingHoursDecimal = $hours + ($minutes / 60);
			}

			// Fetch area details
			$areaQuery = "
				SELECT o.areaid, area.area as areaName 
				FROM outletactivity a 
				JOIN outlets o ON a.outletid = o.id 
				JOIN area ON o.areaid = area.id 
				WHERE a.userid='$employee' AND a.activitydate='$selectdate' AND a.visittype='0' 
				GROUP BY o.areaid, area.area 
				ORDER BY a.id ASC
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
					SELECT COUNT(id) as total_outelate 
					FROM outlets 
					WHERE areaid = '$area' AND DATE(creationdate) < '$selectdate'
				")) ?: ['total_outelate' => 0];

				// New outlets
				$newTotalOutelate = mysqli_fetch_assoc(mysqli_query($con, "
					SELECT COUNT(DISTINCT o.id) as new_total_outelate 
					FROM outletactivity a 
					JOIN outlets o ON a.outletid = o.id 
					WHERE a.userid = '$employee' AND a.activitydate = '$selectdate' AND a.activitytype = 'New Outlet Create' AND o.areaid = '$area'
				")) ?: ['new_total_outelate' => 0];

				// Visited outlets
				$totalVistingOutlate = mysqli_fetch_assoc(mysqli_query($con, "
					SELECT COUNT(DISTINCT o.id) as total_visting_outlate 
					FROM outletactivity a 
					JOIN outlets o ON a.outletid = o.id 
					WHERE a.userid = '$employee' AND a.activitydate = '$selectdate' AND a.activitytype  IN ('Outlet Visit', 'New Outlet Create')  AND o.areaid = '$area'
				")) ?: ['total_visting_outlate' => 0];

				// Booking
				$booking = mysqli_fetch_assoc(mysqli_query($con, "
					SELECT COUNT( DISTINCT outlet_id) as productive_outlets, SUM(total_amount) as total_value_orders 
					FROM booking 
					WHERE user_id = '$employee' AND DATE(booking_time) = '$selectdate'
				")) ?: ['productive_outlets' => 0, 'total_value_orders' => 0];

				// Calculations
				$newTotalOulets = $totalOutelate['total_outelate'] + $newTotalOutelate['new_total_outelate'];
				$visitedOutlets = $totalVistingOutlate['total_visting_outlate'];
				$outletsNotVisited = ($newTotalOulets - $visitedOutlets);

				$productivePercentage = ($newTotalOulets > 0)
					? round(($booking['productive_outlets'] / $newTotalOulets) * 100)
					: 0.0;

				$visitDetails[$area] = [
					'area_name' => $areaName,
					'total_outlets_on_route' => $totalOutelate['total_outelate'],
					'new_outlet_made' => $newTotalOutelate['new_total_outelate'],
					'new_total_oulets' => $newTotalOulets,
					'No_of_outlets_visited' => $visitedOutlets,
					'productive_outlets' => $booking['productive_outlets'],
					'outlets_not_visited' => $outletsNotVisited,
					'productive_percentage' => $productivePercentage,
					'total_value_orders' => $booking['total_value_orders'],
				];
			}

			// Output table row
			$rowData .= "<tr>";
			$rowData .= "<td>";

			$day = date('l', strtotime($dd));
			if ($day == "Sunday") {
				$sunday++;
				$rowData .= date('d-M-Y', strtotime($dd)) . " " . $day;
			} else {
				$rowData .= date('d-M-Y', strtotime($dd));
			}
			$rowData .= "</td>";
			$rowData .= "<td> $day </td>";

			// Start time
			$rowData .= "<td>";
			if ($starttimeStamp > 0) {
				$starttimearray[] = $starttimeStamp;
				$workingday++;
				$rowData .= date('h:i:s A', $starttimeStamp);
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
			} else {
				$rowData .= "Leave";
			}
			$rowData .= "</td>";

			$rowData .= "<td>{$workingTime}</td>";

			// Sum using the decimal value
            $totalhours += $workingHoursDecimal;
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

			
			$rowData .= "<td></td>";
			$rowData .= "</tr>";
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
			  
			  $avgTotalhours=($totalhours>0 and $workingday >0)?($totalhours/$workingday):0;
			  $avgstarttime= (count($starttimearray)>0 and  $totalstime>0) ? ($totalstime/count($starttimearray)):0;
			  $avgendtime= (count($endtimearray)>0 and  $totaletime>0) ? ($totaletime/count($endtimearray)):0;
			  

       $data="<table id='userstable' border='1' cellpadding='10' cellspacing='0' class='table'  data-processing='true' data-filtering='true' data-sorting='true'>
           
              <tr>
                <th colspan='6'>Employee Name : $name </th><th colspan='11'> Total Days Reported for Work : $workingday </th>
              </tr>
              <tr>
			   <th colspan='6'> Selected Period : $Period  </th> <th colspan='11'>  </th>
			  </tr>
		
			  <tr>
			   <th colspan='6'>  </th><th colspan='4'></th> <th colspan='7'></th>
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

			  </tr>";

$data.=$rowData;
			  
	    $data.="<tr>
		         <th>Averages</th> <th> </th> 
				 <th>".date('H:i:s',$avgstarttime)."</th>
				 <th>".date('H:i:s',$avgendtime)."</th>
				 <th>".round(($avgTotalhours),2)." Hrs</th>
				 <th>  </th> 
				 <th>".$totalold."</th>
				 <th>".$totalnew."</th>
				 <th>".($totalold+$totalnew)."</th>
				 <th>".$totalothervisit."</th>
				 <th>".$productivOutlets."</th>
				 <th>".$totaloutletsNotVisited."</th>
				 <th>".$totalProductivePercentage."%</th>
				 <th>".$totalProductivValueOrders."</th>
				 <th></th>
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
