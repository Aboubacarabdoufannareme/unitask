<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: Dashboard.php");
    exit();
}

$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - TaskMasters</title>
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

  <div class="auth-container">
    <!-- Left Side - Branding -->
    <div class="auth-branding">
      <div class="branding-content">
        <a href="index.php" class="brand-logo">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          TaskMasters
        </a>
        <h1>Welcome Back!</h1>
        <p>Sign in to continue managing your academic tasks and projects.</p>
        
        <div class="features-list">
          <div class="feature-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 13l4 4L19 7"/>
            </svg>
            <span>Track your personal tasks</span>
          </div>
          <div class="feature-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 13l4 4L19 7"/>
            </svg>
            <span>Collaborate on group projects</span>
          </div>
          <div class="feature-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 13l4 4L19 7"/>
            </svg>
            <span>Monitor your progress</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="auth-form-container">
      <div class="auth-form-wrapper">
        <div class="auth-header">
          <h2>Sign In</h2>
          <p>Enter your credentials to access your Dashboard, Tasks, and Group Projects</p>
        </div>
        
        <div class="auth-info-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
          </svg>
          <p>You must sign in to access protected pages like Dashboard, Tasks, and Group Projects.</p>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-info">
          <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <form id="loginForm" class="auth-form">
          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              class="form-control" 
              placeholder="you@university.edu" 
              required
            />
          </div>

          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-control" 
              placeholder="Enter your password" 
              required
            />
          </div>

          <div class="form-options">
            <label class="checkbox-label">
              <input type="checkbox" name="remember" />
              <span>Remember me</span>
            </label>
            <a href="#" class="forgot-link">Forgot password?</a>
          </div>

          <button type="submit" class="btn btn-primary btn-block">
            Sign In
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </button>
        </form>

        <div class="auth-footer">
          <p>Don't have an account? <a href="Signup.php">Sign up</a></p>
        </div>

        <div class="back-home">
          <a href="index.php">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Home
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="js/login_validation_fixed.js"></script>
</body>
</html>