<?php 
   include("connect.php");
   extract($_REQUEST);
   mysqli_query($con,"update outletorder set orderstatus='cancel' where orderid='$cancelid'");
   if(mysqli_affected_rows($con)>0)
   {
	 echo"<script> window.location='orders'; </script>";   
   }

?>