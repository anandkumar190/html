<?php

  session_start();

  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

if (!isset($_SESSION['tittu'])) {
    header("Location: /login.php");
    exit;
}

header("Location: /home.php");
exit;

?>