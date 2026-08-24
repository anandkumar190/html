<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['tittu'])) {
    header("Location: login");
    exit;
}

// Render dashboard directly to avoid any redirect loops
require_once __DIR__ . '/home.php';
exit;
