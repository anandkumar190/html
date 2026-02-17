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
    if(isset($_POST['reportdate']))
     {
	   
	   $reportdate=$_POST['reportdate'];
	   
	   $reportdate=date("Y-m-d",strtotime($reportdate));
	   $result=mysqli_query($con,"select e.id,e.name,e.empid,e.contact,d.name as 'designation' from employees e join designation d on e.designationid=d.id where e.usertype='1'");
	   $name="";$contact="";$designation="";
	   
	   
	   
	    
	   
       $data="<table id='userstable' border='1' cellpadding='10' cellspacing='0' class='table'  data-processing='true' data-filtering='true' data-sorting='true'>
           
              <tr>
                <th colspan='2'>Report Date :  </th><th colspan='7'>$reportdate</th><th colspan='10'>$name</th>
              </tr>
              <tr>
			   <th colspan='8'>Employee Daily Report : </th><th colspan='4'></th><th colspan='7'></th>
			  </tr>
			  
			  <tr>
			   <th colspan='8'>  </th><th colspan='4'></th> <th colspan='7'></th>
			  </tr>                                          
              <tr>
			   <th>Employee Name</th><th>Contact</th><th>Designation</th><th>First Sale Call Time</th><th>Last sale Call Time </th><th>Working Time (Hours)</th><th>Total Distance Covered</th><th>Area Visited</th><th>Visit Details</th><th>Old Store Visited</th><th>New Store Visited</th><th>Visit To Distr/SS</th> <th>Other Visits</th><th>Total Visit for Day</th><th>Milk Booth Visit</th><th>GT Visit</th><th>MTS Visit</th><th>MTL Visit</th><th>Horeca Visit </th>
			  </tr>";
			  $starttimearray=array();
			  $endtimearray=array();
			  $totalhours=0;
			  $totaldistance=0;
			  $totalold=0;
			  $totalnew=0;
			  $totalss=0;
			  $totaldistributor=0;
			  $totalothervisit=0;
			  $totalallvisits=0;
			  $totalmilkbooth=0;
			  $totalmts=0;
			  $totalmtl=0;
			  $totalgt=0;
			  $totalhoreca=0;
			  $sunday=0;
			  $workingday=0;
			  $leave=0;
			  
			   while($row=mysqli_fetch_array($result))
			  {
				     $empid=$row["id"];
				     $name=$row["name"]." - (".$row["empid"].")";
		             $designation=$row["designation"];
		             $contact=$row["contact"];
				     $res=mysqli_query($con,"select * from outletactivity where userid='$empid' and activitydate='$reportdate' order by id asc");
				  
				  $starttime=0;$endtime=0;
				  $count=mysqli_num_rows($res);
				  
				  for($i=0;$i<$count;$i++)
				  {
				    $row=mysqli_fetch_array($res);
				    if($i==0)
					{
					  $starttime=$row["activitytime"];  
					}
					if($i==$count-1)
					{
				      $endtime=$row["activitytime"];  
					}
				  }
				  //$res1=mysqli_query($con,"select * from outletactivity where userid='$employee' and activitydate='$selectdate' order by id desc limit 1");
				 // if($row1=mysqli_fetch_array($res1))
				 // {
				//	$endtime=$row1["activitytime"];  
				 // }
				  
                  $starttime = strtotime($starttime);
                  $endtime = strtotime($endtime);
                  
				  
				  $workinghours = round(abs($starttime - $endtime) / 3600,2);
                  $res1=mysqli_query($con,"select * from outletactivity where userid='$empid' and activitydate='$reportdate' order by id asc");          
				  $distance=0;
				  $prelat=0;
				  $prelng=0;
				  while($row=mysqli_fetch_array($res1))
				  {
					  if($prelat==0 || $prelng==0)
					  {
						  $prelat=$row["Latitude"];
						  $prelng=$row["Longitude"];
					  }
					  $lat=$row["Latitude"];
					  $lng=$row["Longitude"];
					  $distance+=distance($prelat,$prelng,$lat,$lng,'K');
					  $prelat=$lat;
					  $prelng=$lng;
				  }
				  $res=mysqli_query($con,"select distinct o.locality from outletactivity a join outlets o on a.outletid=o.id where a.userid='$empid' and a.activitydate='$reportdate' and a.visittype='0' order by a.id asc"); 
				  $areas=array();
				  $visitDetails=array();  
				  $new=0;$old=0;$milkbooth=0;$gt=0;$mts=0;$mtl=0;$horeca=0;
				  while($row=mysqli_fetch_array($res))
				  {
					$area=$row["locality"];
					array_push($areas,$area);  
					$res1=mysqli_query($con,"select o.locality,o.name,o.outlettype,a.activitytype from outletactivity a join outlets o on a.outletid=o.id where a.userid='$empid' and a.activitydate='$reportdate' and a.visittype='0' and o.locality='$area' order by a.id asc"); 
					$visits="";
					while($row1=mysqli_fetch_array($res1))
					{
						$actype=$row1["activitytype"];
						$octype=$row1["outlettype"];
						if($octype=="GT" || $octype=="G.T.")
						{
							$gt++;
						}
						if($octype=="Milk Booth")
						{
							$milkbooth++;
						}
						if($octype=="MTS" || $octype=="MT")
						{
							$mts++;
						}
						if($octype=="MTL")
						{
							$mtl++;
						}
						if($octype=="HORECA" || $octype=="Horeca")
						{
							$horeca++;
						}
						
						if($actype=="Outlet Visit" || $actype=="Visit")
						{
							$old++;
					        $visits.=$row1["name"]."( Outlet ".$row1["activitytype"].")".",";	
						}
						else 
						{
						    $new++;
						    $visits.=$row1["name"]."(".$row1["activitytype"].")".",";		
						}
					}
					array_push($visitDetails,$visits);
					
				  }
				  
				  
				  $res=mysqli_query($con,"select distinct a.area,a.id from outletactivity o join employees e on o.outletid=e.id join area a on e.areaid=a.id where o.userid='$empid' and o.activitydate='$reportdate' and (o.visittype='2' || o.visittype='3') order by o.id asc"); 
				  $stockist=0;$distributor=0;
				  while($row=mysqli_fetch_array($res))
				  {
					$area=$row["area"];
					$areaid=$row["id"];
					array_push($areas,$area);  
					$res1=mysqli_query($con,"select e.name,o.visittype,o.activitytype from outletactivity o join employees e on o.outletid=e.id where o.userid='$empid' and o.activitydate='$reportdate' and (o.visittype='2' || o.visittype='3') and e.area='$areaid' order by id o.asc"); 
					$visits="";
					while($row1=mysqli_fetch_array($res1))
					{
						$actype=$row1["activitytype"];
						$visittype=$row1["visittype"];
						if($visittype=="2")
						{
							$stockist++;
							$visits.=$row1["name"]."(".$row1["activitytype"].")".",";	
						}
						if($visittype=="3")
						{
							$distributor++;
							$visits.=$row1["name"]."(".$row1["activitytype"].")".",";
						}    
					}
					array_push($visitDetails,$visits);
					
				  }
				  
				  
				  $res=mysqli_query($con,"select distinct city from outletactivity  where userid='$empid' and activitydate='$reportdate' and visittype='4' order by id asc"); 
				  $othervisit=0;
				  while($row=mysqli_fetch_array($res))
				  {
					$area=$row["city"];
					
					array_push($areas,$area);  
					$res1=mysqli_query($con,"select companyname,visittype,activitytype from outletactivity  where userid='$empid' and activitydate='$reportdate' and visittype='4' and city='$area' order by id asc"); 
					$visits="";
					while($row1=mysqli_fetch_array($res1))
					{
						$actype=$row1["activitytype"];
						$visittype=$row1["visittype"];
						if($visittype=="4")
						{
							$othervisit++;
							$visits.=$row1["companyname"]."(".$row1["activitytype"].")".",";	
						}
						  
					}
					array_push($visitDetails,$visits);
					
				  }
				  
				  $totalhours+=$workinghours;
				  $totaldistance+=$distance;
				  $totalold+=$old;
				  $totalnew+=$new;
				  $totalss+=$stockist;
				  $totaldistributor+=$distributor;
				  $totalothervisit+=$othervisit;
				  $totalallvisits+=($totalold+$totalnew+$totalss+$totaldistributor+$totalothervisit);
				  $totalmilkbooth+=$milkbooth;
				  $totalgt+=$gt;
				  $totalmts+=$mts;
				  $totalmtl+=$mtl;
				  $totalhoreca+=$horeca;
				  
				$data.="<tr>"; 
				
				$data.="<td>";
				
					$data.=$name;
				
				$data.="</td>";
				$data.="<td>";
				
					$data.=$contact;
				
				$data.="</td>";
				
				$data.="<td>";
				
					$data.=$designation;
				
				$data.="</td>";
				
				$data.="<td>";
				if($starttime!=0)
				{
				  array_push($starttimearray,$starttime);
				  $workingday++;	
				  $starttime=date('h:i:s A', $starttime);
				  $data.=$starttime;
				}
				else
				{
				  if($day!="Sunday" )
			      $leave++;
				  
				  $data.="Leave";
				}
				$data.="</td>";
				
				$data.="<td>";
				if($endtime!=0)
				{
					
				  array_push($endtimearray,$endtime);	
				  $endtime=date('h:i:s A', $endtime);
				  $data.=$endtime;
				  
				}
				else
				{
					$data.="Leave";
				}
				$data.="</td>";
				
				$data.="<td>";
				$data.=$workinghours." Hrs";
				$data.="</td>";
				
				$data.="<td>";
				$data.=round($distance,2)." Km";
				$data.="</td>";
				
				$data.="<td><table border='1' cellspacing='0' cellpadding='5'>";
				foreach($areas as $vv)
				{
					$data.="<tr><td>$vv</td></tr>";
				}
			  
				$data.="</table></td>";
				
				$data.="<td><table border='1' cellspacing='0' cellpadding='5'>";
				foreach($visitDetails as $vv)
				{
					$data.="<tr><td>$vv</td></tr>";
				}
				$data.="</table></td>";
				
				$data.="<td>";
				$data.=$old;
				$data.="</td>";
				
				$data.="<td>";
				$data.=$new;
				$data.="</td>";
				
				$data.="<td>";
				$data.=($stockist+$distributor);
				$data.="</td>";
				
				
				$data.="<td>";
				$data.=($othervisit);
				$data.="</td>";
				
				$data.="<td>";
				$data.=($old+$new+$stockist+$distributor+$othervisit);
				$data.="</td>";
				
				$data.="<td>";
				$data.=$milkbooth;
				$data.="</td>";
				
				$data.="<td>";
				$data.=$gt;
				$data.="</td>";
				
				$data.="<td>";
				$data.=$mts;
				$data.="</td>";
				
				$data.="<td>";
				$data.=$mtl;
				$data.="</td>";
				
				$data.="<td>";
				$data.=$horeca;
				$data.="</td>";
				
				$data.="</tr>";
				  
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
			  
			  $avgstarttime=($totalstime/count($starttimearray));
			  $avgendtime=($totaletime/count($endtimearray));
			  
			  
	    $data.="<tr>
		          <th></th><th></th>
		         <th>Averages</th><th>".date('H:i:s',$avgstarttime)."</th><th>".date('H:i:s',$avgendtime)."</th><th>".round(($totalhours/$workingday),2)." Hrs</th><th>".round(($totaldistance/$workingday),2)." Km</th><th colspan='2'> Total </th><th>".$totalold."</th><th>".$totalnew."</th><th>".($totalss+$totaldistributor)."</th><th>".$totalothervisit."</th><th>".($totalold+$totalnew+$totalss+$totaldistributor+$totalothervisit)."</th><th>".$totalmilkbooth."</th><th>".$totalgt."</th><th>".$totalmts."</th><th>".$totalmtl."</th><th>".$totalhoreca."</th>
		       </tr>";		  
		$data.="</table>";
		
		
		$data.="<table border='1' cellspacing='0' cellpadding='5'>";
		
		$data.="<tr>
		         <th></th><th></th>
		       </tr>";
	    $data.="<tr>
		         <th></th><th></th>
		       </tr>";
		
		$data.="<tr>
		         <th>Total Working Days</th><th>".$workingday."</th>
		       </tr>";
		$data.="<tr>
		         <th>Total Leave </th><th>".$leave."</th>
		       </tr>";	   		  
		$data.="<tr>
		         <th>Total Sunday</th><th>".$sunday."</th>
		       </tr>";
		$data.="</table>";
		header('Content-type: application/excel');
        header("Content-Disposition: attachment; filename=$name Report.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
	    echo $data;
       
   }
    ?>
