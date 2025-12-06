<?php
/**
 * Logout Script
 * Properly destroys session and redirects to home page
 */

// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to home page with success message
header("Location: index.php?message=" . urlencode("You have been logged out successfully"));
exit();
?>