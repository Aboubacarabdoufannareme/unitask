<?php
// Start session and check authentication
require_once 'includes/auth_check.php';
require_once 'config/DBconnection.php';

// Get user information from session
$userName = $_SESSION['user_name'] ?? 'Student';
$userEmail = $_SESSION['user_email'] ?? '';
$userId = $_SESSION['user_id'];

// Get statistics directly from database
// Get task stats (safe: verify table exists first to avoid fatal errors)
$taskStats = ['total' => 0, 'completed' => 0, 'pending' => 0];
$check = $conn->query("SHOW TABLES LIKE 'tasks'");
if ($check && $check->num_rows > 0) {
  $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE user_id = ?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $taskResult = $stmt->get_result();
  $f = $taskResult->fetch_assoc();
  if ($f) {
    $taskStats['total'] = (int) ($f['total'] ?? 0);
  }
  // Preserve backward compatibility where completed/pending aren't tracked
  $taskStats['completed'] = 0;
  $taskStats['pending'] = $taskStats['total'];
  $stmt->close();
}

// Get project stats (defensive query with error handling)
$projectStats = ['total_projects' => 0];
$groupProjects = [];
try {
    $stmt = $conn->prepare("SHOW TABLES LIKE 'group_projects'");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Table exists, try to get project stats
        $stmt->close();
        // Try a simpler query that's less likely to fail
        $stmt = $conn->prepare("SELECT COUNT(*) as total_projects FROM group_projects WHERE created_by = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $projectResult = $stmt->get_result();
            $projectStats = $projectResult->fetch_assoc();
        }
        $stmt->close();
        
        // Fetch user's group projects
        $stmt = $conn->prepare("SELECT * FROM group_projects WHERE created_by = ? ORDER BY created_at DESC LIMIT 3");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $groupProjects = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (Exception $e) {
    // If anything fails, just use default values
    $projectStats = ['total_projects' => 0];
    $groupProjects = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - TaskMasters</title>
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/Styles.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar">
    <div class="navbar-container">
      <a href="Dashboard.php" class="navbar-brand">
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
        <li><a href="Dashboard.php" class="active">
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
        <li><a href="about.php">
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
      </ul>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="dashboard-main">
    <div class="container">
      
      <!-- Header -->
      <div class="dashboard-header">
        <div>
          <h1>Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</h1>
          <p>Here's what's happening with your tasks and projects today.</p>
        </div>
        <div class="header-actions">
          <a href="task-manager.php" class="btn btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 4v16m8-8H4"/>
            </svg>
            New Task
          </a>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="stats-grid">
        <div class="stat-card stat-primary">
          <div class="stat-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3><?php echo $taskStats['total'] ?? 0; ?></h3>
            <p>Total Tasks</p>
          </div>
        </div>

        <div class="stat-card stat-success">
          <div class="stat-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3><?php echo $taskStats['completed'] ?? 0; ?></h3>
            <p>Completed</p>
          </div>
        </div>

        <div class="stat-card stat-warning">
          <div class="stat-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3><?php echo $taskStats['pending'] ?? 0; ?></h3>
            <p>In Progress</p>
          </div>
        </div>

        <div class="stat-card stat-secondary">
          <div class="stat-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3><?php echo $projectStats['total_projects'] ?? 0; ?></h3>
            <p>Group Projects</p>
          </div>
        </div>
      </div>

      <!-- Progress Section -->
      <div class="progress-section">
        <div class="section-header">
          <h2>Overall Progress</h2>
          <p id="progressText">Loading...</p>
        </div>
        <div class="progress-bar-wrapper">
          <div class="progress-bar">
            <div id="progressBarFill" class="progress-bar-fill"></div>
          </div>
        </div>
      </div>

      <!-- Content Grid -->
      <div class="dashboard-grid">
        
        <!-- Recent Tasks -->
        <div class="dashboard-card">
          <div class="card-header">
            <h3>Recent Tasks</h3>
            <div class="task-controls">
              <input type="search" id="taskSearch" placeholder="Search tasks..." class="search-input">
              <select id="taskFilter" class="filter-select">
                <option value="all">All</option>
                <option value="completed">Completed</option>
                <option value="in-progress">In Progress</option>
              </select>
            </div>
          </div>
          <div class="card-body">
            <ul id="dashboardTasks" class="task-list">
              <li class="loading">Loading tasks...</li>
            </ul>
          </div>
          <div class="card-footer">
            <a href="task-manager.php" class="view-all-link">
              View all tasks
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
          <div class="card-header">
            <h3>Quick Actions</h3>
          </div>
          <div class="card-body">
            <div class="quick-actions">
              <a href="task-manager.php" class="action-button">
                <div class="action-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 4v16m8-8H4"/>
                  </svg>
                </div>
                <div class="action-content">
                  <h4>Add Task</h4>
                  <p>Create a new task</p>
                </div>
              </a>

              <a href="group_project.php" class="action-button">
                <div class="action-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
                <div class="action-content">
                  <h4>New Project</h4>
                  <p>Start a group project</p>
                </div>
              </a>

              <a href="task-manager.php" class="action-button">
                <div class="action-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                  </svg>
                </div>
                <div class="action-content">
                  <h4>View All Tasks</h4>
                  <p>Manage your tasks</p>
                </div>
              </a>
            </div>
          </div>
        </div>

      </div>

      <!-- Group Projects Section -->
      <div class="group-projects-section" style="margin-top: 2rem;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
          <div>
            <h2>Your Group Projects</h2>
            <p>Manage and track progress on your team projects</p>
          </div>
          <a href="group_project.php" class="btn btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 4v16m8-8H4"/>
            </svg>
            New Project
          </a>
        </div>

        <?php if (!empty($groupProjects)): ?>
          <div class="projects-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php foreach ($groupProjects as $project): ?>
              <?php
                // Calculate project progress
                $projectId = $project['id'];
                $stmt = $conn->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed FROM group_tasks WHERE project_id = ?");
                $stmt->bind_param("i", $projectId);
                $stmt->execute();
                $taskStats = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                
                $totalTasks = $taskStats['total'] ?? 0;
                $completedTasks = $taskStats['completed'] ?? 0;
                $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
              ?>
              <div class="dashboard-card project-card">
                <div class="card-header">
                  <h3 style="margin: 0;"><?= htmlspecialchars($project['project_name']) ?></h3>
                </div>
                <div class="card-body">
                  <?php if (!empty($project['description'])): ?>
                    <p style="color: #6b7280; margin-bottom: 1rem; font-size: 0.875rem;">
                      <?= htmlspecialchars(substr($project['description'], 0, 80)) . (strlen($project['description']) > 80 ? '...' : '') ?>
                    </p>
                  <?php endif; ?>
                  
                  <div style="background: #f3f4f6; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 500;">
                      <span>Progress</span>
                      <span><?= $progressPercent ?>%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: #e5e7eb; border-radius: 999px; overflow: hidden;">
                      <div style="width: <?= $progressPercent ?>%; height: 100%; background: linear-gradient(90deg, #667eea, #764ba2); border-radius: 999px; transition: width 0.3s ease;"></div>
                    </div>
                    <div style="display: flex; gap: 2rem; margin-top: 1rem; font-size: 0.875rem; color: #6b7280;">
                      <div>
                        <div style="font-weight: 600; color: #1f2937;"><?= $completedTasks ?></div>
                        <div>Completed</div>
                      </div>
                      <div>
                        <div style="font-weight: 600; color: #1f2937;"><?= $totalTasks - $completedTasks ?></div>
                        <div>Remaining</div>
                      </div>
                    </div>
                  </div>

                  <div style="display: flex; gap: 0.5rem; font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                      <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span><?= htmlspecialchars($project['num_members']) ?> Members • Led by <?= htmlspecialchars($project['leader_name']) ?></span>
                  </div>
                </div>
                <div class="card-footer" style="display: flex; gap: 0.5rem;">
                  <a href="project_detail.php?id=<?= $project['id'] ?>" class="btn btn-small btn-primary" style="flex: 1; text-align: center; text-decoration: none;">
                    Manage
                  </a>
                  <a href="group_project.php" class="btn btn-small btn-secondary" style="flex: 1; text-align: center; text-decoration: none;">
                    View All
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="dashboard-card" style="text-align: center; padding: 3rem 2rem;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 1rem; opacity: 0.5;">
              <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p style="color: #6b7280;">No group projects yet. Start collaborating with your team!</p>
            <a href="group_project.php" class="btn btn-primary" style="margin-top: 1rem;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 4v16m8-8H4"/>
              </svg>
              Create Project
            </a>
          </div>
        <?php endif; ?>
      </div>

  <script src="assets/js/dashboard_ajax.js"></script>
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

    // User Dropdown Toggle (for mobile)
    function toggleUserDropdown() {
      const userDropdown = document.getElementById('userDropdown');
      if (userDropdown) {
        userDropdown.classList.toggle('active');
      }
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
      const userDropdown = document.getElementById('userDropdown');
      if (userDropdown && !userDropdown.contains(event.target)) {
        userDropdown.classList.remove('active');
      }
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