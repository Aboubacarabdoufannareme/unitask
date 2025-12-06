<?php
header('Content-Type: application/json');
session_start();

// Database credentials
$dbHost = 'localhost';
$dbUser = 'fannareme.abdou';
$dbPass = 'fa889033';
$dbName = 'webtech_2025A_fannareme_abdou'; // FIXED: Removed extra '0'

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Get and validate input
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
    exit();
}

// Database connection
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
    error_log("Database connection failed: " . $mysqli->connect_error);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit();
}

// Prepare statement
$stmt = $mysqli->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $mysqli->error);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    $mysqli->close();
    exit();
}

// Bind and execute
$stmt->bind_param('s', $email);
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    $stmt->close();
    $mysqli->close();
    exit();
}

// Store result and check
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    $stmt->close();
    $mysqli->close();
    exit();
}

// Bind result variables
$stmt->bind_result($id, $full_name, $password_hash);

// Fetch the result
if ($stmt->fetch()) {
    // Now $password_hash should contain the hashed password from database
    
    // Debug: Check what we got
    error_log("Debug: User ID=$id, Password hash from DB: " . ($password_hash ? substr($password_hash, 0, 20) : 'NULL') . "...");
    
    // Verify password
    if ($password_hash !== null && password_verify($password, $password_hash)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $full_name;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'user' => ['id' => $id, 'name' => $full_name]
        ]);
    } else {
        // Also check if password is stored in plain text (for migration purposes)
        if ($password === $password_hash) {
            // Password is plain text - you should hash it!
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $full_name;
            
            // Optionally update to hashed password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param('si', $hashed, $id);
            $updateStmt->execute();
            $updateStmt->close();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful (password upgraded)'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    }
} else {
    // Fetch failed
    echo json_encode(['success' => false, 'message' => 'Database error fetching user']);
}

// Cleanup
$stmt->close();
$mysqli->close();
exit();
?>