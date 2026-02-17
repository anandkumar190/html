
<?php

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
  
  if(isset($_GET['register']))
  {
     extract($_POST);
      
	  $lat=truncate_number($latitude,1);
	  $lng=truncate_number($longitude,1);
	  
	  $res=mysqli_query($con,"select * from area where country='$country' and state='$state' and region='$region' and truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng'");
	 
	  while($row=mysqli_fetch_array($res))
	  {
		 $dis=distance($latitude,$longitude,$row[5],$row[6],'K');
		 if($dis<=$row[7])
		 {
			 echo $row[4]." Area Already Registered in ".$row[7]." KM radius and You cannot Register Other Area with in $row[7] Km Radius";
			 return;
		 }
	  }
     mysqli_query($con,"insert into area(country,state,region,area,latitude,longitude,km,registrationdate,createdby) values('$country','$state','$region','$area','$latitude','$longitude','$km','$datetime','$userid')") or die(mysqli_error($con));
      if(mysqli_affected_rows($con)>0)
      {
	     echo "success";
      }
      else
      {
	    echo "error";
      }
  }



  if(isset($_GET['edit']))
  {
     extract($_POST);
      
	  $lat=truncate_number($latitude,1);
	  $lng=truncate_number($longitude,1);
	  
	  $res=mysqli_query($con,"select * from area where country='$country' and state='$state' and region='$region' and truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng' and id!='$id'");
	 
	  while($row=mysqli_fetch_array($res))
	  {
		 $dis=distance($latitude,$longitude,$row[5],$row[6],'K');
		 if($dis<=$row[7])
		 {
			 echo $row[4]." Area Already Registered in ".$row[7]." KM radius and You cannot Register Other Area with in $row[7] Km Radius";
			 return;
		 }
	  }
     mysqli_query($con,"update  area set  country='$country',state='$state',region='$region',area='$area',latitude='$latitude',longitude='$longitude',Km='$km', registrationdate='$datetime',createdby='$userid' where id='$id'") or die(mysqli_error($con));
      if(mysqli_affected_rows($con)>0)
      {
	     echo "success";
      }
      else
      {
	    echo "error".$id;
      }
  }
 
 
  
  if(isset($_GET['check']))
  {
     
	  extract($_POST);
	  
	  $lat=truncate_number($latitude,1);
	  $lng=truncate_number($longitude,1);
	  
	  $res=mysqli_query($con,"select * from area where country='$country' and state='$state' and region='$region' and truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng'");
	  
	  while($row=mysqli_fetch_array($res))
	  {
         $km=distance($latitude, $longitude, $row[5], $row[6], "K");	
	     
		 if($km<=$row[7])
		 {
		   echo json_encode($row);
		   return;	 
		 }
	  }
	  
	  
	  echo "notregister";
	 
  }
  
   if(isset($_GET['getarea']))
  {
	  extract($_POST);
	  
	  //$lat=truncate_number($latitude,0);
	  //$lng=truncate_number($longitude,0);
	  $response=array();
	  //$res=mysqli_query($con,"select * from area where truncate(latitude,0)='$lat' and truncate(longitude,0)='$lng'");
	  $res=mysqli_query($con,"select *, round( ( 3959 * acos( least(1.0,  cos( radians($latitude) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians(latitude) ) ) ) ), 1) as distance from area having distance <= 5 order by distance asc");
	  $i=0;
	  while($row=mysqli_fetch_array($res))
	  {
		  //$getlat=$row['latitude'];
		  //$getlng=$row['longitude'];
		  
         //$km=distance($latitude, $longitude, $getlat, $getlng, "K");	
	     
		 //if($km<=5)
		 { 
		    $rr = array("id"=>$row["id"],"area"=>$row["area"],"region"=>$row["region"],"state"=>$row["state"]);		 
		    $response[]=$rr;
		 }
		 //$i++;
	  }
	  $data=array();
	  $data["data"]=$response;	 
	  //$data["count"]=$i;	 
	  echo json_encode($data); 	  
  } 
  
 
?>
