<?php
require_once __DIR__ . '/config/DBconnection.php';
require_once __DIR__ . '/includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Student';

// //Check if status column exists, if not, add it
//////$checkColumn = $conn->query("SHOW COLUMNS FROM tasks LIKE 'status'");
///if ($checkColumn->num_rows == 0) {
  //  $conn->query("ALTER TABLE tasks ADD COLUMN status ENUM('pending', 'completed') DEFAULT 'pending'");
//}

// Fetch user's tasks
$stmt = $conn->prepare('SELECT * FROM tasks WHERE user_id = ? ORDER BY 
    CASE WHEN status = "completed" THEN 1 ELSE 0 END,
    due_date IS NULL, 
    due_date ASC');
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tasks — TaskMasters</title>
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/Styles.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/tasks.css">
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
        <li><a href="task-manager.php" class="active">
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
  <main class="tasks-main">
    <div class="container">
      
      <!-- Page Header -->
      <div class="tasks-header">
        <div>
          <h1>Task Manager</h1>
          <p>Organize and manage your tasks efficiently</p>
        </div>
        <button class="btn btn-primary" id="createTaskBtn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 4v16m8-8H4"/>
          </svg>
          New Task
        </button>
      </div>

      <!-- Tasks List -->
      <div class="tasks-container">
        <div class="tasks-filters">
          <button class="filter-btn active" data-filter="all">All Tasks</button>
          <button class="filter-btn" data-filter="pending">Pending</button>
          <button class="filter-btn" data-filter="completed">Completed</button>
        </div>

        <div id="tasksList" class="tasks-list">
          <?php if (!empty($tasks)): ?>
            <?php foreach ($tasks as $task): ?>
              <div class="task-card <?php echo $task['status'] ?? 'pending'; ?>" data-task-id="<?= $task['id'] ?>" data-status="<?= $task['status'] ?? 'pending' ?>">
                <div class="task-checkbox">
                  <input type="checkbox" class="task-complete-checkbox" 
                    <?php echo ($task['status'] ?? 'pending') === 'completed' ? 'checked' : ''; ?>
                    data-task-id="<?= $task['id'] ?>">
                  <label></label>
                </div>
                <div class="task-content">
                  <h3 class="task-title"><?= htmlspecialchars($task['title']) ?></h3>
                  <?php if (!empty($task['description'])): ?>
                    <p class="task-description"><?= htmlspecialchars($task['description']) ?></p>
                  <?php endif; ?>
                  <div class="task-meta">
                    <?php if ($task['due_date']): ?>
                      <span class="task-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                          <line x1="16" y1="2" x2="16" y2="6"/>
                          <line x1="8" y1="2" x2="8" y2="6"/>
                          <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <?= date('M d, Y', strtotime($task['due_date'])) ?>
                      </span>
                    <?php endif; ?>
                    <span class="task-status-badge <?= ($task['status'] ?? 'pending') === 'completed' ? 'completed' : 'pending' ?>">
                      <?= ucfirst($task['status'] ?? 'pending') ?>
                    </span>
                  </div>
                </div>
                <div class="task-actions">
                  <button class="task-action-btn edit-btn" data-task-id="<?= $task['id'] ?>" title="Edit Task">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button class="task-action-btn delete-btn" data-task-id="<?= $task['id'] ?>" title="Delete Task">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                    </svg>
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              <h3>No tasks yet</h3>
              <p>Create your first task to get started!</p>
              <button class="btn btn-primary" id="emptyStateCreateBtn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 4v16m8-8H4"/>
                </svg>
                Create Task
              </button>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </main>

  <!-- Create/Edit Task Modal -->
  <div id="taskModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitle">Create New Task</h2>
        <button class="modal-close" id="closeModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      <form id="taskForm" class="modal-form">
        <input type="hidden" id="taskId" name="task_id">
        <input type="hidden" name="action" id="formAction" value="create">
        
        <div class="form-group">
          <label for="taskTitle" class="form-label">Task Title *</label>
          <input type="text" id="taskTitle" name="title" class="form-control" placeholder="Enter task title" required>
        </div>
        
        <div class="form-group">
          <label for="taskDescription" class="form-label">Description</label>
          <textarea id="taskDescription" name="description" class="form-control" rows="4" placeholder="Enter task description"></textarea>
        </div>
        
        <div class="form-group">
          <label for="taskDueDate" class="form-label">Due Date</label>
          <input type="date" id="taskDueDate" name="due_date" class="form-control">
        </div>
        
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <span id="submitBtnText">Create Task</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="modal">
    <div class="modal-content modal-small">
      <div class="modal-header">
        <h2>Delete Task</h2>
        <button class="modal-close" id="closeDeleteModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this task? This action cannot be undone.</p>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-secondary" id="cancelDeleteBtn">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>

  <script src="js/task-manager_ajax.js"></script>
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
