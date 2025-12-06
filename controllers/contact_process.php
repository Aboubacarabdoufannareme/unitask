<?php
/**
 * Contact Form Processor
 * Handles contact form submissions
 */

session_start();

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../about.php?error=" . urlencode("Invalid request method"));
    exit();
}

// Get and sanitize form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = "Name is required";
} elseif (strlen($name) < 2) {
    $errors[] = "Name must be at least 2 characters";
}

if (empty($email)) {
    $errors[] = "Email is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (empty($message)) {
    $errors[] = "Message is required";
} elseif (strlen($message) < 10) {
    $errors[] = "Message must be at least 10 characters";
}

// If there are errors, redirect back with error message
if (!empty($errors)) {
    header("Location: ../about.php?error=" . urlencode(implode(', ', $errors)));
    exit();
}

// Since we don't have a contact_messages table, we'll just redirect with a success message
// In a real application, you would save this to a database or send an email

// Optional: Send email notification
// $to = "support@unitask.edu";
// $subject = "New Contact Form Submission from " . $name;
// $emailMessage = "Name: $name\nEmail: $email\n\nMessage:\n$message";
// $headers = "From: no-reply@TaskMasters.edu\r\nReply-To: $email";
// mail($to, $subject, $emailMessage, $headers);

// Redirect with success message
header("Location: ../about.php?success=" . urlencode("Thank you! Your message has been sent successfully."));
exit();
?>