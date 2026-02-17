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
  if (isset($_GET["show"])) {

	$query="select * From states order by name "; 
     $res=mysqli_query($con,$query);
	 $response=array();  
	 $result=mysqli_query($con,"select c.state_id,
								COUNT( DISTINCT c.id) AS total_city_count ,
								COUNT( DISTINCT r.id) AS total_region_count ,
								COUNT( DISTINCT o.id) AS total_outlet_count ,
								COUNT( DISTINCT a.id) AS total_route_count 
								from outlets o 
								left JOIN area a ON o.routeid=a.id
								left JOIN regions r On a.region=r.id
								left JOIN cities c On r.city_id=c.id
								GROUP BY c.state_id");
	 
	$arrayCity=$arrayReion=$arraylastvist= $arrayoutlate = array();

	 while($outlets=mysqli_fetch_array($result))
	 {

	   $arrayCity[$outlets['state_id']]=$outlets['total_city_count'];
	   $arrayOutlet[$outlets['state_id']]=$outlets['total_outlet_count'];
	   $arrayRoute[$outlets['state_id']]=$outlets['total_route_count'];
	   $arrayReion[$outlets['state_id']]=$outlets['total_region_count'];
	 }

	 
	 while($row=mysqli_fetch_array($res))
	 {
		 		 $rr=array("id"=>$row["id"],
				 "status"=>$row["status"],
				 "name"=>ucwords(strtolower($row["name"])),
				 "total_city_count"=>@$arrayCity[$row["id"]]??0,
				 "total_outlet_count"=>@$arrayOutlet[$row["id"]]??0,
				 "total_route_count"=>@$arrayRoute[$row["id"]]??0,
				 "total_region_count"=>@$arrayReion[$row["id"]]??0,
				);
		 $response[]=$rr;
     }	 
	 $data=json_encode($response);
	 echo $data;
	 	
	}
	 

	 if(isset($_GET['edit'])) {
	
		extract($_POST);
		$query1="update states set name='$name' where id='$id' ";
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


   
   if(isset($_GET['statestatus']))
   {
      $status=$_GET['status'];
	  $id=$_GET['id'];

	  $query1="update states set status='$status' where id='$id' ";
	  mysqli_query($con,$query1) or die(mysqli_error($con));
	  
	  if(mysqli_affected_rows($con)>0)
	  {  
		echo"success"; 
	  }
	  else
	  {
	    echo "error";
	  }
   }  else if(isset($_GET['deletestate']))
  { $id=$_GET['id'];
  	if(!empty($id)){
      $result=mysqli_query($con," DELETE FROM `states` WHERE id='$id'");
      if($result) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
     }	
  	}
  }

 

?>