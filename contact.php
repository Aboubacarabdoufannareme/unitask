<?php
session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $_SESSION['user_name'] ?? 'Guest';

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $messageContent = trim($_POST['message'] ?? '');
    
    // Simple validation
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($messageContent)) {
        // In a real application, you would send an email or save to database
        $message = 'Thank you for your message! We will get back to you soon.';
    } else {
        $message = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us - TaskMasters</title>
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/Styles.css">
  <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar">
    <div class="navbar-container">
      <a href="<?php echo $isLoggedIn ? 'Dashboard.php' : 'index.php'; ?>" class="navbar-brand">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        TaskMasters
      </a>
      <ul class="navbar-menu">
        <?php if ($isLoggedIn): ?>
          <li><a href="Dashboard.php">Dashboard</a></li>
          <li><a href="task-manager.php">Tasks</a></li>
          <li><a href="group_project.php">Projects</a></li>
          <li><a href="about.php">About</a></li>
          <li>
            <div class="user-dropdown">
              <button class="user-button">
                <div class="user-avatar"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
                <span><?php echo htmlspecialchars($userName); ?></span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M6 9l6 6 6-6"/>
                </svg>
              </button>
              <div class="dropdown-menu">
                <a href="logout.php">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                  </svg>
                  Logout
                </a>
              </div>
            </div>
          </li>
        <?php else: ?>
          <li><a href="index.php">Home</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="Login.php">Login</a></li>
          <li><a href="Signup.php" class="btn btn-primary btn-sm">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="about-hero">
    <div class="container">
      <h1>Contact Us</h1>
      <p>We'd love to hear from you. Get in touch with our team.</p>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="about-section">
    <div class="container">
      <div class="content-grid">
        <div class="content-text">
          <h2>Get In Touch</h2>
          <p>
            Have questions about TaskMasters? Need help with your account? Want to provide feedback? 
            Our team is here to assist you.
          </p>
          
          <?php if ($message): ?>
          <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
          </div>
          <?php endif; ?>
          
          <form method="post" class="auth-form">
            <div class="form-group">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" id="email" name="email" class="form-control" placeholder="you@university.edu" required>
            </div>
            
            <div class="form-group">
              <label for="subject" class="form-label">Subject</label>
              <input type="text" id="subject" name="subject" class="form-control" placeholder="What is this regarding?" required>
            </div>
            
            <div class="form-group">
              <label for="message" class="form-label">Message</label>
              <textarea id="message" name="message" class="form-control" rows="5" placeholder="Your message here..." required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
              Send Message
            </button>
          </form>
        </div>
        
        <div class="content-image">
          <div class="image-placeholder">
            <svg width="100%" height="100%" viewBox="0 0 400 300" fill="none">
              <rect width="400" height="300" fill="url(#gradient1)"/>
              <circle cx="200" cy="150" r="80" fill="white" opacity="0.2"/>
              <defs>
                <linearGradient id="gradient1" x1="0" y1="0" x2="400" y2="300">
                  <stop offset="0%" stop-color="#8B0000"/>
                  <stop offset="100%" stop-color="#FFD700"/>
                </linearGradient>
              </defs>
            </svg>
          </div>
        </div>
      </div>
      
      <!-- Contact Info -->
      <div class="mv-grid" style="margin-top: 4rem;">
        <div class="mv-card">
          <div class="mv-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
          </div>
          <h3>Phone</h3>
          <p>+233 123 456 789</p>
        </div>

        <div class="mv-card">
          <div class="mv-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </div>
          <h3>Email</h3>
          <p>support@unitask.edu</p>
        </div>

        <div class="mv-card">
          <div class="mv-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <h3>Address</h3>
          <p>Ashesi University Campus<br>Berekuso, Ghana</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-brand">
          <h3>TaskMasters</h3>
          <p>Empowering university students to achieve academic excellence through better organization.</p>
          <div class="social-links">
            <a href="#" aria-label="Facebook">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
              </svg>
            </a>
            <a href="#" aria-label="Twitter">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
              </svg>
            </a>
            <a href="#" aria-label="Instagram">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
              </svg>
            </a>
          </div>
        </div>
        
        <div class="footer-links">
          <h4>Product</h4>
          <ul>
            <li><a href="#">Features</a></li>
            <li><a href="#">Pricing</a></li>
            <li><a href="#">Integrations</a></li>
            <li><a href="#">Roadmap</a></li>
          </ul>
        </div>
        
        <div class="footer-links">
          <h4>Resources</h4>
          <ul>
            <li><a href="about.php">About Us</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Help Center</a></li>
            <li><a href="#">Community</a></li>
          </ul>
        </div>
        
        <div class="footer-links">
          <h4>Legal</h4>
          <ul>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Cookie Policy</a></li>
            <li><a href="#">Security</a></li>
          </ul>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2025 TaskMasters. All rights reserved. Designed with ❤️ for university students.</p>
      </div>
    </div>
  </footer>

</body>
</html>