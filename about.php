<?php
session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $_SESSION['user_name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - TaskMasters</title>
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/Styles.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/about.css">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar">
    <div class="navbar-container">
      <a href="<?php echo $isLoggedIn ? 'Dashboard.php' : 'index.php'; ?>" class="navbar-brand">
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
        <?php if ($isLoggedIn): ?>
          <li><a href="Dashboard.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
              <path d="M9 22V12h6v10"/>
            </svg>
            Dashboard
          </a></li>
          <li><a href="task-manager.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Tasks
          </a></li>
          <li><a href="group_project.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Projects
          </a></li>
          <li><a href="about.php" class="active">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <path d="M12 16v-4M12 8h.01"/>
            </svg>
            About
          </a></li>
          <li>
            <div class="user-dropdown" id="userDropdown">
              <button class="user-button" onclick="toggleUserDropdown()">
                <div class="user-avatar"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
                <span><?php echo htmlspecialchars($userName); ?></span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M6 9l6 6 6-6"/>
                </svg>
              </button>
              <div class="dropdown-menu">
                <a href="logout.php">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                  </svg>
                  Logout
                </a>
              </div>
            </div>
          </li>
        <?php else: ?>
          <li><a href="index.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
              <path d="M9 22V12h6v10"/>
            </svg>
            Home
          </a></li>
          <li><a href="about.php" class="active">
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
        <?php endif; ?>
      </ul>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="about-hero">
    <div class="container">
      <div class="hero-content">
        <h1>About TaskMasters</h1>
        <p>Empowering students to achieve academic excellence through better organization</p>
        <div class="hero-badge">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
          </svg>
          <span>Built by Students, for Students</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Story Section -->
  <section class="about-section">
    <div class="container">
      <div class="content-grid">
        <div class="content-text">
          <div class="section-badge">Our Story</div>
          <h2>From Student Challenges to Solutions</h2>
          <p>
            TaskMasters was born from a simple observation: university students struggle to manage 
            multiple assignments, projects, and deadlines simultaneously. As students ourselves, 
            we experienced the stress of juggling coursework, group projects, and personal commitments.
          </p>
          <p>
            We created TaskMasters to solve this problem—a platform designed specifically for students, 
            by students. Our goal is to make academic life simpler by providing intuitive tools 
            for task management and team collaboration.
          </p>
          <div class="stats-inline">
            <div class="stat-box">
              <div class="stat-number">1000+</div>
              <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-box">
              <div class="stat-number">5000+</div>
              <div class="stat-label">Tasks Managed</div>
            </div>
            <div class="stat-box">
              <div class="stat-number">500+</div>
              <div class="stat-label">Projects Created</div>
            </div>
          </div>
        </div>
        <div class="content-image">
          <div class="image-placeholder">
            <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="gradient1" x1="0" y1="0" x2="400" y2="300">
                  <stop offset="0%" stop-color="#8B0000"/>
                  <stop offset="100%" stop-color="#FFD700"/>
                </linearGradient>
              </defs>
              <rect width="400" height="300" fill="url(#gradient1)" opacity="0.1"/>
              <circle cx="200" cy="150" r="80" fill="url(#gradient1)" opacity="0.2"/>
              <path d="M150 120 L200 100 L250 120 L250 180 L200 200 L150 180 Z" fill="url(#gradient1)" opacity="0.3"/>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Mission & Vision -->
  <section class="mission-vision">
    <div class="container">
      <div class="mv-grid">
        <div class="mv-card">
          <div class="mv-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
          </div>
          <h3>Our Mission</h3>
          <p>
            To empower university students to manage their academic workload effectively by 
            providing a simple, collaborative, and efficient task management platform. We aim 
            to reduce stress, enhance productivity, and promote teamwork for better academic success.
          </p>
        </div>

        <div class="mv-card">
          <div class="mv-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </div>
          <h3>Our Vision</h3>
          <p>
            To become the leading digital platform that inspires academic excellence, teamwork, 
            and personal growth for students around the world through smart organization and 
            innovative technology.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Values Section -->
  <section class="values-section">
    <div class="container">
      <div class="section-header">
        <h2>Our Core Values</h2>
        <p>The principles that guide everything we do</p>
      </div>

      <div class="values-grid">
        <div class="value-card">
          <div class="value-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <h3>Integrity</h3>
          <p>We believe in honest, transparent, and ethical practices in all our interactions.</p>
        </div>

        <div class="value-card">
          <div class="value-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <h3>Collaboration</h3>
          <p>We foster teamwork and community to help students achieve more together.</p>
        </div>

        <div class="value-card">
          <div class="value-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
          </div>
          <h3>Innovation</h3>
          <p>We continuously improve our platform with cutting-edge solutions for student needs.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <section class="team-section">
    <div class="container">
      <div class="section-header">
        <h2>Meet Team TaskMasters</h2>
        <p>The brilliant minds behind TaskMasters</p>
      </div>

      <div class="team-grid">
        <div class="team-card">
          <div class="team-avatar">
            <img src="images/Fanna.jpg" alt="Fannareme" class="avatar-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="avatar-fallback" style="display: none;">
              <?php echo strtoupper(substr('Fannareme', 0, 1)); ?>
            </div>
          </div>
          <h3>Fannareme</h3>
          <p class="team-role">Project Manager & Frontend Developer</p>
          <p class="team-bio">
            Leading the team with vision and coordinating all project activities. 
            Specializes in creating intuitive user interfaces.
          </p>
          <div class="team-skills">
            <span class="skill-tag">Leadership</span>
            <span class="skill-tag">React</span>
            <span class="skill-tag">UI Design</span>
          </div>
        </div>

        <div class="team-card">
          <div class="team-avatar">
            <img src="images/Malika.jpg" alt="Malika" class="avatar-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="avatar-fallback" style="display: none;">
              <?php echo strtoupper(substr('Malika', 0, 1)); ?>
            </div>
          </div>
          <h3>Malika</h3>
          <p class="team-role">UI/UX Designer & Documentation Lead</p>
          <p class="team-bio">
            Crafting beautiful and user-friendly designs that make task management 
            a delightful experience.
          </p>
          <div class="team-skills">
            <span class="skill-tag">PHP</span>
            <span class="skill-tag">CSS</span>
            <span class="skill-tag">Design Systems</span>
          </div>
        </div>

        <div class="team-card">
          <div class="team-avatar">
            <img src="images/Moctar.jpg" alt="Moctar" class="avatar-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="avatar-fallback" style="display: none;">
              <?php echo strtoupper(substr('Moctar', 0, 1)); ?>
            </div>
          </div>
          <h3>Moctar</h3>
          <p class="team-role">Backend Developer (Database & PHP)</p>
          <p class="team-bio">
            Building robust backend systems and managing data architecture. 
            Ensures security and scalability.
          </p>
          <div class="team-skills">
            <span class="skill-tag">MySQL</span>
            <span class="skill-tag">APIs</span>
          </div>
        </div>

        <div class="team-card">
          <div class="team-avatar">
            <img src="images/Peter.jpg" alt="Peter" class="avatar-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="avatar-fallback" style="display: none;">
              <?php echo strtoupper(substr('Peter', 0, 1)); ?>
            </div>
          </div>
          <h3>Peter</h3>
          <p class="team-role">Full Stack Developer & QA Lead</p>
          <p class="team-bio">
            Bridging frontend and backend development while ensuring quality 
            and performance standards.
          </p>
          <div class="team-skills">
            <span class="skill-tag">JavaScript</span>
            <span class="skill-tag">Testing</span>
            <span class="skill-tag">Performance</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact CTA -->
  <section class="contact-cta">
    <div class="container">
      <h2>Have Questions or Feedback?</h2>
      <p>We'd love to hear from you! Reach out to our team for support or suggestions.</p>
      <a href="contact.php" class="btn btn-primary btn-lg">
        Contact Us
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
      </a>
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

    function closeMobileMenu() {
      if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
      if (navbarMenu) navbarMenu.classList.remove('active');
      if (mobileOverlay) mobileOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }

    function toggleUserDropdown() {
      const userDropdown = document.getElementById('userDropdown');
      if (userDropdown) {
        userDropdown.classList.toggle('active');
      }
    }

    document.addEventListener('click', function(event) {
      const userDropdown = document.getElementById('userDropdown');
      if (userDropdown && !userDropdown.contains(event.target)) {
        userDropdown.classList.remove('active');
      }
    });

    window.addEventListener('resize', function() {
      if (window.innerWidth > 968) {
        closeMobileMenu();
      }
    });

    // Smooth scroll for anchor links
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
  </script>
</body>
</html>
