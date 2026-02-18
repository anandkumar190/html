<?php 
//Constants for database connection
   ob_start();
   date_default_timezone_set('Asia/Kolkata');

	define('DB_HOST','localhost');
/*<<<<<<< HEAD*/
	define('DB_USER','root');
	define('DB_PASS',''); 
/*=======*/
	//define('DB_USER','vivandgo_cart');//vivandgo_cart  cartroute
	//define('DB_PASS','cartroute#123456'); //cartroute#123456
/*>>>>>>> 33844e7bad3ffdc0811aed4112ae5646b033ba4f*/
	//define('DB_NAME','vivanwmp_cartroute');
	define('DB_NAME','newdb_20_01_2026');
	$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME) or die('Unable to connect');


 ?>
