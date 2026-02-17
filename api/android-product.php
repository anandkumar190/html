<?php

  require('../connect.php');
  $time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d"); 
  
  
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

  
 

  if(isset($_GET['show']))
  {	
     
	 $res=mysqli_query($con,"select * from skus");
	 
	 $response=array();
	  
	 while($row=mysqli_fetch_array($res))
	 {
       $rr = array("id"=>$row["id"],"productname"=>$row["productname"],"productid"=>$row["productid"],"unit"=>$row["unit"],"mrp"=>$row["mrp"],"rate"=>$row["rate"],"image"=>$row["image"]);
		 $response[]=$rr;
     }
	   $data=array();
	   $data["data"]=$response;	 
	   echo json_encode($data); 
  }

?>