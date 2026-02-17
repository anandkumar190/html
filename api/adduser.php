<?php
 session_start();
if(!isset($_SESSION['tittu']))
{
	echo"invalid";
	exit();
}



  $userid=@$_SESSION['id']??1;
  $usertype=@$_SESSION['usertype']??1;
  $useremail=@$_SESSION['tittu']??"admin@vivans.co.in";

  
  require('../connect.php');
  
  $time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d");
  
  if(isset($_GET['insert']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
// 	  var_dump($_POST);
// die;
	  $query = "select empid from employees where  empid = '$empcode' and usertype='1'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empcode";
			return;
	  }
	  $query = "select email from employees where email = '$empemail' and usertype='1'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empemail";
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and usertype='1'";
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
	mysqli_query($con,"insert into employees(image,name,empid,email,contact,address,designationid,roleid,managerid,usertype,password,salary,commission,doj,reportsto,creationdate,createdby,lastlogin) values('$filename','$empname','$empcode','$empemail','$empcontact','$empaddress','$empdesignation','$emprole','$empmanager','1',password('$emppass'),'$empsalary','$empcomm','$empdoj','$empreportto','$userid','$datetime','$datetime')") or die(mysqli_error($con));
	
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
	  $query="select e.id,e.status, e.image, e.name, e.empid, e.email, e.contact, e.address, e.designationid, e.roleid, e.managerid, e.usertype, e.password, e.salary, e.commission, e.city, e.state, e.latitude, e.longitude, e.region, e.doj, e.dol, e.reportsto, e.creationdate ,d.name as designation,r.name as role from  employees e left join designation d on e.designationid=d.id join roles r on e.roleid=r.id where e.usertype='$usertype' and e.id='$userid' union select e.id, e.image, e.name, e.empid, e.email, e.contact, e.address, e.designationid, e.roleid, e.managerid, e.usertype, e.password, e.salary, e.commission, e.city, e.state, e.latitude, e.longitude, e.region, e.doj, e.dol, e.reportsto, e.creationdate ,d.name as designation,r.name as role from  employees e left join designation d on e.designationid=d.id left join roles r on e.roleid=r.id where e.usertype='$usertype' and (e.reportsto='$userid' || e.managerid='$userid')";
	  if($useremail=="admin@vivans.co.in"||$useremail=="rohit@fricbergen.com"||$useremail=="vivek@vivans.co.in")
	  {
		  $query="select e.id,e.status, e.image, e.name, e.empid, e.email, e.contact, e.address, e.designationid, e.roleid, e.managerid, e.usertype, e.password, e.salary, e.commission, e.city, e.state, e.latitude, e.longitude, e.region, e.doj, e.dol, e.reportsto, e.creationdate ,d.name as designation,r.name as role from  employees e left join designation d on e.designationid=d.id left join roles r on e.roleid=r.id where e.usertype='$usertype'";
	  }
     $res=mysqli_query($con,$query);
	 $response=array();
	 

	 while($row=mysqli_fetch_array($res))
	 {
		 		 $rr=array("id"=>$row["id"],"status"=>$row["status"],"name"=>$row["name"],"empid"=>$row["empid"],"email"=>$row["email"],"contact"=>$row["contact"],"address"=>$row["address"],"designation"=>$row["designation"],"role"=>$row["role"],"managerid"=>$row["managerid"],"salary"=>$row["salary"],"commission"=>$row["commission"],"city"=>$row["city"],"latitude"=>$row["latitude"],"longitude"=>$row["longitude"],"region"=>$row["region"],"doj"=>$row["doj"],"dol"=>$row["dol"],"reportsto"=>$row["reportsto"],"image"=>$row["image"]);
		 $response[]=$rr;
     }	 
	 $data=json_encode($response);
	 echo $data;
  }

  else if (isset($_GET['delete_user']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Ensures ID is an integer
    $stmt = $con->prepare("DELETE FROM `employees` WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
    }

	header('Location: ' . $_SERVER['HTTP_REFERER'], true, 303);
	exit;
}
  else if(isset($_GET['edit']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
	  //var_dump($_POST);
	  
	  $query = "select empid from employees where  empid = '$empcode' && id!='$id' and usertype='1'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empcode";
			return;
	  }
	  $query = "select email from employees where email = '$empemail' and id!='$id' and usertype='1'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empemail";
			return;
	  }
// 	  $query = "select contact from employees where contact = '$empcontact' and id!='$id' and usertype='1'";
// 	  $res=mysqli_query($con,$query);
// 	  if(mysqli_num_rows($res)> 0 ){
// 			echo "empcontact";
// 			return;
// 	  }
	
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
		  
	      mysqli_query($con,"update  employees set name='$empname',empid='$empcode',email='$empemail',contact='$empcontact',address='$empaddress',designationid='$empdesignation',roleid='$emprole',managerid='$empmanager',reportsto='$empreportto',salary='$empsalary',commission='$empcomm',doj='$empdoj',dol='$empdol',image='$filename',lastlogin='$datetime',password=password('$emppass') where id='$id'") or die(mysqli_error($con));
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
	      mysqli_query($con,"update  employees set name='$empname',empid='$empcode',email='$empemail',contact='$empcontact',address='$empaddress',designationid='$empdesignation',roleid='$emprole',managerid='$empmanager',reportsto='$empreportto',salary='$empsalary',commission='$empcomm',doj='$empdoj',dol='$empdol',lastlogin='$datetime',password=password('$emppass') where id='$id'") or die(mysqli_error($con));
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