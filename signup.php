<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: Dashboard.php");
    exit();
}

$error = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - TaskMasters</title>
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-branding">
      <div class="branding-content">
        <a href="index.php" class="brand-logo">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          TaskMasters
        </a>
        <h1>Get Started!</h1>
        <p>Join TaskMasters to manage your academic tasks and collaborate with peers.</p>
      </div>
    </div>

    <div class="auth-form-container">
      <div class="auth-form-wrapper">
        <div class="auth-header">
          <h2>Create Account</h2>
          <p>Sign up to get started</p>
        </div>

        <form id="signupForm" class="auth-form">
          <div class="form-group">
            <label for="fullName" class="form-label">Full Name</label>
            <input type="text" id="fullName" name="fullName" class="form-control" placeholder="Enter your full name" required />
          </div>
          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="you@university.edu" required />
          </div>
          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Create a strong password" required />
          </div>
          <div class="form-group">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Re-enter your password" required />
          </div>
          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" name="terms" required />
              <span>I agree to Terms & Privacy Policy</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>

        <div class="auth-footer">
          <p>Already have an account? <a href="Login.php">Sign In</a></p>
        </div>
      </div>
    </div>
  </div>

  <script src="js/Signup_validation_fixed.js"></script>
</body>
</html>