
<?php
/*area api*/

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
  $userid=@$_SESSION['id'];

  if(isset($_GET['routes']))
  {	
     extract($_REQUEST);
      if(!empty($userType) and is_numeric($userType) and  !empty($userId) and is_numeric($userId) ){
	       $res=mysqli_query($con,"select a.id,a.area, round( ( 6371 * acos( least(1.0,  cos( radians($lat) ) * cos( radians(a.latitude) ) * cos( radians(a.longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(a.latitude) ) ) ) ), 3) as distance from area a where a.distributor_id='$userId'order by distance asc limit 6");
      }else{
	       $res=mysqli_query($con,"select a.id,a.area, round( ( 6371 * acos( least(1.0,  cos( radians($lat) ) * cos( radians(a.latitude) ) * cos( radians(a.longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(a.latitude) ) ) ) ), 3) as distance from area a  order by distance asc limit 6");
      }

	   $response=array();
	  
    while($row=mysqli_fetch_array($res))
    {
        $rr=array();
        $rr['id']=$row['id'];
        $rr['area']=$row['area'];
        array_push($response,$rr);
    }
	   $data=array();
	   $data["data"]=$response;	 
	   echo json_encode($data); 
  }


  if(isset($_GET['show']))
  { 
    $res=mysqli_query($con,"SELECT a.id,a.country,a.area,a.latitude,a.longitude,a.km,a.registrationdate,s.name AS sname,reg.name AS rname ,c.city AS cityname ,emp.name As distributor  FROM area a LEFT JOIN regions reg ON reg.id=a.region LEFT JOIN states s ON s.id=a.state LEFT JOIN cities c ON c.id=a.city LEFT JOIN employees emp on emp.id=a.distributor_id  order By a.area "); 
		    $response=array();
      
      

        $result=mysqli_query($con,"select routeid , COUNT(id) AS total_count ,MAX(lastvisit) AS max_lastvisit from outlets GROUP BY routeid");
        $outlets=mysqli_fetch_array($result);

       $arraylastvist= $arrayoutlate = array();
        while($outlets=mysqli_fetch_array($result))
        {
          $arrayoutlate[$outlets['routeid']]=$outlets['total_count'];
          $arraylastvist[$outlets['routeid']]=$outlets['max_lastvisit'];
        }


        while($row=mysqli_fetch_array($res))
        {


          $rr=array();
          $rr['id']=$row['id'];
          $rr['area']=$row['area'];
          $rr['region']=$row['rname'];
          $rr['state']=$row['sname'];
          $rr['cityname']=$row['cityname'];    
          $rr['distributor']=$row['distributor'];
  		    $rr['last_visit']=@$arraylastvist[$row['id']]??0;
  		    $rr['no_of_outlats']=@$arrayoutlate[$row['id']]??0;

	       array_push($response,$rr);
        }
    echo json_encode($response); 

  }

  elseif(isset($_GET['edit']))
  {
 
     extract($_POST);
     mysqli_query($con,"update area set latitude='$lat',longitude='$lng', registrationdate='$datetime',distributor_id='$distributor',area='$area',state='$state', city='$city' ,region='$region' where id='$id'") or die(mysqli_error($con));
      if(mysqli_affected_rows($con)>0)
      {
	     echo "success";
      }
      else
      {
	     echo "error".$id;
      }
  }  
  
  elseif(isset($_GET['add']))
  {
     extract($_POST);


     mysqli_query($con,"insert into area(country,state,city,region,area,latitude,longitude,km,registrationdate,distributor_id) values ( '$country','$state','$city','$region','$area','$lat','$lng','0','$datetime','$distributor')") or die(mysqli_error($con));
      if(mysqli_affected_rows($con)>0)
      {
	     echo "success";
      }
      else
      {
	    echo "error".$id;
      }
  }
  
  elseif(isset($_GET['import']))
  {
      
      
    $query="SELECT id,city FROM cities";
    $res=mysqli_query($con,$query);
    $cityname=array();
    
    while($row=mysqli_fetch_array($res))
    {
        $cityname[strtolower($row["city"])]=$row["id"];
    }
    
    
          
    $query="SELECT id,name FROM states";
    $sres=mysqli_query($con,$query);
    $statesname=array();
    
    while($srow=mysqli_fetch_array($sres))
    {
        $statesname[strtolower($srow["name"])]=$srow["id"];
    }
      
      
            
    $query="SELECT id,name FROM regions";
    $res=mysqli_query($con,$query);
    $regionsname=array();
    $datetime = date("Y-m-d H:i:s");
    
    while($row=mysqli_fetch_array($res))
    {
        $regionsname[strtolower($row["name"])]=$row["id"];
    }
      
    
    

      
	 $filetype=$_FILES["file1"]["type"];
	 /*if($filetype!="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
	 {
		echo "error-type";
		return; 
	 }*/
     $filename=$_FILES["file1"]["tmp_name"];
     if($_FILES["file1"]["size"] > 0)
      {
        $file = fopen($filename, "r");
        $count=0;

/*
       print_r($statesname);
       print_r($regionsname);
       echo "string";*/
        while (($areaData = fgetcsv($file, 10000, ",")) !== FALSE)
        {
  			
              $count++;
  			if($count>1)
  			{
          
			    $areaData[1]=$statesname[strtolower($areaData[1])];
			/*  $areaData[2]=$cityname[strtolower($areaData[2])];*/
			    $areaData[2]=$regionsname[strtolower($areaData[2])];
			    
			/*	echo "$areaData[0],$areaData[1],$areaData[2],$areaData[3],$areaData[4],$areaData[5]"."<br>";*/
                mysqli_query($con,"insert into area(country,state,region,area,latitude,longitude,km,registrationdate) values ( '$areaData[0]','$areaData[1]','$areaData[2]','$areaData[3]','$areaData[4]','$areaData[5]','0','$datetime')") or die(mysqli_error($con));
			}
        }
        fclose($file);
      /*  die();*/
        echo "success";
        
     }      
     else
     {
	    echo "error";
     }
  }
  
  
elseif(isset($_GET['areas']))
{
  $ids=$_GET['c_id'];


          $query="SELECT area.id,area.area,res.name FROM `area` LEFT JOIN regions res ON res.id=area.region Where area.city='$ids'";
             $res=mysqli_query($con,$query);
     $response=array();
     
     
     
       while($row=mysqli_fetch_array($res))
         {
           $rr=array("id"=>$row["id"],"area"=>$row["area"],"resion"=>$row["name"]);
              $response[]=$rr;
             } 
      
     $data=json_encode($response);
     echo $data;
}    
  
elseif(isset($_GET['delete']))
{
	$ids=$_POST['ids'];
	foreach($ids as $id)
	{
      mysqli_query($con,"delete from area where id='$id'");
	}
   echo "Areas Delete Succesfully...";
}   
 
?>
