<?php
  
   include("../connect.php");




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
		if (($lat1 == $lat2) && ($lon1 == $lon2)){
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

  
  
   if(isset($_GET['new']))
   {
	  
	  extract($_POST);
	  $response=array();
	  $res=mysqli_query($con,"select * from outlets where outlettype='$outlettype' and name='$name'  and contact='$contact'");
	  if($row=mysqli_fetch_array($res)) 
	   {
		 $response["message"]="already";
		 echo json_encode($response); 
		 return;   
	   }
	  if(isset($_FILES['empimage']['name']))
	  {
	  $filename=$_FILES['empimage']['name'];
	  $tmpname=$_FILES['empimage']['tmp_name'];
	  $filename=$name.$contact.$filename.".jpg";
	  }
	  
	  $lat=truncate_number($latitude,3);
	  $lng=truncate_number($longitude,3);

	  mysqli_query($con,"insert into outlets(name,address,lastvisitpic,contactperson,contact,pincode,gstnumber,outlettype,outletsubtype,routeid,competitor_presense,street,locality,city,state,latitude,longitude,areaid,lastvisit,creationdate,createdby) values('$name','$address','$filename','$contactperson','$contact','$pincode','$gstnumber','$outlettype','$outletsubtype','$areaId','0','$street','$locality','$city','$state','$latitude','$longitude','$areaId','$datetime','$datetime','$createdby')");
	  
	  if(mysqli_affected_rows($con)>0)
	  {
		  $outletid=mysqli_insert_id($con);
		$response["id"]=$outletid;
		mysqli_query($con,"insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,Latitude,Longitude) values('$createdby','$outletid','New Outlet Create','$battery%','$date','$time','$latitude','$longitude')")or die(mysqli_error($con));
		  if(isset($_FILES['empimage']['name']))
		  {
		     if(move_uploaded_file($tmpname,"../imgoutlets/".$filename))
		      {
		         $response["message"]="success";
			
		      }
		      else
	          {
		         $response["message"]="error";
		  
	          }
		  }
		  else
		  {
			  $response["message"]="success";  
		  }
	  }
	  else
	  {
		   $response["message"]="error";
	  }
	   
	    echo json_encode($response); 
   }
   
   if(isset($_GET['edit']))
   {
	  
	  extract($_POST);
	  $filename=$_FILES['empimage']['name'];
	  $tmpname=$_FILES['empimage']['tmp_name'];
	  $filename=$name.$contact.$filename.".jpg";
	  $response=array();
	  
	  $lat=truncate_number($latitude,3);
	  $lng=truncate_number($longitude,3);
	  
	  
	  
	  mysqli_query($con,"update outlets set  name='$name',
	  address='$address',
	  lastvisitpic='$filename',
	  contactperson='$contactperson',
	  contact='$contact',
	  pincode='$pincode',
	  gstnumber='$gstnumber',
	  outlettype='$outlettype',
	  outletsubtype='$outletsubtype',
	  distributorid='$distributorid',
	  routeid='$areaId',
	  areaid='$areaId',
	  competitor_presense='0',
	  street='$street',
	  locality='$locality',
	  city='$city',
	  state='$state',
	  latitude='$latitude',
	  longitude='$longitude',
	  lastvisit='$datetime',
	  updated_at='$datetime',
	  createdby='$createdby' where id='$id'");
	  
	  if(mysqli_affected_rows($con)>0)
	  {
		  $outletid=mysqli_insert_id($con);
		  $descrip="Outlet $name , $address details updated by $username";
		mysqli_query($con,"insert into log(description,creationdate,createdby) values('$descrip','$datetime','$username')")or die(mysqli_error($con));
		
// 		print_r($tmpname,"../imgoutlets/".$filename);
// 			echo "tmpname==";
// 		echo $tmpname;
// 		echo "filename==";
// 	    echo $filename;
// 		$test =move_uploaded_file($tmpname,"../imgoutlets/".$filename);
// 		echo $test;
// 		die;
		  if(move_uploaded_file($tmpname,"../imgoutlets/".$filename))
		  {
		    $response["message"]="success";
		  }
		  else
	      {
		   $response["message"]="image error ";
	      }
	  }
	  else
	  {
		   $response["message"]=" no updated  error";
	  }
	   
	    echo json_encode($response); 
   }




      
   if(isset($_GET['visitregister']))
   {
   
       
	  extract($_POST);
	   if (!empty($datetime)) {
		 $datetime = $datetime;
	  }else {
		$datetime = date("Y-m-d H:i:s");
	  }
	  if(isset($_FILES['lastvisitpic']['name']))
	  {
	    $filename=$_FILES['lastvisitpic']['name'];
	    $tmpname=$_FILES['lastvisitpic']['tmp_name'];
	    $filename=$name.$contact.$filename.".jpg";
	    $response=array();
	    $folder="imgoutlets";	  
	    $query="update  outlets set lastvisitpic='$filename',lastvisit='$datetime' where id='$id'";
        if($outlettype=="2")
	    { 		  
	      $folder="imgusers";
	      $query="update employees set image='$filename',lastlogin='$datetime' where id='$id' and usertype='2'";
	    }
	  
	    if($outlettype=="3")
	    {		  
	      $folder="imgusers";
	      $query="update employees set image='$filename',lastlogin='$datetime' where id='$id' and usertype='3'";
	    }
	  
	    if($outlettype=="4")
	    {		  
	      $outlettype=="0";
	    }      	  
	    mysqli_query($con,$query)or die(mysqli_error($con));
	    if(mysqli_affected_rows($con)>0)
	    {
           move_uploaded_file($tmpname,"../".$folder."/".$filename);		  
	    }
	  } else {
	    $query="update  outlets set lastvisit='$datetime' where id='$id'";
		mysqli_query($con,$query)or die(mysqli_error($con));
	  }
	  
	  mysqli_query($con,"insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,Latitude,Longitude,visittype) values('$userid','$id','$outletvisit','$battery%','$date','$time','$latitude','$longitude','$outlettype')")or die(mysqli_error($con));
		if(mysqli_affected_rows($con)>0)
		{ 
		   $entryid=mysqli_insert_id($con);		  		  
		    $response["message"]="success";
			$response["entryid"]=$entryid;
		}
	    else
	    {
	        $query="update  outlets set lastvisit='$datetime' where id='$id'";   mysqli_query($con,$query)or die(mysqli_error($con));
		   $response["message"]="error";
	    }	   
	    echo json_encode($response); 
 }


if(isset($_GET['feedback']))
   {
	  extract($_POST);
	  $response=array();
	  	  
	  mysqli_query($con,"update  outletactivity set feedback='$feedback', rating='$rating' where id='$id'")or die(mysqli_error($con));
	  
	  if(mysqli_affected_rows($con)>0)
	  {
		    $response["message"]="success";
	  }
	  else
	  {
		   $response["message"]="error";
	  }
	   $response["message"]="success";
	    echo json_encode($response); 
 
   }

    if(isset($_GET['outletshow']))
	{
		extract($_REQUEST);
	   //$res=mysqli_query($con,"select * from outlets where areaid='$areaid'");
	   //$lat=truncate_number($lat,1);
	   //$lng=truncate_number($lng,1);
	  
	   //$res=mysqli_query($con,"select * from outlets where truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng' order by lastvisit desc");
	   //$res=mysqli_query($con,"select * from outlets");
		$res=mysqli_query($con,"select *, round( ( 6371  * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance from outlets having distance <= 0.5 order by distance asc");

	   $response=array();
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["lastvisitpic"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["pincode"]=$row["pincode"];
		   $rr["gstnumber"]=$row["gstnumber"];
		   $rr["outlettype"]=$row["outlettype"];
		   $rr["competitor_presense"]=$row["competitor_presense"];
		   $rr["distributorid"]=$row["distributorid"];
		   $rr["salesmanagerid"]=$row["salesmanagerid"];
		   $rr["rsmid"]=$row["rsmid"];
		   $rr["routeid"]=$row["routeid"];
		   $rr["street"]=$row["street"];
		   $rr["locality"]=$row["locality"];
		   $rr["city"]=$row["city"];
		   $rr["state"]=$row["state"];
		   $rr["latitude"]=$row["latitude"];
		   $rr["longitude"]=$row["longitude"];
		   $rr["areaid"]=$row["areaid"];
		   $rr["lastvisit"]=$row["lastvisit"];
		   $rr["creationdate"]=$row["creationdate"];
		   $rr["createdby"]=$row["createdby"]; 
		   array_push($response,$rr);
	   } 
	   
	   $data=array();
	   $data["data"]=$response;
	   echo json_encode($data);
	   return;
	   
	}
    

    if(isset($_GET['distributorshow']))
	{
		extract($_REQUEST);
	   //$res=mysqli_query($con,"select * from outlets where areaid='$areaid'");
	   //$lat=truncate_number($lat,1);
	   //$lng=truncate_number($lng,1);
	  
	   //$res=mysqli_query($con,"select * from outlets where truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng' order by lastvisit desc");
	   //$res=mysqli_query($con,"select * from outlets");
	   $res=mysqli_query($con,"select *, round( ( 6371  * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance from employees where usertype=3 having distance <= 0.5  order by distance asc");
	   $response=array();
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		//    $rr["address"]=$row['address'];
		//    $rr["lastvisitpic"]=$row["lastvisitpic"];
		//    $rr["contactperson"]=$row["contactperson"];
		//    $rr["contact"]=$row["contact"];
		//    $rr["pincode"]=$row["pincode"];
		//    $rr["gstnumber"]=$row["gstnumber"];
		//    $rr["outlettype"]=$row["outlettype"];
		   $rr["empid"]=$row["empid"];
		//    $rr["distributorid"]=$row["distributorid"];
		//    $rr["salesmanagerid"]=$row["salesmanagerid"];
		//    $rr["rsmid"]=$row["rsmid"];
		//    $rr["routeid"]=$row["routeid"];
		//    $rr["street"]=$row["street"];
		//    $rr["locality"]=$row["locality"];
		//    $rr["city"]=$row["city"];
		//    $rr["state"]=$row["state"];
		   $rr["latitude"]=$row["latitude"];
		   $rr["longitude"]=$row["longitude"];
		   $rr["areaid"]=$row["areaid"];
		//    $rr["lastvisit"]=$row["lastvisit"];
		//    $rr["creationdate"]=$row["creationdate"];
		//    $rr["createdby"]=$row["createdby"]; 
		   array_push($response,$rr);
	   } 
	   
	   $data=array();
	   $data["data"]=$response;
	   echo json_encode($data);
	   return;
	   
	}	

   if(isset($_GET['show']))
   {
	   extract($_REQUEST);
	   //$res=mysqli_query($con,"select * from outlets where areaid='$areaid'");
	   //$lat=truncate_number($lat,1);
	   //$lng=truncate_number($lng,1);
	  
	   //$res=mysqli_query($con,"select * from outlets where truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng' order by lastvisit desc");
	   //$res=mysqli_query($con,"select * from outlets");
	  // $res=mysqli_query($con,"select *, round( ( 6371  * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance from outlets where areaid='$areaId having distance <= 0.5 order by distance asc");
	   $res=mysqli_query($con,"select *, round( ( 6371  * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance  from outlets  where routeid='$areaId' order by distance asc ");
	   $response=array();

  $distributorid = $distributorName = '';

// Escape $areaId to prevent issues if it's from user input
		

		$distributorid = $distributorName = '';

		$res2 = mysqli_query($con, "
			SELECT employees.name AS distributor_name, employees.id AS id 
			FROM area 
			JOIN employees ON area.distributor_id = employees.id  
			WHERE area.id = '$areaId'
		");

		if ($res2 && mysqli_num_rows($res2) > 0) {
			$resRoute = mysqli_fetch_assoc($res2);
			if ($resRoute) {
				$distributorName = $resRoute['distributor_name'];
				$distributorid = $resRoute['id'];
			}
		}

	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["lastvisitpic"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["pincode"]=$row["pincode"];
		   $rr["gstnumber"]=$row["gstnumber"];
		   $rr["outlettype"]=$row["outlettype"];
		   $rr["competitor_presense"]=$row["competitor_presense"];
		   $rr["distributorid"]=$distributorid;
		   $rr["salesmanagerid"]=$row["salesmanagerid"];
		   $rr["rsmid"]=$row["rsmid"];
		   $rr["routeid"]=$row["routeid"];
		   $rr["street"]=$row["street"];
		   $rr["locality"]=$row["locality"];
		   $rr["city"]=$row["city"];
		   $rr["state"]=$row["state"];
		   $rr["latitude"]=$row["latitude"];
		   $rr["longitude"]=$row["longitude"];
		   $rr["areaid"]=$row["areaid"];
		   $rr["lastvisit"]=$row["lastvisit"];
		   $rr["creationdate"]=$row["creationdate"];
		   $rr["createdby"]=$row["createdby"]; 
		   $rr["distributor_name"]=$distributorName; 

		   array_push($response,$rr);
	   } 
	   
	   
	   //$res=mysqli_query($con,"select * from employees where truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng' and (usertype='2' or usertype='3')");
	   ////$res=mysqli_query($con,"select * from outlets");
	   
	   //while($row=mysqli_fetch_array($res))
	   //{
		  // $rr=array();
		  // $rr["id"]=$row["id"];
		  // $rr["name"]=$row["name"];
		  // $rr["address"]=$row['address'];
		  // $rr["lastvisitpic"]=$row["image"];
		  // $rr["contactperson"]=$row["empid"];
		  // $rr["contact"]=$row["contact"];
		  // $rr["pincode"]=$row["battery"];;
		  // $rr["gstnumber"]=$row["region"];
		  // $rr["outlettype"]=$row["usertype"]=="2"?"STOCKIST":"DISTRIBUTOR";
		  // $rr["competitor_presense"]=$row["email"];
		  // $rr["distributorid"]=$row["stockistid"];
		  // $rr["salesmanagerid"]="0";
		  // $rr["rsmid"]="0";
		  // $rr["routeid"]="0";
		  // $rr["street"]=$row["locality"];
		  // $rr["locality"]=$row["locality"];
		  // $rr["city"]=$row["city"];
		  // $rr["state"]=$row["state"];
		  // $rr["latitude"]=$row["latitude"];
		  // $rr["longitude"]=$row["longitude"];
		  // $rr["areaid"]=$row["areaid"];
		  // $rr["lastvisit"]=$row["lastlogin"];
		  // $rr["creationdate"]=$row["creationdate"];
		  // $rr["createdby"]=$row["createdby"]; 
		  // array_push($response,$rr);
	   //} 
	   
	   
	   $data=array();
	   $data["data"]=$response;
	   echo json_encode($data);
	   return; 
   }
   
   
      if(isset($_GET['activityvisit']))
   {
	   $userid=$_POST["userid"];
	   $res=mysqli_query($con,"select a.id,o.name,o.address,o.lastvisitpic,o.contactperson,o.contact,o.gstnumber,o.outlettype,a.Latitude,a.Longitude,a.activitytype,a.activitydate,a.activitytime,a.feedback,a.battery,a.rating,e.name as 'empname',e.empid from outletactivity a join outlets o on a.outletid=o.id join employees e  on e.id=a.userid where a.userid='$userid' and a.visittype='0' and a.activitydate='$date' order by a.id desc");   
	   $response=array();
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["lastvisitpic"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["gstnumber"]=$row["gstnumber"];
		   $rr["outlettype"]=$row["outlettype"];
		   $rr["activitytype"]=$row["activitytype"];
		   $rr["activitydate"]=$row["activitydate"];
		   $rr["activitytime"]=$row["activitytime"];
		   $rr["feedback"]=$row["feedback"];
		   $rr["battery"]=$row["battery"];
		   $rr["rating"]=$row["rating"]; 
		   $rr["latitude"]=$row["Latitude"];
		   $rr["longitude"]=$row["Longitude"];
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"];
           		   
		   array_push($response,$rr);
	   }
	   
	   $res=mysqli_query($con,"select a.id,o.name,o.address,o.image,o.empid,o.contact,o.region,a.Latitude,a.Longitude,a.activitytype,a.activitydate,a.activitytime,a.feedback,a.battery,a.rating,e.name as 'empname',e.empid from outletactivity a join employees o on a.outletid=o.id join employees e  on e.id=a.userid where a.userid='$userid' and a.visittype='2' and a.activitydate='$date' order by a.id desc");   
	   
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["image"];
		   $rr["contactperson"]=$row["empid"];
		   $rr["contact"]=$row["contact"];
		   $rr["gstnumber"]=$row["region"];
		   $rr["outlettype"]="Stockist";
		   $rr["activitytype"]=$rr["activitytype"];
		   $rr["activitydate"]=$row["activitydate"];
		   $rr["activitytime"]=$row["activitytime"];
		   $rr["feedback"]=$row["feedback"];
		   $rr["battery"]=$row["battery"];
		   $rr["rating"]=$row["rating"]; 
		   $rr["latitude"]=$row["Latitude"];
		   $rr["longitude"]=$row["Longitude"];
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"];
           		   
		   array_push($response,$rr);
	   }
	   
	   	   $res=mysqli_query($con,"select a.id,o.name,o.address,o.image,o.empid,o.contact,o.region,a.Latitude,a.Longitude,a.activitytype,a.activitydate,a.activitytime,a.feedback,a.battery,a.rating,e.name as 'empname',e.empid from outletactivity a join employees o on a.outletid=o.id join employees e  on e.id=a.userid where a.userid='$userid' and a.visittype='3' and a.activitydate='$date' order by a.id desc");   
	   
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["image"];
		   $rr["contactperson"]=$row["empid"];
		   $rr["contact"]=$row["contact"];
		   $rr["gstnumber"]=$row["region"];
		   $rr["outlettype"]="Distributor";
		   $rr["activitytype"]=$row["activitytype"];
		   $rr["activitydate"]=$row["activitydate"];
		   $rr["activitytime"]=$row["activitytime"];
		   $rr["feedback"]=$row["feedback"];
		   $rr["battery"]=$row["battery"];
		   $rr["rating"]=$row["rating"]; 
		   $rr["latitude"]=$row["Latitude"];
		   $rr["longitude"]=$row["Longitude"];
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"];
           		   
		   array_push($response,$rr);
	   }
	   
	   
	   $res=mysqli_query($con,"select a.id,a.companyname,a.address,a.image,a.contactperson,a.contactno,a.area,a.city,a.state,a.Latitude,a.Longitude,a.activitytype,a.activitydate,a.activitytime,a.feedback,a.battery,a.rating,e.name as 'empname',e.empid from outletactivity a join employees e  on e.id=a.userid where a.userid='$userid' and a.visittype='4'  and a.activitydate='$date' order by a.id desc");   
	   
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["companyname"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["image"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contactno"];
		   $rr["gstnumber"]=$row["area"];
		   $rr["outlettype"]="Tour Visit";
		   $rr["activitytype"]=$row["activitytype"];
		   $rr["activitydate"]=$row["activitydate"];
		   $rr["activitytime"]=$row["activitytime"];
		   $rr["feedback"]=$row["feedback"];
		   $rr["battery"]=$row["battery"];
		   $rr["rating"]=$row["rating"]; 
		   $rr["latitude"]=$row["Latitude"];
		   $rr["longitude"]=$row["Longitude"];
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"];
           		   
		   array_push($response,$rr);
	   }
	   
	    
	   /*$dist=0.0;
	   for($i=0;$i<count($response)-1;$i++)
	   {
		   $rr=$response[$i];
		   $rr1=$response[$i+1];
		   $dist+=distance($rr["latitude"],$rr["longitude"],$rr1["latitude"],$rr1["longitude"],"K");
	   }
	   */
	   $data=array();
	   //array_multisort($response[0]['id'],SORT_DESC,SORT_NUMERIC);
	   $data["data"]=$response;
	  
	   echo json_encode($data);
	   //echo round($dist,2);
	   return;
   }
   
   if(isset($_GET['tourvisitregister']))
   {
	  extract($_POST);
	  $filename=$_FILES['lastvisitpic']['name'];
	  $tmpname=$_FILES['lastvisitpic']['tmp_name'];
	  $filename=$name.$contact.$filename.".jpg";
	  $response=array();
	  $folder="imgoutlets";	  
	  mysqli_query($con,"insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,Latitude,Longitude,visittype,companyname,contactperson,contactno,address,area,city,state,image) values('$userid','0','Tour Visit($reason)','$battery%','$date','$time','$lat','$lng','$outlettype','$companyname','$contactperson','$contactno','$address','$area','$city','$state','$filename')")or die(mysqli_error($con)); 
	  $entryid=mysqli_insert_id($con);
	  if(move_uploaded_file($tmpname,"../".$folder."/".$filename))
		{
		  $response["message"]="success";
		  $response["entryid"]=$entryid;
		}
	  else
	    {
		  $response["message"]="error";
		  $response["entryid"]="0";
	    }
	    echo json_encode($response); 
   }


if(isset($_GET['sync']))
   {
	  
	  extract($_POST);
	  $response="";
	  $res=mysqli_query($con,"select * from outlets where outlettype='$outlettype' and name='$name'  and contact='$contact'");
	  if($row=mysqli_fetch_array($res)) 
	   {
		 $response="already";
		 echo $response; 
		 return;   
	   }
	  
	  
	  
	  $lat=truncate_number($latitude,3);
	  $lng=truncate_number($longitude,3);
	  
	  
	  
	  mysqli_query($con,"insert into outlets(name,address,lastvisitpic,contactperson,contact,pincode,gstnumber,outlettype,outletsubtype,distributorid,routeid,competitor_presense,street,locality,city,state,latitude,longitude,areaid,lastvisit,creationdate,createdby) values('$name','$address','','$contactperson','$contact','$pincode','$gstnumber','$outlettype','$outletsubtype','$distributorid','0','0','$street','$locality','$city','$state','$latitude','$longitude','$areaid','$time','$date','$createdby')");
	  
	  if(mysqli_affected_rows($con)>0)
	  {
		  $outletid=mysqli_insert_id($con);
		  //$response["id"]=$outletid;
		  mysqli_query($con,"insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,Latitude,Longitude) values('$createdby','$outletid','New Outlet Create','$battery%','$date','$time','$latitude','$longitude')")or die(mysqli_error($con));
		
		  
		   $response="success";
		  
	      
	  }
	  else
	  {
		   $response="error";
	  }
	   
	    echo $response; 
   }
   
?>
