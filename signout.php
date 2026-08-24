<?php
session_start();
session_destroy();
header("Location: login?logout");
echo "<script> window.location='login?logout'; </script>";
?>