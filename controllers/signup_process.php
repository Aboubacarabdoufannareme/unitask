<?php
header('Content-Type: application/json');
session_start();

$dbHost = 'localhost';
$dbUser = 'fannareme.abdou';
$dbPass = 'fa889033';
$dbName = webtech_2025A_fannareme_abdou';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$fullName = trim($_POST['fullName'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($fullName === '' || $email === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit();
}

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit();
}

// Check if email exists
$stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    $stmt->close();
    $mysqli->close();
    exit();
}
$stmt->close();

// Insert new user
$hash = password_hash($password, PASSWORD_DEFAULT);
$ins = $mysqli->prepare("INSERT INTO users (full_name, email, password, created_at) VALUES (?, ?, ?, NOW())");
$ins->bind_param('sss', $fullName, $email, $hash);

if ($ins->execute()) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $mysqli->insert_id;
    $_SESSION['user_name'] = $fullName;
    $ins->close();
    $mysqli->close();
    echo json_encode(['success' => true, 'message' => 'Account created successfully']);
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Signup failed. Try again']);
}
$ins->close();
$mysqli->close();
?>
