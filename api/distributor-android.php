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

  
  
   
  function genrateId($con)
  {  
	  $query = "select empid from employees where usertype='3' order by empid desc";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  $id="";
	  if($row=mysqli_fetch_array($res)){
			$id=$row["empid"];
	  }
	  if($id=="")
	  return "DIS0001";
	  $id=(int)substr($id,3);
	  $id=$id+1;
	  
	  if($id>0 &&$id<10)
	  {
		  return"DIS000".$id;
	  }
	  if($id>9 &&$id<100)
	  {
		  return"DIS00".$id;
	  }
	  if($id>99 &&$id<1000)
	  {
		  return"DIS0".$id;
	  }
	  if($id>999)
	  {
		  return"DIS".$id;
	  }
  }
  
  
   function genrateSSId($con)
  {
	  
	  $query = "select empid from employees where usertype='2' order by empid desc";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  $id="";
	  if($row=mysqli_fetch_array($res)){
			$id=$row["empid"];
	  }
	  if($id=="")
	  return "SS0001";
	  $id=(int)substr($id,2);
	  $id++;
	  if($id>0 &&$id<10)
	  {
		  return"SS000".$id;
	  }
	  if($id>9 &&$id<100)
	  {
		  return"SS00".$id;
	  }
	  if($id>99 &&$id<1000)
	  {
		  return"SS0".$id;
	  }
	  if($id>999)
	  {
		  return"SS".$id;
	  }
  }

  
  if(isset($_GET['insert']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	  $response=array();
	  //var_dump($_POST);
	  if($usertype=="3")
	  {
	    $empcode=genrateId($con);
		  
	  }
	  if($usertype=="2")
	  {
	    $empcode=genrateSSId($con); 
	  }
	  
	  
	  $query = "select email from employees where email = '$empemail' and usertype='$usertype'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			$response["message"]="empemail";
			echo json_encode($response);
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and usertype='$usertype'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			$response["message"]="empcontact";
			echo json_encode($response);
			return;
	  }
	$filename=$_FILES['lastvisitpic']['name'];
	$tmpname=$_FILES['lastvisitpic']['tmp_name'];
	$filesize=$_FILES['lastvisitpic']['size'];
	$filetype=$_FILES['lastvisitpic']['type'];
	
	if($filesize>800000)
	{
	  echo"Image can't be Greater than 800KB .";
	  return;
	}
	
	$filename=$empname.$empcode.".jpg";
	mysqli_query($con,"insert into employees(image,name,empid,email,contact,address,designationid,roleid,managerid,usertype,password,salary,commission,city,state,reportsto,latitude,longitude,battery,region,doj,creationdate,createdby,stockistid,areaid,lastlogin) values('$filename','$empname','$empcode','$empemail','$empcontact','$empaddress','0','0','0','$usertype',password('$emppass'),'0','0','$city','$state','0','$lat','$lng','$pannumber','$gstnumber','$date','$datetime','$userid','$stockistid','$areaid','$datetime')") or die(mysqli_error($con));
	
	$createid=mysqli_insert_id($con);
	$query="";
	if($usertype=="2")
	{
	  $query="insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,feedback,Latitude,Longitude,visittype) values('$userid','$createid','Super Stockist Create','$battery%','$date','$time','New Super Stockist Create id is $empcode','$lat','$lng','2')";
	}
	else
	{
	  $query="insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,feedback,Latitude,Longitude,visittype) values('$userid','$createid','Distributor Create','$battery%','$date','$time','New Distributor Create id is $empcode','$lat','$lng','3')";
	}
	
	mysqli_query($con,$query);
	
	if(mysqli_affected_rows($con)>0)
	{
		
	  if(move_uploaded_file($tmpname,"../imgusers/".$filename))
	  {
		  $response["message"]="success";
			echo json_encode($response);
	  }
	  else
	  {
		  $response["message"]="success";
		 //$response["message"]=mysqli_error($con);
			echo json_encode($response);
	  }
	}
	
  }


 if(isset($_GET['edit']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	  $response=array();
	  
	  $query = "select email from employees where email = '$empemail' and usertype='$usertype' and id!='$id'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			$response["message"]="empemail";
			echo json_encode($response);
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and usertype='$usertype' and id!='$id'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			$response["message"]="empcontact";
			echo json_encode($response);
			return;
			
	  }
	$filename=$_FILES['lastvisitpic']['name'];
	$tmpname=$_FILES['lastvisitpic']['tmp_name'];
	$filesize=$_FILES['lastvisitpic']['size'];
	$filetype=$_FILES['lastvisitpic']['type'];
	
	if($filesize>800000)
	{
	  echo"Image can't be Greater than 800KB .";
	  return;
	}
	
	$filename=$empname.$empcode.".jpg";
	mysqli_query($con,"update  employees set  image='$filename',name='$empname',email='$empemail',contact='$empcontact',address='$empaddress',usertype='$usertype',password=password('$emppass'),city='$city',state='$state',latitude='$lat',longitude='$lng',battery='$pannumber',region='$gstnumber',stockistid='$stockistid',areaid='$areaid',lastlogin='$datetime' where id='$id' and usertype='$usertype'") or die(mysqli_error($con));
	
	$query="";
	if($usertype=="2")
	{
	  //$query="insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,feedback,Latitude,Longitude,visittype) values('$userid','$id','Super Stockist Update','$battery%','$date','$time','Super Stockist Updated id is $empcode','$latitude','$longitude','3')";
	}
	else
	{
		//$query="insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,feedback,Latitude,Longitude,visittype) values('$userid','$id','Distributor Update','$battery%','$date','$time','Distributor Updated id is $empcode','$latitude','$longitude','3')";
	}
	
	mysqli_query($con,$query);
	
	if(mysqli_affected_rows($con)>0)
	{
		
	  if(move_uploaded_file($tmpname,"../imgusers/".$filename))
	  {
		  $response["message"]="success";
			echo json_encode($response);
	  }
	  else
	  {
		  $response["message"]="error";
			echo json_encode($response);
	  }
	}
	
  }


  if(isset($_GET['show']))
  {	
     extract($_REQUEST);
	   //$res=mysqli_query($con,"select * from outlets where areaid='$areaid'");
	   //$lat=truncate_number($lat,0);
	   //$lng=truncate_number($lng,0); 
	 
	 //$res=mysqli_query($con,"select e.id, e.image,e.name, e.empid, e.email, e.contact, e.address, e.usertype, e.password, e.salary, e.commission, e.city, e.state, e.latitude, e.longitude, e.region, e.doj,e.dol,e.reportsto,e.stockistid,e.battery as 'panno',e.region  as'gstno',e.areaid,e.creationdate,e.lastlogin,e.createdby,emp.name as 'ssname' from  employees e join employees emp on emp.id=e.stockistid where e.usertype='3' order by lastlogin desc");
	  $res=mysqli_query($con,"select e.id, e.image,e.name, e.empid, e.email, e.contact, e.address, e.usertype, e.password, e.salary, e.commission, e.city, e.state, e.latitude, e.longitude, e.region, e.doj,e.dol,e.reportsto,e.stockistid,e.battery as 'panno',e.region  as'gstno',e.areaid,e.creationdate,e.lastlogin,e.createdby,emp.name as 'ssname', round( ( 6371 * acos( least(1.0,  cos( radians($lat) ) * cos( radians(e.latitude) ) * cos( radians(e.longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(e.latitude) ) ) ) ), 3) as distance from  employees e join employees emp on emp.id=e.stockistid where e.usertype='3' having distance <= 1 order by distance asc");
	 $response=array();
	  
	 while($row=mysqli_fetch_array($res))
	 {
       $rr = array("id"=>$row["id"],"name"=>$row["name"],"empid"=>$row["empid"],"email"=>$row["email"],"usertype"=>$row["usertype"],"contact"=>$row["contact"],"address"=>$row["address"],"salary"=>$row["salary"],"commission"=>$row["commission"],"city"=>$row["city"],"state"=>$row["state"],"locality"=>$row["city"],"latitude"=>$row["latitude"],"longitude"=>$row["longitude"],"panno"=>$row["panno"],"gstno"=>$row["gstno"],"image"=>$row["image"],"lastlogin"=>$row["lastlogin"],"creationdate"=>$row["creationdate"],"createdby"=>$row["createdby"],"areaid"=>$row["areaid"],"ssname"=>$row["ssname"]);
		 $response[]=$rr;
     }
	   $data=array();
	   $data["data"]=$response;	 
	   echo json_encode($data); 
  }

   if(isset($_GET['showlist']))
  {	
     extract($_REQUEST);
	   //$res=mysqli_query($con,"select * from outlets where areaid='$areaid'");
	   //$lat=truncate_number($lat,0);
	   //$lng=truncate_number($lng,0); 
	 
	 //$res=mysqli_query($con,"select e.id, e.name, e.address from  employees e where e.usertype='3' order by e.name asc ");
	 $res=mysqli_query($con,"select e.id,e.name,e.address, round( ( 6371 * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance from employees e where e.usertype='3' having distance <= 1 order by distance asc");
	 $response=array();
	  
	 while($row=mysqli_fetch_array($res))
	 {
       $rr = array("id"=>$row["id"],"name"=>$row["name"],"address"=>$row["address"]);
		 $response[]=$rr;
     }
	   $data=array();
	   $data["data"]=$response;	 
	   echo json_encode($data); 
  }


  if(isset($_GET['sync']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	  $response="";
	  //var_dump($_POST);
	  if($usertype=="3")
	  {
	    $empcode=genrateId($con);
		  
	  }
	  if($usertype=="2")
	  {
	    $empcode=genrateSSId($con); 
	  }
	  
	  
	  $query = "select email from employees where email = '$empemail' and usertype='$usertype'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			$response="empemail";
			echo $response;
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and usertype='$usertype'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			$response="empcontact";
			echo $response;
			return;
	   }
	
	
	
	mysqli_query($con,"insert into employees(image,name,empid,email,contact,address,designationid,roleid,managerid,usertype,password,salary,commission,city,state,reportsto,latitude,longitude,battery,region,doj,creationdate,createdby,stockistid,areaid,lastlogin) values('','$empname','$empcode','$empemail','$empcontact','$empaddress','0','0','0','$usertype',password('$emppass'),'0','0','$city','$state','0','$lat','$lng','$pannumber','$gstnumber','$date','$datetime','$userid','$stockistid','$areaid','$datetime')") or die(mysqli_error($con));
	
	$createid=mysqli_insert_id($con);
	$query="";
	if($usertype=="2")
	{
	  $query="insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,feedback,Latitude,Longitude,visittype) values('$userid','$createid','Super Stockist Create','$battery%','$date','$time','New Super Stockist Create id is $empcode','$lat','$lng','2')";
	}
	else
	{
	  $query="insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,feedback,Latitude,Longitude,visittype) values('$userid','$createid','Distributor Create','$battery%','$date','$time','New Distributor Create id is $empcode','$lat','$lng','3')";
	}
	
	mysqli_query($con,$query);
	
	if(mysqli_affected_rows($con)>0)
	{	
	    $response="success";
		echo $response;
	}
	else
	{
		$response="error";
	  //$response["message"]=mysqli_error($con);
		echo $response;
	}	
}

?>