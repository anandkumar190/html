<?php
session_start();
if(!isset($_SESSION['tittu']))
{
	echo"invalid";
	exit();
}


  $userid=$_SESSION['id'];
  $usertype=$_SESSION['usertype'];
  $useremail=$_SESSION['tittu'];
  require('../connect.php');
  
  $time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d");
 
 
   
   if(isset($_GET['statestatus']))
   {
      $status=$_GET['status'];
	  $id=$_GET['id'];

	  $query1="update cities set status='$status' where id='$id' ";
	  mysqli_query($con,$query1) or die(mysqli_error($con));
	  
	  if(mysqli_affected_rows($con)>0)
	  {  
		echo"success"; 
	  }
	  else
	  {
	    echo "error";
	  }
   }
   
     
  if(isset($_GET['insert']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
	  $query = "select city from cities where `city` = '$name'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res) > 0 ){
			echo "name";
			return;
	  }

	 	  $name=ucwords($name);
	mysqli_query($con,"insert into `cities`(city,state_id) values('$name','$state_id')") or die(mysqli_error($con));
	
	if(mysqli_affected_rows($con)>0)
	{
	       echo"success";
	  
	}
	else
	  {
        echo"error";
	  }
	
  }
    else if(isset($_GET['deletecityid']))
    { $id=$_GET['id'];
    
    	if(!empty($id)){
        $result=mysqli_query($con," DELETE FROM `cities` WHERE id='$id'");
        if($result) {
  		header('Location: ' . $_SERVER['HTTP_REFERER']);
  		exit;
       }	
    	}
  		header('Location: ' . $_SERVER['HTTP_REFERER']);

    }

  else if(isset($_GET['edit']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
	  //var_dump($_POST);
	  
/*	  $query = "select city from cities where  city = '$name'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "name";
			return;
	  }*/
	  
	      //$filename=$pname.$pshort.".jpg";
	      	  $name=ucwords($name);
	      mysqli_query($con,"update cities set city='$name',state_id='$state_id' where id='$id'") or die(mysqli_error($con));
	      if(mysqli_affected_rows($con)>0)
       	  {
	          echo"success";  
	      }
		  else
		  {
			  echo"No changes affected...";
		  }
	   
  }else{
       $query="SELECT cities.city,cities.id,cities.status,states.name FROM cities JOIN states on cities.state_id=states.id";
	  
          $res=mysqli_query($con,$query);
	      $response=array();
     

	 
	 while($row=mysqli_fetch_array($res))
	 {
		 		 $rr=array("id"=>$row["id"],"status"=>$row["status"],"name"=>$row["city"],"state"=>$row["name"]);
		 $response[]=$rr;
     }
     
  
	 $data=json_encode($response);
	 echo $data;
      
  }
  
  
 

?>