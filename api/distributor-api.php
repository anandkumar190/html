<?php

session_start();
if(!isset($_SESSION['tittu']))
{
	echo"invalid";
	exit();
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


  $userid=$_SESSION['id'];
  $usertype=$_SESSION['usertype'];
  require('../connect.php');

$time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d");
  if(isset($_GET['insert']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
      $empcode=genrateId($con);	
	  //var_dump($_POST);
	  
	  $query = "select empid from employees where  empid = '$empcode' and usertype='3'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empcode";
			return;
	  }
	  $query = "select email from employees where email = '$empemail' and usertype='3'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empemail";
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and usertype='3'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empcontact";
			return;
	  }

	
	$filename="image";
	mysqli_query($con,"insert into employees(image,name,contactperson,sortname,empid,email,contact,password,address,designationid,roleid,managerid,usertype,salary,commission,city,state,reportsto,latitude,longitude,battery,region,doj,creationdate,createdby,lastlogin) values('$filename','$empname','$empcontactname','$empsortname','$empcode','$empemail','$empcontact',PASSWORD('$empcontact'),'$empaddress','0','0','0','3','0','0','$empcity','$empstate','0','$emplat','$emplng','','','$datetime','$datetime','$userid','$datetime')") or die(mysqli_error($con));
	
	if(mysqli_affected_rows($con)>0)
	{
	  echo"success";

	}else
	  {
        echo"error";
	  }
	
  }

  else if(isset($_GET['show']))
  {
	$query = "SELECT 
            e.id, 
            e.name, 
            e.email, 
            e.contactperson, 
            e.contact, 
            e.usertype,
			e.address, 
            c.city, 
            s.name AS state 
          FROM employees e 
          LEFT JOIN states s ON e.state = s.id 
          LEFT JOIN cities c ON e.city = c.id 
          WHERE e.usertype = '3'";
	$res=mysqli_query($con,$query);

	if (!$res) {
		die("Query failed: " . mysqli_error($con));
	}

		$response=array();   
		$result=mysqli_query($con,"select a.distributor_id,
					COUNT( DISTINCT o.id) AS total_outlet_count ,
					COUNT( DISTINCT a.id) AS total_route_count 
					from outlets o 
					left JOIN area a ON o.routeid=a.id
					GROUP BY a.distributor_id");

		$arrayRoute= $arrayOutlet = array();

		while($outlets=mysqli_fetch_array($result))
		{
		$arrayOutlet[$outlets['distributor_id']]=$outlets['total_outlet_count'];
		$arrayRoute[$outlets['distributor_id']]=$outlets['total_route_count'];
		}

	 while($row=mysqli_fetch_array($res))
	 {
		 		 $rr=array(
					"id"=>$row["id"],
					"name"=>$row["name"],
					"email"=>$row["email"],
					"contact"=>$row["contact"],
					"address"=>$row["address"],	
					"city"=>$row["city"],
					"state"=>$row["state"],
					"contactperson"=>$row["contactperson"],
					"no_of_outlets"=>@$arrayOutlet[$row["id"]]??0,
					"no_of_routes"=>@$arrayRoute[$row["id"]]??0,
				
				);
		 $response[]=$rr;
     }	 
	 $data=json_encode($response);
	 echo $data;
  }

  else if(isset($_GET['edit']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
	  //var_dump($_POST);
	  
	  //$query = "select empid from employees where  empid = '$empcode' && id!='$id' and usertype='3'";
	  //echo($pshort);
	  //$res=mysqli_query($con,$query);
	  //if( mysqli_num_rows($res)> 0 ){
	//		echo "empcode";
	//		return;
	//  }
	  $query = "select email from employees where email = '$empemail' and id!='$id' and usertype='3'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empemail";
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and id!='$id' and usertype='3'";
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
		  
	      $filename=$empname.$id.".jpg";
		  
		  if(file_exists("../imgusers".$filename))
		  {
			  unlink("../imgusers".$filename);
		  }
		  
	      	      mysqli_query($con,"update  employees set name='$empname',
				  email='$empemail',
				  contact='$empcontact',
				  password=PASSWORD('$empcontact'),
				  address='$empaddress',
				  latitude='$emplat',
				  longitude='$emplng',
				  city='$empcity',
				  state='$empstate',
				  lastlogin='$datetime',
				  contactperson='$empcontactname',
				  sortname='$empsortname' where id='$id'") or die(mysqli_error($con));

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

	      	mysqli_query($con,"update  employees set name='$empname',
			email='$empemail',
			contact='$empcontact',
			password=PASSWORD('$empcontact'),
			address='$empaddress',
			city='$empcity',
			state='$empstate',
			latitude='$emplat',
			longitude='$emplng',
			lastlogin='$datetime',
			contactperson='$empcontactname',
			sortname='$empsortname'
			where id='$id'") or die(mysqli_error($con));

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
  
  
if(isset($_GET['import']))
  {
	 $filetype=$_FILES["file1"]["type"];	 
     $filename=$_FILES["file1"]["tmp_name"];
     if($_FILES["file1"]["size"] > 0)
	 {
		 $file = fopen($filename, "r");
        $count=0;
        while (($areaData = fgetcsv($file, 10000, ",")) !== FALSE)
        {
			$imgpath="deafult-user.png";
			$empcode=genrateId($con);  
			$count++;
			if($count>1)
			{
				$areaid=getArea($areaData[8],$areaData[9],$con);
				$stockistid=getStockistId($areaData[12],$con);
				mysqli_query($con,"insert into employees(image,name,empid,email,contact,address,designationid,roleid,managerid,usertype,salary,commission,city,state,reportsto,latitude,longitude,battery,region,doj,creationdate,createdby,stockistid,areaid,lastlogin) values('$imgpath','$areaData[0]','$empcode','$areaData[1]','$areaData[2]','$areaData[3]','0','0','0','3','0','0','$areaData[4]','$areaData[5]','0','$areaData[10]','$areaData[11]','$areaData[7]','$areaData[6]','$datetime','$datetime','$userid','$stockistid','$areaid','$datetime')") or die(mysqli_error($con));
			}
   		}
		fclose($file);
        echo "success";
	  }
	  else
	  {
		echo "error"; 
	  }	 
  }
  
  function getArea($areaname,$region,$con)
  {
	 $res=mysqli_query($con,"select id from area where area='$areaname' and region='$region'");
	 $row=mysqli_fetch_array($res);
	 return $row["id"];
  }
  
  function getStockistId($stockistid,$con)
  {
	 $res=mysqli_query($con,"select id from employees where empid='$stockistid'");
	 $row=mysqli_fetch_array($res);
	 return $row["id"];
  }
  
   if(isset($_GET['delete']))
  {
	$ids=$_POST['ids'];
	foreach($ids as $id)
	{
      mysqli_query($con,"delete from employees where id='$id'");
	}
     echo "Distributors Delete Succesfully...";
  }  

?>