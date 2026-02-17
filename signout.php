<?php
     session_start();
     session_destroy();
	 header("loaction:login?logout");
	 echo"<script> window.location='login?logout'; </script>";
?>