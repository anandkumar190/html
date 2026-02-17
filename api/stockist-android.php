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

  
   
  if(isset($_GET['insert']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
	  //var_dump($_POST);
	  $empcode=genrateId();  
	  $query = "select email from employees where email = '$empemail' and usertype='2'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empemail";
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and usertype='2'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empcontact";
			return;
	  }
	 $filename=$_FILES['empimage']['name'];
	 $tmpname=$_FILES['empimage']['tmp_name'];
	 $filesize=$_FILES['empimage']['size'];
	 $filetype=$_FILES['empimage']['type'];
	if($filetype!="image/jpg" && $filetype!="image/png" && $filetype!="image/jpeg")
	{
	  echo"Please Upload Images(PNG,JPG & JPEG) Files Only...";
	  return;
	}
	if($filesize>800000)
	{
	  echo"Image can't be Greater than 800KB .";
	  return;
	}
	
	$filename=$empname.$empcode.".jpg";
	mysqli_query($con,"insert into employees(image,name,empid,email,contact,address,designationid,roleid,managerid,usertype,password,salary,commission,city,state,reportsto,latitude,longitude,battery,region,doj,creationdate,createdby,stockistid,areaid,lastlogin) values('$filename','$empname','$empcode','$empemail','$empcontact','$empaddress','0','0','0','$usertype',password('$emppass'),'0','0','$city','$state','0','$lat','$lng','$pannumber','$gstnumber','$datetime','$datetime','$userid','$stockistid','$areaid','$datetime')") or die(mysqli_error($con));
	
	mysqli_query($con,"insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,feedback) values('$userid','0','Stockist Create','$battery','$date','$time','New Stockist Create id is $empcode')");
	
	if(mysqli_affected_rows($con)>0)
	{
		
	  if(move_uploaded_file($tmpname,"../imgusers/".$filename))
	  {
	    echo"success";
	  }
	  else
	  {
        echo"error";
	  }
	}
	
  }

  if(isset($_GET['show']))
  {
    
     extract($_REQUEST);
	 //$res=mysqli_query($con,"select * from outlets where areaid='$areaid'");
	 // $lat=truncate_number($lat,0);
	 // $lng=truncate_number($lng,0);
	  	
	
	 $res=mysqli_query($con,"select e.id, e.image,e.name, e.empid, e.email, e.contact, e.address, e.usertype, e.password, e.salary, e.commission,e.city, e.state, e.latitude, e.longitude, e.region, e.doj,e.dol,e.reportsto,e.stockistid,e.battery as 'panno',e.region as'gstno',e.areaid,e.creationdate,e.lastlogin,e.createdby from  employees e where e.usertype='2' order by lastlogin desc");
	 
	 $response=array();
	 
	while($row=mysqli_fetch_array($res))
	 {
       $rr = array("id"=>$row["id"],"name"=>$row["name"],"empid"=>$row["empid"],"email"=>$row["email"],"usertype"=>$row["usertype"],"contact"=>$row["contact"],"address"=>$row["address"],"salary"=>$row["salary"],"commission"=>$row["commission"],"city"=>$row["city"],"state"=>$row["state"],"locality"=>$row["city"],"latitude"=>$row["latitude"],"longitude"=>$row["longitude"],"panno"=>$row["panno"],"gstno"=>$row["gstno"],"image"=>$row["image"],"lastlogin"=>$row["lastlogin"],"creationdate"=>$row["creationdate"],"createdby"=>$row["createdby"],"areaid"=>$row["areaid"]);
		 $response[]=	$rr;
     }
	   $data=array();
	   $data["data"]=$response;	 
	   echo json_encode($data);
	 
  }

  if(isset($_GET['edit']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
	  //var_dump($_POST);
	  
	  $query = "select empid from employees where  empid = '$empcode' && id!='$id' and usertype='2'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empcode";
			return;
	  }
	  $query = "select email from employees where email = '$empemail' and id!='$id' and usertype='2'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empemail";
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and id!='$id' and usertype='2'";
	  $res=mysqli_query($con,$query);
	  if(mysqli_num_rows($res)> 0 ){
			echo "empcontact";
			return;
	  }
	
	 if(isset($_FILES['empimage']['name']) && $_FILES['empimage']['name']!="" && !isset($_POST['empimage']) )
	  {
	     $filename=$_FILES['empimage']['name'];
	     $tmpname=$_FILES['empimage']['tmp_name'];
	     $filesize=$_FILES['empimage']['size'];
	     $filetype=$_FILES['empimage']['type'];
	     
		 if($filetype!="image/jpg" && $filetype!="image/png" &&   $filetype!="image/jpeg")
	     {
	       echo"Please Upload Images(PNG,JPG & JPEG) Files Only...";
 	       return;
      	 }
	     if($filesize>800000)
	     {
	       echo"Image can't be Greater than 800KB .";
	       return;
	     }
		  
	      $filename=$empname.$empcode.".jpg";
		  
		  if(file_exists("../imgusers".$filename))
		  {
			  unlink("../imgusers".$filename);
		  }
		  
	      mysqli_query($con,"update  employees set name='$empname',empid='$empcode',email='$empemail',contact='$empcontact',address='$empaddress',designationid='$empdesignation',roleid='$emprole',managerid='$empmanager',reportsto='$empreportto',salary='$empsalary',commission='$empcomm',doj='$empdoj',dol='$empdol',image='$filename',lastlogin='$datetime' where id='$id'") or die(mysqli_error($con));
	      if(mysqli_affected_rows($con)>0)
       	  {
	       if(move_uploaded_file($tmpname,"../imgusers/".$filename))
	        {
	          echo"success";
	        }
	       else
	        {
             echo"Image error";
	        }
	      }
		  else
		  {
			 if(move_uploaded_file($tmpname,"../imgusers/".$filename))
	        {
	          echo"success";
	        }
	       else
	        {
             echo"Image error";
	        } 
		  }
	   }
	   else
	   {
		   
	      //$filename=$pname.$pshort.".jpg";
		  
	      mysqli_query($con,"update  employees set name='$empname',empid='$empcode',email='$empemail',contact='$empcontact',address='$empaddress',designationid='$empdesignation',roleid='$emprole',managerid='$empmanager',reportsto='$empreportto',salary='$empsalary',commission='$empcomm',doj='$empdoj',dol='$empdol',lastlogin='$datetime' where id='$id'") or die(mysqli_error($con));
	      if(mysqli_affected_rows($con)>0)
       	  {
	          echo"success";  
	      }
		  else
		  {
			  echo"No changes affected...";
		  }
	   }
  }


?>