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

	  $query1="update parduct_sub_cat set status='$status' where id='$id' ";
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



     if(isset($_GET['sub_cats'])){
         $id=$_GET['c_id'];
           $query="SELECT id,name FROM parduct_sub_cat Where cat_id='$id' order by name ";
              $res=mysqli_query($con,$query);
   	 $response=array();
   	 
   	 	 while($row=mysqli_fetch_array($res))
   	     {
   		 		 $rr=array("id"=>$row["id"],"name"=>$row["name"]);
   		        $response[]=$rr;
              }	 
            
   	 $data=json_encode($response);
   	 echo $data;
         die();  
     }
   
     
  if(isset($_GET['insert']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	


	  $query = "select name from parduct_sub_cat where `name` = '$name'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res) > 0 ){
			echo "name";
			return;
	  }

	 	  $name=ucwords($name);
	mysqli_query($con,"insert into `parduct_sub_cat`(name,cat_id ,pmrp,punit ,prate, cunit, cmrp, unit_no, discount ,prodective_cell) values('$name','$cat_id','$pmrp','$punit','$prate','$cunit','$cmrp','$unit_no','$discount','$prodective_cell')") or die(mysqli_error($con));
	
	if(mysqli_affected_rows($con)>0)
	{
	       echo"success";
	  
	}
	else
	  {
        echo"error";
	  }
	
  }
    else if(isset($_GET['deletescat']))
    { $id=$_GET['id'];
    	if(!empty($id)){
        $result=mysqli_query($con," DELETE FROM `parduct_sub_cat` WHERE id='$id'");
        if($result) {
  		header('Location: ' . $_SERVER['HTTP_REFERER']);
  		exit;
       }	
    	}
    }

    else if(isset($_GET['edit']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
// var_dump($_POST);
	  
/*	  $query = "select city from parduct_sub_cat where  city = '$name'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "name";
			return;
	  }*/	  
	      //$filename=$pname.$pshort.".jpg";
	      
	      	  $name=ucwords($name);
	     $mysqli= mysqli_query($con,"update parduct_sub_cat set name='$name',cat_id='$cat_id',pmrp='$pmrp',prate='$prate',cunit='$cunit',cmrp='$cmrp',unit_no='$unit_no',punit='$punit',discount='$discount',prodective_cell='$prodective_cell' where id='$id'") or die(mysqli_error($con));
	      if(mysqli_affected_rows($con)>0)
       	  {
	          echo"success";  
	      }
		  else
		  {
			  echo"No changes affected...";
		  }
	   
  }else{
       $query="SELECT parduct_sub_cat.name AS subcat,parduct_sub_cat.id,parduct_sub_cat.status,product_cat.name AS cat ,parduct_sub_cat.pmrp,parduct_sub_cat.punit,parduct_sub_cat.prate,parduct_sub_cat.cunit,parduct_sub_cat.cmrp,parduct_sub_cat.unit_no,parduct_sub_cat.discount ,parduct_sub_cat.prodective_cell FROM parduct_sub_cat JOIN product_cat on parduct_sub_cat.cat_id=product_cat.id ORDER BY parduct_sub_cat.prodective_cell ASC "  ;
	  
          $res=mysqli_query($con,$query);
	      $response=array();
     
/*
pmrp
punit
prate
cunit
cmrp
unit_no
discount*/
	 
	 $unit=array();
	 
	 $query="SELECT `id`, `name` FROM `sku_unit`"  ;
	  
       $units=mysqli_query($con,$query);
      while($resut=mysqli_fetch_array($units)){
          $unit[$resut['id']]=$resut['name'];
          
      }   
	     
	 
	 
	 
	 while($row=mysqli_fetch_array($res))
	 {
	 	
		 $rr=array("id"=>$row["id"],"name"=>$row["subcat"],"cat"=>$row["cat"],"status"=>$row["status"],"pmrp"=>$row["pmrp"],"punit"=>$unit[$row["punit"]],"prate"=>$row["prate"],"prodective_cell"=>$row["prodective_cell"], "cunit"=>$unit[$row["cunit"]],"cmrp"=>$row["cmrp"],"unit_no"=>$row["unit_no"],"discount"=>$row["discount"]);
		 $response[]=$rr;
     }
     
  
	 $data=json_encode($response);
	 echo $data;
      
  }
  
  
 

?>