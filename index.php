<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: Dashboard.php");
    exit();
}

// Check for logout message
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TaskMasters - Student Task Management Platform</title>
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/Styles.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar">
    <div class="navbar-container">
      <a href="index.php" class="navbar-brand">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        TaskMasters
      </a>
      <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <div class="mobile-overlay" id="mobileOverlay"></div>
      <ul class="navbar-menu" id="navbarMenu">
        <li><a href="index.php" class="active">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            <path d="M9 22V12h6v10"/>
          </svg>
          Home
        </a></li>
        <li><a href="#features">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
          Features
        </a></li>
        <li><a href="about.php">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 16v-4M12 8h.01"/>
          </svg>
          About
        </a></li>
        <li><a href="contact.php">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
          Contact
        </a></li>
        <li><a href="Login.php">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
          </svg>
          Login
        </a></li>
        <li><a href="signup.php" class="btn btn-primary btn-sm">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
            <circle cx="8.5" cy="7" r="4"/>
            <line x1="20" y1="8" x2="20" y2="14"/>
            <line x1="23" y1="11" x2="17" y2="11"/>
          </svg>
          Sign Up
        </a></li>
      </ul>
    </div>
  </nav>

  <?php if ($message): ?>
  <div class="alert-banner">
    <div class="container">
      <div class="alert alert-success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?php echo htmlspecialchars($message); ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <div class="hero-badge">
        <span>✨ For University Students</span>
      </div>
      <h1 class="hero-title">
        Organize Your Academic Life
        <span class="gradient-text">Effortlessly</span>
      </h1>
      <p class="hero-description">
        TaskMasters helps you manage individual tasks and group projects with ease. 
        Stay on top of deadlines, collaborate with teammates, and achieve academic success.
      </p>
      <div class="hero-notice">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="16" x2="12" y2="12"/>
          <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        <span>Sign up or log in to access your Dashboard, Tasks, and Group Projects</span>
      </div>
      <div class="hero-buttons">
        <a href="signup.php" class="btn btn-primary btn-lg get-started-primary" id="getStartedBtn">
          <span>Get Started Free</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </a>
        <a href="Login.php" class="btn btn-secondary btn-lg">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
          </svg>
          Sign In
        </a>
      </div>
      <p class="hero-subtext">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Free to use • No credit card required • Start organizing today
      </p>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-number">1000+</div>
          <div class="stat-label">Active Students</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">5000+</div>
          <div class="stat-label">Tasks Completed</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">500+</div>
          <div class="stat-label">Group Projects</div>
        </div>
      </div>
    </div>
    <div class="hero-image">
      <div class="floating-card card-1">
        <div class="card-icon">✓</div>
        <div class="card-text">Task Completed!</div>
      </div>
      <div class="floating-card card-2">
        <div class="card-icon">📊</div>
        <div class="card-text">85% Progress</div>
      </div>
      <div class="floating-card card-3">
        <div class="card-icon">👥</div>
        <div class="card-text">4 Team Members</div>
      </div>
      <div class="hero-illustration">
        <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="50" y="50" width="300" height="200" rx="10" fill="#8B0000" opacity="0.1"/>
          <rect x="70" y="80" width="100" height="15" rx="5" fill="#8B0000"/>
          <rect x="70" y="110" width="150" height="15" rx="5" fill="#FFD700"/>
          <rect x="70" y="140" width="120" height="15" rx="5" fill="#228B22"/>
          <circle cx="320" cy="100" r="30" fill="#FF8C00" opacity="0.2"/>
          <circle cx="320" cy="100" r="20" fill="#FF8C00"/>
        </svg>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section id="features" class="features-section">
    <div class="container">
      <div class="section-header">
        <h2>Everything You Need to Succeed</h2>
        <p>Powerful features designed specifically for university students</p>
      </div>
      
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
          </div>
          <h3>Task Management</h3>
          <p>Create, organize, and track your personal academic tasks with ease. Set priorities and deadlines to stay on schedule.</p>
          <a href="signup.php" class="feature-link">Get Started →</a>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <h3>Group Collaboration</h3>
          <p>Work seamlessly with your team. Assign tasks, track progress, and ensure everyone stays aligned on group projects.</p>
          <a href="signup.php" class="feature-link">Get Started →</a>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
          <h3>Progress Tracking</h3>
          <p>Visualize your productivity with intuitive dashboards. Monitor completion rates and identify areas for improvement.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h3>Deadline Management</h3>
          <p>Never miss a due date again. Set reminders and organize tasks by priority to manage your time effectively.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <h3>Secure & Private</h3>
          <p>Your data is encrypted and secure. We prioritize your privacy with industry-standard security measures.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
          </div>
          <h3>Mobile Friendly</h3>
          <p>Access your tasks anywhere, anytime. Our responsive design works perfectly on all your devices.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="cta-content">
      <h2>Ready to Transform Your Academic Experience?</h2>
      <p>Join thousands of students who are already using TaskMasters to stay organized and achieve their goals.</p>
      <div class="cta-buttons">
        <a href="signup.php" class="btn btn-primary btn-lg">
          Create Your Free Account
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </a>
        <a href="Login.php" class="btn btn-outline btn-lg" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white;">
          Sign In to Your Account
        </a>
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
            <li><a href="#features">Features</a></li>
            <li><a href="task-manager.php">Tasks</a></li>
            <li><a href="group_project.php">Projects</a></li>
            <li><a href="Dashboard.php">Dashboard</a></li>
          </ul>
        </div>
        
        <div class="footer-links">
          <h4>Resources</h4>
          <ul>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="Login.php">Login</a></li>
            <li><a href="signup.php">Sign Up</a></li>
          </ul>
        </div>
        
        <div class="footer-links">
          <h4>Legal</h4>
          <ul>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="Login.php">Sign In</a></li>
            <li><a href="signup.php">Get Started</a></li>
          </ul>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2025 TaskMasters. All rights reserved. Designed with ❤️ for university students.</p>
      </div>
    </div>
  </footer>

  <script>
    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navbarMenu = document.getElementById('navbarMenu');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if (mobileMenuToggle) {
      mobileMenuToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        navbarMenu.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
        document.body.style.overflow = navbarMenu.classList.contains('active') ? 'hidden' : '';
      });
    }

    if (mobileOverlay) {
      mobileOverlay.addEventListener('click', function() {
        closeMobileMenu();
      });
    }

    // Close mobile menu when clicking on a link
    const navLinks = navbarMenu.querySelectorAll('a');
    navLinks.forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 968) {
          closeMobileMenu();
        }
      });
    });

    function closeMobileMenu() {
      if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
      if (navbarMenu) navbarMenu.classList.remove('active');
      if (mobileOverlay) mobileOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href.length > 1) {
          e.preventDefault();
          const target = document.querySelector(href);
          if (target) {
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        }
      });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth > 968) {
        closeMobileMenu();
      }
    });
  </script>
</body>
</html>