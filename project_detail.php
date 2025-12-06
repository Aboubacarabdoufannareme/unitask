<?php
// Start session and check authentication
require_once 'includes/auth_check.php';
require_once 'config/DBconnection.php';

// Get user information from session
$userName = $_SESSION['user_name'] ?? 'Student';
$userId = $_SESSION['user_id'];
$projectId = intval($_GET['id'] ?? 0);

if ($projectId <= 0) {
    header("Location: group_project.php");
    exit();
}

// Fetch the specific project
$project = null;
$tasks = [];

try {
    $stmt = $conn->prepare("SELECT * FROM group_projects WHERE id = ? AND created_by = ?");
    $stmt->bind_param("ii", $projectId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
    $stmt->close();
    
    if (!$project) {
        header("Location: group_project.php");
        exit();
    }
    
    // Fetch tasks for this project
    $stmt = $conn->prepare("SELECT * FROM group_tasks WHERE project_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Calculate progress
    $totalTasks = count($tasks);
    $completedTasks = count(array_filter($tasks, fn($t) => $t['status'] === 'completed'));
    $inProgressTasks = count(array_filter($tasks, fn($t) => $t['status'] === 'in-progress'));
    $pendingTasks = count(array_filter($tasks, fn($t) => $t['status'] === 'pending'));
    $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
} catch (Exception $e) {
    header("Location: group_project.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($project['project_name']) ?> - TaskMasters</title>
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/Styles.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/group-project.css">
  <style>
    .project-detail-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem;
      border-radius: 8px;
      margin-bottom: 2rem;
    }

    .project-detail-header h1 {
      margin: 0;
      font-size: 2rem;
    }

    .project-detail-header p {
      margin: 0.5rem 0 0 0;
      opacity: 0.9;
    }

    .project-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .stat-card {
      background: rgba(255,255,255,0.1);
      padding: 1rem;
      border-radius: 6px;
    }

    .stat-label {
      font-size: 0.875rem;
      opacity: 0.8;
    }

    .stat-value {
      font-size: 1.5rem;
      font-weight: bold;
      margin-top: 0.5rem;
    }

    .tasks-section {
      margin-top: 2rem;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .section-header h2 {
      margin: 0;
    }

    .task-item {
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .task-info {
      flex: 1;
    }

    .task-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin: 0 0 0.5rem 0;
      color: #1f2937;
    }

    .task-assigned {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #6b7280;
      font-size: 0.875rem;
    }

    .task-priority {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      margin-left: 1rem;
    }

    .priority-high {
      background: #fee2e2;
      color: #991b1b;
    }

    .priority-medium {
      background: #fef3c7;
      color: #92400e;
    }

    .priority-low {
      background: #dbeafe;
      color: #0c2d6b;
    }

    .task-actions {
      display: flex;
      gap: 0.5rem;
    }

    .task-action-btn {
      padding: 0.5rem;
      border: none;
      background: none;
      cursor: pointer;
      color: #6b7280;
      border-radius: 4px;
      transition: all 0.2s;
    }

    .task-action-btn:hover {
      background: #f3f4f6;
      color: #1f2937;
    }

    .empty-tasks {
      text-align: center;
      padding: 2rem;
      background: #f9fafb;
      border-radius: 8px;
      color: #6b7280;
    }

    .back-button {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 2rem;
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
    }

    .back-button:hover {
      opacity: 0.8;
    }
  </style>
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
  <main class="projects-main">
    <div class="container">
      
      <a href="group_project.php" class="back-button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Back to Projects
      </a>

      <!-- Project Header -->
      <div class="project-detail-header">
        <h1><?= htmlspecialchars($project['project_name']) ?></h1>
        <?php if (!empty($project['description'])): ?>
          <p><?= htmlspecialchars($project['description']) ?></p>
        <?php endif; ?>
        
        <div class="project-stats">
          <div class="stat-card">
            <div class="stat-label">Team Members</div>
            <div class="stat-value"><?= htmlspecialchars($project['num_members']) ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Team Leader</div>
            <div class="stat-value"><?= htmlspecialchars($project['leader_name']) ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Total Tasks</div>
            <div class="stat-value"><?= $totalTasks ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Progress</div>
            <div class="stat-value"><?= $progressPercent ?>%</div>
          </div>
        </div>

        <!-- Progress Breakdown -->
        <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(255,255,255,0.1); border-radius: 8px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem;">Task Status</h3>
            <div style="display: flex; gap: 2rem; font-size: 0.875rem;">
              <div><span style="display: inline-block; width: 12px; height: 12px; background: #10b981; border-radius: 2px; margin-right: 0.5rem;"></span>Completed: <?= $completedTasks ?></div>
              <div><span style="display: inline-block; width: 12px; height: 12px; background: #f59e0b; border-radius: 2px; margin-right: 0.5rem;"></span>In Progress: <?= $inProgressTasks ?></div>
              <div><span style="display: inline-block; width: 12px; height: 12px; background: #ef4444; border-radius: 2px; margin-right: 0.5rem;"></span>Pending: <?= $pendingTasks ?></div>
            </div>
          </div>
          <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.2); border-radius: 999px; overflow: hidden; display: flex;">
            <?php if ($totalTasks > 0): ?>
              <div style="width: <?= $completedTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 ?>%; height: 100%; background: #10b981;"></div>
              <div style="width: <?= $inProgressTasks > 0 ? round(($inProgressTasks / $totalTasks) * 100) : 0 ?>%; height: 100%; background: #f59e0b;"></div>
              <div style="width: <?= $pendingTasks > 0 ? round(($pendingTasks / $totalTasks) * 100) : 0 ?>%; height: 100%; background: #ef4444;"></div>
            <?php endif; ?>
          </div>
          <div style="margin-top: 1rem; font-size: 0.875rem; text-align: center; opacity: 0.9;">
            <?= $progressPercent ?>% Complete
          </div>
        </div>
      </div>

      <!-- Tasks Section -->
      <div class="tasks-section">
        <div class="section-header">
          <h2>Team Tasks</h2>
          <button class="btn btn-primary" id="createTaskBtn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 4v16m8-8H4"/>
            </svg>
            Add Task
          </button>
        </div>

        <?php if (!empty($tasks)): ?>
          <div class="tasks-list">
            <?php foreach ($tasks as $task): ?>
              <div class="task-item" data-task-id="<?= $task['id'] ?>">
                <div class="task-info">
                  <h3 class="task-title"><?= htmlspecialchars($task['task_name']) ?></h3>
                  <div class="task-assigned">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                      <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span><?= !empty($task['assigned_to']) ? htmlspecialchars($task['assigned_to']) : 'Unassigned' ?></span>
                    <span class="task-priority priority-<?= $task['priority'] ?>"><?= $task['priority'] ?></span>
                  </div>
                </div>
                <div class="task-actions">
                  <button class="task-action-btn edit-task-btn" data-task-id="<?= $task['id'] ?>" title="Edit Task">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button class="task-action-btn delete-task-btn" data-task-id="<?= $task['id'] ?>" title="Delete Task">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2">
                      <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                    </svg>
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-tasks">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 1rem; opacity: 0.5;">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p>No tasks created yet. Create your first task to get started!</p>
            <button class="btn btn-primary" id="emptyStateCreateTaskBtn" style="margin-top: 1rem;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 4v16m8-8H4"/>
              </svg>
              Add Task
            </button>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- Create/Edit Task Modal -->
  <div id="taskModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="taskModalTitle">Add Task</h2>
        <button class="modal-close" id="closeTaskModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      <form id="taskForm" class="modal-form">
        <input type="hidden" name="project_id" value="<?= $projectId ?>">
        <input type="hidden" id="taskId" name="task_id">
        <input type="hidden" name="action" id="taskAction" value="create">
        
        <div class="form-group">
          <label for="taskName" class="form-label">Task Name *</label>
          <input type="text" id="taskName" name="task_name" class="form-control" placeholder="Enter task name" required>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="assignedTo" class="form-label">Assign To *</label>
            <input type="text" id="assignedTo" name="assigned_to" class="form-control" placeholder="Member name" required>
          </div>
          <div class="form-group">
            <label for="taskPriority" class="form-label">Priority *</label>
            <select id="taskPriority" name="priority" class="form-control" required>
              <option value="">Select priority</option>
              <option value="low">Low</option>
              <option value="medium" selected>Medium</option>
              <option value="high">High</option>
            </select>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="taskStatus" class="form-label">Status</label>
            <select id="taskStatus" name="status" class="form-control">
              <option value="pending" selected>Pending</option>
              <option value="in-progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div class="form-group">
            <label for="taskDueDate" class="form-label">Due Date</label>
            <input type="date" id="taskDueDate" name="due_date" class="form-control">
          </div>
        </div>
        
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" id="cancelTaskBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitTaskBtn">
            <span id="submitTaskBtnText">Add Task</span>
          </button>
        </div>
      </form>
    </div>
  </div>

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

    // Task Modal Management
    const taskModal = document.getElementById('taskModal');
    const taskForm = document.getElementById('taskForm');
    const createTaskBtn = document.getElementById('createTaskBtn');
    const emptyStateCreateTaskBtn = document.getElementById('emptyStateCreateTaskBtn');
    const closeTaskModal = document.getElementById('closeTaskModal');
    const cancelTaskBtn = document.getElementById('cancelTaskBtn');
    const projectId = <?= $projectId ?>;

    function openTaskModal(isEdit = false) {
      if (!isEdit) {
        taskForm.reset();
        document.getElementById('taskId').value = '';
        document.getElementById('taskAction').value = 'create';
        document.getElementById('taskModalTitle').textContent = 'Add Task';
        document.getElementById('submitTaskBtnText').textContent = 'Add Task';
      }
      taskModal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeModals() {
      taskModal.classList.remove('active');
      document.body.style.overflow = '';
    }

    createTaskBtn?.addEventListener('click', () => openTaskModal());
    emptyStateCreateTaskBtn?.addEventListener('click', () => openTaskModal());
    closeTaskModal?.addEventListener('click', closeModals);
    cancelTaskBtn?.addEventListener('click', closeModals);

    taskForm?.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(taskForm);
      const submitBtn = document.getElementById('submitTaskBtn');
      submitBtn.disabled = true;
      submitBtn.textContent = formData.get('action') === 'create' ? 'Adding...' : 'Updating...';

      fetch('controllers/group_task_handler.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert('Error: ' + (data.message || 'Unknown error'));
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<span id="submitTaskBtnText">' + formData.get('action') === 'create' ? 'Add Task' : 'Update Task' + '</span>';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span id="submitTaskBtnText">' + formData.get('action') === 'create' ? 'Add Task' : 'Update Task' + '</span>';
      });
    });

    // Edit task
    document.querySelectorAll('.edit-task-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const taskId = this.dataset.taskId;
        const taskItem = document.querySelector(`[data-task-id="${taskId}"]`);
        const taskTitle = taskItem.querySelector('.task-title').textContent;
        const assignedTo = taskItem.querySelector('.task-assigned span:nth-child(2)').textContent;

        document.getElementById('taskId').value = taskId;
        document.getElementById('taskName').value = taskTitle;
        document.getElementById('assignedTo').value = assignedTo === 'Unassigned' ? '' : assignedTo;
        document.getElementById('taskAction').value = 'update';
        document.getElementById('taskModalTitle').textContent = 'Edit Task';
        document.getElementById('submitTaskBtnText').textContent = 'Update Task';
        openTaskModal(true);
      });
    });

    // Delete task
    document.querySelectorAll('.delete-task-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this task?')) {
          const taskId = this.dataset.taskId;
          const formData = new FormData();
          formData.append('action', 'delete');
          formData.append('project_id', projectId);
          formData.append('task_id', taskId);

          fetch('controllers/group_task_handler.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              location.reload();
            } else {
              alert('Error: ' + (data.message || 'Failed to delete task'));
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
          });
        }
      });
    });

    taskModal?.addEventListener('click', function(e) {
      if (e.target === taskModal) {
        closeModals();
      }
    });
  </script>
</body>
</html>
