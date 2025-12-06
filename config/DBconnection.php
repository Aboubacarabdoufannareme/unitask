<?php
/**
 * Database Connection Configuration
 * MVC Structure - Config Layer
 */

// Database credentials - CHANGE THESE TO MATCH YOUR SERVER
define('DB_HOST', 'localhost');
define('DB_USER', 'fannareme.abdou');                    // Change to your MySQL username
define('DB_PASS', 'fa889033');                        // Change to your MySQL password
define('DB_NAME', 'webtech_2025A_fannareme_abdou');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die(json_encode(['success' => false, 'message' => 'Database connection failed. Please check your configuration.']));
}

// Set charset to UTF-8 to support all characters
$conn->set_charset("utf8mb4");

// Error reporting for development (disable in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * Function to sanitize input data
 * Prevents XSS attacks
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Function to generate CSRF token
 * Call this in forms to generate security token
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Function to verify CSRF token
 * Call this to validate form submissions
 */
function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Function to close database connection
 * Call this at the end of scripts if needed
 */
function close_connection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

// Note: Connection will be closed automatically at end of script
// But you can manually close it using close_connection() if needed
?>
