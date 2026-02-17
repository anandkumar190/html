<?php
session_start();
if(!isset($_SESSION['tittu']))
{
	echo"invalid";
	exit();
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
	
	  //var_dump($_POST);
	   $empcode=genrateSSId($con);  
	  $query = "select empid from employees where  empid = '$empcode' and usertype='2'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empcode";
			return;
	  }
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
	mysqli_query($con,"insert into employees(image,name,empid,email,contact,address,designationid,roleid,managerid,usertype,salary,commission,city,state,reportsto,latitude,longitude,battery,region,doj,creationdate,createdby,stockistid,areaid,lastlogin) values('$filename','$empname','$empcode','$empemail','$empcontact','$empaddress','0','0','0','2','0','0','$empcity','$empstate','0','$emplat','$emplng','$emppanno','$empgstno','$datetime','$datetime','$userid','0','$emparea','$datetime')") or die(mysqli_error($con));
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

  else if(isset($_GET['show']))
  {
     $res=mysqli_query($con,"select e.id, e.image, e.name, e.empid, e.email, e.contact, e.address, e.usertype, e.password, e.salary, e.commission, e.city, e.state, e.latitude, e.longitude, e.region, e.doj, e.dol, e.reportsto, e.creationdate ,e.stockistid,e.battery as 'panno',e.region as'gstno' from  employees e where e.usertype='2'");
	 $response=array();
	 
	 while($row=mysqli_fetch_array($res))
	 {
		 		 $rr=array("id"=>$row["id"],"name"=>$row["name"],"empid"=>$row["empid"],"email"=>$row["email"],"contact"=>$row["contact"],"address"=>$row["address"],"salary"=>$row["salary"],"commission"=>$row["commission"],"city"=>$row["city"],"latitude"=>$row["latitude"],"longitude"=>$row["longitude"],"panno"=>$row["panno"],"gstno"=>$row["gstno"],"image"=>$row["image"]);
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
	  
	 //$query = "select empid from employees where  empid = '$empcode' && id!='$id' and usertype='2'";
	  //echo($pshort);
	  //$res=mysqli_query($con,$query);
	  //if( mysqli_num_rows($res)> 0 ){
	//		echo "empcode";
	//		return;
	 // }
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
		  
	      $filename=$empname.$id.".jpg";
		  
		  if(file_exists("../imgusers".$filename))
		  {
			  unlink("../imgusers".$filename);
		  }
		  
	      mysqli_query($con,"update  employees set name='$empname',email='$empemail',contact='$empcontact',address='$empaddress',areaid='$emparea',image='$filename',latitude='$emplat',longitude='$emplng',$city='$empcity',state='$empstate',battery='$emppanno',region='$empgstno',lastlogin='$datetime' where id='$id'") or die(mysqli_error($con));
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
		  
	      mysqli_query($con,"update  employees set name='$empname',email='$empemail',contact='$empcontact',address='$empaddress',areaid='$emparea',city='$empcity',state='$empstate',battery='$emppanno',region='$empgstno',latitude='$emplat',longitude='$emplng',lastlogin='$datetime' where id='$id'") or die(mysqli_error($con));
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
  
   if(isset($_GET['delete']))
  {
	$ids=$_POST['ids'];
	foreach($ids as $id)
	{
      mysqli_query($con,"delete from employees where id='$id'");
	}
     echo "Stockist Delete Succesfully...";
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
			$empcode=genrateSSId($con);  
			$count++;
			if($count>1)
			{
				$areaid=getArea($areaData[8],$areaData[9],$con);
				mysqli_query($con,"insert into employees(image,name,empid,email,contact,address,designationid,roleid,managerid,usertype,salary,commission,city,state,reportsto,latitude,longitude,battery,region,doj,creationdate,createdby,stockistid,areaid,lastlogin) values('$imgpath','$areaData[0]','$empcode','$areaData[1]','$areaData[2]','$areaData[3]','0','0','0','2','0','0','$areaData[4]','$areaData[5]','0','$areaData[10]','$areaData[11]','$areaData[7]','$areaData[6]','$datetime','$datetime','$userid','0','$areaid','$datetime')") or die(mysqli_error($con));
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
  
  function getArea($areaname,$region,$cc)
  {
	 $res=mysqli_query($cc,"select id from area where area='$areaname' and region='$region'");
	 $row=mysqli_fetch_array($res);
	 return $row["id"];
  }
?>