<?php

  session_start();
   if(!isset($_SESSION['tittu']))
   {
	   header("loaction:login");
	   echo"<script>window.location='login';</script>";
	   exit();
   }
   else
   {
	   header("loaction:home");
	   echo"<script>window.location='home';</script>";
	   exit();
   }
?>