<?php
  require('../connect.php');
  $time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d");
  if(isset($_GET['insert']))
  {
	
	extract($_POST);
	$productids=explode(",",$productids);
	$units=explode(",",$units);
	$res=mysqli_query($con,"select distributorid from outlets where id='$outletid'");
	$row=mysqli_fetch_array($res); 
	$distributorid=$row[0];
	mysqli_query($con,"insert into outletorder(distributorid,empid,outletid,orderdate,ordertime,remark) values('$distributorid','$userid','$outletid','$date','$time','$remark')") or die(mysqli_error($con));
	 if(mysqli_affected_rows($con)>0)
	  {
		  $orderid=mysqli_insert_id($con);
		  for($i=0;$i<count($productids);$i++)
		  {
            mysqli_query($con,"insert into orderitem(orderid,productid,qty,date) values('$orderid','$productids[$i]','$units[$i]','$datetime')");			  
		  }
	     echo"success";	  
	  }
	 else
	    echo"error";
	
  }
  
  else if(isset($_GET['show']))
  {
     $res=mysqli_query($con,"select o.orderid,o.orderdate,o.ordertime,o.orderstatus,o.remark,o.distributorid,d.name as 'distributor',d.contact,d.address,e.name as 'employee',ot.name as 'outlet' from outletorder o join employees d on o.distributorid=d.id join employees e on e.id=o.empid join outlets ot on ot.id=o.outletid where orderstatus!='cancel' order by o.orderid desc");
	 $response=array();
	 while($row=mysqli_fetch_array($res))
	 {
		 $rr=array("id"=>$row["orderid"],"distributorid"=>$row["distributorid"],"distributor"=>$row["distributor"],"contact"=>$row["contact"],"address"=>$row["address"],"employee"=>$row["employee"],"outlet"=>$row["outlet"],"orderdate"=>$row["orderdate"],"ordertime"=>$row["ordertime"],"status"=>$row["orderstatus"],"remark"=>$row["remark"]);
		 $response[]=$rr;
     }	 
	 $data=json_encode($response);
	 echo $data;
  }
  
  else if(isset($_GET['showtoday']))
  {
     $res=mysqli_query($con,"select o.orderid,o.orderdate,o.ordertime,o.orderstatus,o.remark,o.distributorid,d.name as 'distributor',d.contact,d.address,e.name as 'employee',ot.name as 'outlet' from outletorder o join employees d on o.distributorid=d.id join employees e on e.id=o.empid join outlets ot on ot.id=o.outletid where o.orderdate='$date' and orderstatus!='cancel' order by o.orderid desc");
	 $response=array();
	 while($row=mysqli_fetch_array($res))
	 {
		 $rr=array("id"=>$row["orderid"],"distributorid"=>$row["distributorid"],"distributor"=>$row["distributor"],"contact"=>$row["contact"],"address"=>$row["address"],"employee"=>$row["employee"],"outlet"=>$row["outlet"],"orderdate"=>$row["orderdate"],"ordertime"=>$row["ordertime"],"status"=>$row["orderstatus"],"remark"=>$row["remark"]);
		 $response[]=$rr;
     }	 
	 $data=json_encode($response);
	 echo $data;
  }

  
  else if(isset($_GET['cancel']))
  {
     $res=mysqli_query($con,"select o.orderid,o.orderdate,o.ordertime,o.orderstatus,o.remark,o.distributorid,d.name as 'distributor',d.contact,d.address,e.name as 'employee',ot.name as 'outlet' from outletorder o join employees d on o.distributorid=d.id join employees e on e.id=o.empid join outlets ot on ot.id=o.outletid where orderstatus=='cancel' order by o.orderid desc");
	 $response=array();
	 while($row=mysqli_fetch_array($res))
	 {
		 $rr=array("id"=>$row["orderid"],"distributorid"=>$row["distributorid"],"distributor"=>$row["distributor"],"contact"=>$row["contact"],"address"=>$row["address"],"employee"=>$row["employee"],"outlet"=>$row["outlet"],"orderdate"=>$row["orderdate"],"ordertime"=>$row["ordertime"],"status"=>$row["orderstatus"],"remark"=>$row["remark"]);
		 $response[]=$rr;
     }	 
	 $data=json_encode($response);
	 echo $data;
  }
  
  else if(isset($_GET['canceltoday']))
  {
     $res=mysqli_query($con,"select o.orderid,o.orderdate,o.ordertime,o.orderstatus,o.remark,o.distributorid,d.name as 'distributor',d.contact,d.address,e.name as 'employee',ot.name as 'outlet' from outletorder o join employees d on o.distributorid=d.id join employees e on e.id=o.empid join outlets ot on ot.id=o.outletid where o.orderdate='$date' and orderstatus=='cancel' order by o.orderid desc");
	 $response=array();
	 while($row=mysqli_fetch_array($res))
	 {
		 $rr=array("id"=>$row["orderid"],"distributorid"=>$row["distributorid"],"distributor"=>$row["distributor"],"contact"=>$row["contact"],"address"=>$row["address"],"employee"=>$row["employee"],"outlet"=>$row["outlet"],"orderdate"=>$row["orderdate"],"ordertime"=>$row["ordertime"],"status"=>$row["orderstatus"],"remark"=>$row["remark"]);
		 $response[]=$rr;
     }	 
	 $data=json_encode($response);
	 echo $data;
  }




  else if(isset($_GET['edit']))
  {
	  extract($_POST);
	  
	  $query = "select productid from skus where productid = '$pshort' && id='$pid'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)<=0 ){
			$query = "select productid from skus where productid = '$pshort'";
	     //echo($pshort);
	     $res=mysqli_query($con,$query);
	     if( mysqli_num_rows($res)> 0 ){
	 		echo "get_value";
			return;
	    }
	  }
	  
	  //print_r($_FILES); 
	  //print_r($_POST);	  
	  //die();
	  
	  if(isset($_FILES['pimage']['name']) && $_FILES['pimage']['name']!="" && !isset($_POST['pimage']) )
	  {
		  
	     $filename=$_FILES['pimage']['name'];
	     $tmpname=$_FILES['pimage']['tmp_name'];
	     $filesize=$_FILES['pimage']['size'];
	     $filetype=$_FILES['pimage']['type'];
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
		  
	      $filename=$pname.$pshort.".jpg";
	      mysqli_query($con,"update  skus set productname='$pname',productid='$pshort',unit='$punit',mrp='$pmrp',rate='$prate',image='$filename' where id='$pid'") or die(mysqli_error($con));
		  
	      if(mysqli_affected_rows($con)>0)
       	  {
	       if(move_uploaded_file($tmpname,"../imgproduct/".$filename))
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
			if(move_uploaded_file($tmpname,"../imgproduct/".$filename))
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
		  
	      mysqli_query($con,"update skus set productname='$pname',productid='$pshort',unit='$punit',mrp='$pmrp',rate='$prate'  where id='$pid'") or die(mysqli_error($con));
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