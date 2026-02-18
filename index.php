<?php
session_start();

if (empty($_SESSION['tittu'])) {
    header("Location: /login.php");
    exit;
}

header("Location: /home.php");
exit;
