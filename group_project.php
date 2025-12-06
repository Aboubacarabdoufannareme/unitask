<?php
// Start session and check authentication
require_once 'includes/auth_check.php';
require_once 'config/DBconnection.php';

// Get user information from session
$userName = $_SESSION['user_name'] ?? 'Student';
$userId = $_SESSION['user_id'];

// Fetch user's projects
$projects = [];
try {
    $stmt = $conn->prepare("SELECT * FROM group_projects WHERE created_by = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $projects = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    // Table might not exist, that's okay
    $projects = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Group Projects - TaskMasters</title>
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/Styles.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/group-project.css">
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
        <li><a href="group_project.php" class="active">
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
  <main class="projects-main">
    <div class="container">
      
      <!-- Page Header -->
      <div class="projects-header">
        <div>
          <h1>Group Projects</h1>
          <p>Collaborate with your team and manage group projects efficiently</p>
        </div>
        <button class="btn btn-primary" id="createProjectBtn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 4v16m8-8H4"/>
          </svg>
          New Project
        </button>
      </div>

      <!-- Projects Grid -->
      <div class="projects-container">
        <?php if (!empty($projects)): ?>
          <div class="projects-grid" id="projectsGrid">
            <?php foreach ($projects as $project): ?>
              <div class="project-card" data-project-id="<?= $project['id'] ?>">
                <div class="project-card-header">
                  <div class="project-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                  </div>
                  <div class="project-actions">
                    <button class="project-action-btn edit-project-btn" data-project-id="<?= $project['id'] ?>" title="Edit Project">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                      </svg>
                    </button>
                    <button class="project-action-btn delete-project-btn" data-project-id="<?= $project['id'] ?>" title="Delete Project">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="project-card-body">
                  <h3 class="project-title"><?= htmlspecialchars($project['project_name'] ?? 'Untitled Project') ?></h3>
                  <?php if (!empty($project['description'])): ?>
                    <p class="project-description"><?= htmlspecialchars($project['description']) ?></p>
                  <?php endif; ?>
                  <div class="project-meta">
                    <div class="project-meta-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                      </svg>
                      <span><?= htmlspecialchars($project['num_members'] ?? '0') ?> Members</span>
                    </div>
                    <?php if (!empty($project['leader_name'])): ?>
                      <div class="project-meta-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                          <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span><?= htmlspecialchars($project['leader_name']) ?></span>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="project-card-footer">
                  <span class="project-date">
                    Created <?= date('M d, Y', strtotime($project['created_at'] ?? 'now')) ?>
                  </span>
                  <a href="project_detail.php?id=<?= $project['id'] ?>" class="btn btn-small btn-primary" style="margin-left: auto;">
                    Manage Tasks
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3>No projects yet</h3>
            <p>Create your first group project to start collaborating with your team!</p>
            <button class="btn btn-primary" id="emptyStateCreateBtn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 4v16m8-8H4"/>
              </svg>
              Create Project
            </button>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- Create/Edit Project Modal -->
  <div id="projectModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitle">Create New Project</h2>
        <button class="modal-close" id="closeModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      <form id="projectForm" class="modal-form">
        <input type="hidden" id="projectId" name="project_id">
        <input type="hidden" name="action" id="formAction" value="create">
        
        <div class="form-group">
          <label for="projectName" class="form-label">Project Name *</label>
          <input type="text" id="projectName" name="project_name" class="form-control" placeholder="Enter project name" required>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="numMembers" class="form-label">Number of Members *</label>
            <input type="number" id="numMembers" name="num_members" class="form-control" placeholder="e.g., 4" min="2" max="20" required>
          </div>
          <div class="form-group">
            <label for="leaderName" class="form-label">Team Leader *</label>
            <input type="text" id="leaderName" name="leader_name" class="form-control" placeholder="Leader name" required>
          </div>
        </div>
        
        <div class="form-group">
          <label for="projectDescription" class="form-label">Description</label>
          <textarea id="projectDescription" name="description" class="form-control" rows="4" placeholder="Enter project description"></textarea>
        </div>
        
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <span id="submitBtnText">Create Project</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="modal">
    <div class="modal-content modal-small">
      <div class="modal-header">
        <h2>Delete Project</h2>
        <button class="modal-close" id="closeDeleteModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this project? This action cannot be undone.</p>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-secondary" id="cancelDeleteBtn">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>

  <script src="js/group_project_ajax.js"></script>
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
  </script>
</body>
</html>
