<?php session_start();?>
<?php

if(!isset($_SESSION['tittu']))
{
	echo"invalid";
	exit();
}
$userid=$_SESSION['id'];
function truncate_number( $number, $precision = 2) {
    // Zero causes issues, and no need to truncate
    if ( 0 == (int)$number ) {
        return $number;
    }
    // Are we negative?
    $negative = $number / abs($number);
    // Cast the number to a positive to solve rounding
    $number = abs($number);
    // Calculate precision number for dividing / multiplying
    $precision = pow(10, $precision);
    // Run the math, re-applying the negative value to ensure returns correctly negative / positive
    return floor( $number * $precision ) / $precision * $negative;
    }

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



require("../connect.php");  
  
  $time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d");
  $userid=$_SESSION['id'];
  if(isset($_GET['show']))
  { 
	  $res=mysqli_query($con,"select r.id,r.routename,r.createdatetime,concat(e.name,' ',e.empid) as distid from route r left outer join employees e on r.distributorid=e.id"); 
		$response=array();
        while($row=mysqli_fetch_array($res))
        {
	      $rr=array();
	      $rr['id']=$row['id'];
	      $rr['route']=$row['routename'];
	      $rr['distid']=$row['distid'];
		  $rr['createdate']=$row['createdatetime'];
	      array_push($response,$rr);
        }
   echo json_encode($response);  
  }

  if(isset($_GET['edit']))
  {
     extract($_POST);
     mysqli_query($con,"update route set  routename='$route' where id='$id'") or die(mysqli_error($con));
      if(mysqli_affected_rows($con)>0)
      {
	     echo "success";
      }
      else
      {
	    echo "error".$id;
      }
  }  
  
   if(isset($_GET['add']))
  {
     extract($_POST);
     mysqli_query($con,"insert into route(routename,createdatetime,createdby) values('$route','$datetime','$userid')") or die(mysqli_error($con));
      if(mysqli_affected_rows($con)>0)
      {
	     echo "success";
      }
      else
      {
	    echo "error".$id;
      }
  }
  
  if(isset($_GET['delete']))
{
	$ids=$_POST['ids'];
	foreach($ids as $id)
	{
      mysqli_query($con,"delete from route where id='$id'");
	}
   echo "Routes Delete Succesfully...";
}  

if(isset($_GET['assigndist']))
{
	$ids=$_POST['ids'];
	$distid=$_POST['id'];
	foreach($ids as $id)
	{
      mysqli_query($con,"update route set distributorid='$distid' where id='$id'");
	}
   echo "Routes Assign Succesfully...";
}  
 
?>
