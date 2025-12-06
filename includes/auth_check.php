<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Check for session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    // Session expired, destroy session and redirect to login
    session_unset();
    session_destroy();
    header("Location: Login.php?timeout=1");
    exit();
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();
?>