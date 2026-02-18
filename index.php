<?php

  session_start();

  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

   if(!isset($_SESSION['tittu']))
   {
	   header("loaction:login");
	   echo"<script>window.location='login';</script>";
	   exit();
   }
   else
   {
	   header("loaction:home");
	  //  echo"<script>window.location='home';</script>";
	   exit();
   }
?>