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
  
  if(isset($_GET['insert']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
	  $query = "select name from regions where `name` = '$name'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res) > 0 ){
			echo "name";
			return;
	  }
	  
	  $name=ucwords($name);

	 
	mysqli_query($con,"insert into `regions` (name,city_id,state_id) values('$name','$city_id','$state_id')") or die(mysqli_error($con));
	
	if(mysqli_affected_rows($con)>0)
	{
	       echo"success";
	  
	}
	else
	  {
        echo"error";
	  }
	
  }
  
  
  if(isset($_GET['cities'])){
      $id=$_GET['s_id'];
        $query="SELECT id,city FROM cities Where state_id='$id' order by city ";
           $res=mysqli_query($con,$query);
	 $response=array();
	 
	 	 while($row=mysqli_fetch_array($res))
	     {
		 		 $rr=array("id"=>$row["id"],"city"=>$row["city"]);
		        $response[]=$rr;
           }	 
	 $data=json_encode($response);
	 echo $data;
      
      
  }

  if(isset($_GET['regions'])){
      $id=$_GET['s_id'];
        $query="SELECT id,name FROM regions Where city_id='$id' order by name ";
           $res=mysqli_query($con,$query);
	 $response=array();
	 
	 	 while($row=mysqli_fetch_array($res))
	     {
		 		 $rr=array("id"=>$row["id"],"name"=>$row["name"]);
		        $response[]=$rr;
           }	 
	 $data=json_encode($response);
	 echo $data;
      
      
  }




    else if(isset($_GET['deleteregon']))
    { $id=$_GET['id'];
    	if(!empty($id)){
        $result=mysqli_query($con," DELETE FROM `regions` WHERE id='$id'");
        if($result) {
  		header('Location: ' . $_SERVER['HTTP_REFERER']);
  		exit;
       }	
    	}
    }

  else if(isset($_GET['show']))
  {
	  $query="SELECT regions.name,regions.status,regions.id,cities.city,states.name as staename  FROM `regions` JOIN states on regions.state_id= states.id join cities on regions.city_id=cities.id  order by regions.name ";
	  if($useremail=="admin@vivans.co.in"||$useremail=="rohit@fricbergen.com"||$useremail=="vivek@vivans.co.in")
	  {
         $query="SELECT regions.name,regions.status,regions.id,cities.city,states.name as staename  FROM `regions` JOIN states on regions.state_id= states.id join cities on regions.city_id=cities.id order by  regions.name";
	  }

     $res=mysqli_query($con,$query);
	 $response=array();
	 

	 $result=mysqli_query($con,"select a.region , COUNT( DISTINCT o.id) AS total_outlet_count , COUNT( DISTINCT a.id) AS total_route_count from outlets o JOIN area a ON  o.routeid=a.id  GROUP BY a.region");
	 $outlets=mysqli_fetch_array($result);

	$arraylastvist= $arrayoutlate = array();
	 while($outlets=mysqli_fetch_array($result))
	 {
	   $arrayOutlet[$outlets['region']]=$outlets['total_outlet_count'];
	   $arrayRoute[$outlets['region']]=$outlets['total_route_count'];
	 }



	 while($row=mysqli_fetch_array($res))
	 {
				$rr=array(		"id"=>$row["id"],
								"name"=>$row["name"],
								"city"=>$row["city"],
								"staename"=>$row["staename"],
								"no_routes"=>@$arrayRoute[$row["id"]]??0,
								"no_outlet"=>@$arrayOutlet[$row["id"]]??0
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
	  
/*	  $query = "select name from regions where  name = '$name'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "name";
			return;
	  }*/
	  
	      //$filename=$pname.$pshort.".jpg";
	      
	      	  $name=ucwords($name);
	      mysqli_query($con,"update regions set name='$name',city_id='$city_id',state_id='$state_id' where id='$id'") or die(mysqli_error($con));
	      if(mysqli_affected_rows($con)>0)
       	  {
	          echo"success";  
	      }
		  else
		  {
			  echo"No changes affected...";
		  }
	   
  }


?>