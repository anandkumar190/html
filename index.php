<?php
session_start();

if (empty($_SESSION['tittu'])) {
    header("Location: login");
    exit;
}

header("Location: home");
exit;
