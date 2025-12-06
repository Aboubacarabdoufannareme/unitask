// Dashboard dynamic task rendering with real-time updates
document.addEventListener('DOMContentLoaded', () => {
  const tasksContainer = document.getElementById('dashboardTasks');
  const searchInput = document.getElementById('taskSearch');
  const filterSelect = document.getElementById('taskFilter');
  const progressBarFill = document.getElementById('progressBarFill');
  const progressText = document.getElementById('progressText');

  let tasks = [];

  // Load tasks from server
  async function loadTasks() {
    try {
      // Since we don't have a TaskController.php, we'll simulate loading tasks
      // In a real application, you would fetch from an actual endpoint
      tasks = [];
      
      // Show loading state
      tasksContainer.innerHTML = '<li class="loading">Loading tasks...</li>';
      
      // Simulate API delay
      await new Promise(resolve => setTimeout(resolve, 500));
      
      // In a real application, you would uncomment the following code:
      /*
      const res = await fetch('controllers/TaskController.php?action=read');
      const data = await res.json();
      
      if (data.success && data.tasks) {
        tasks = data.tasks.map(t => ({
          id: String(t.id),
          title: t.title,
          description: t.description,
          completed: !!t.completed,
          created_at: t.created_at
        }));
      } else {
        tasks = [];
      }
      */
    } catch (e) {
      console.error('Error loading tasks:', e);
      tasks = [];
    }

    renderTasks();
    updateProgress();
  }

  function renderTasks() {
    const q = (searchInput.value || '').toLowerCase();
    const filter = filterSelect.value;

    const filtered = tasks.filter(t => {
      const matchesQuery = t.title.toLowerCase().includes(q) || (t.description || '').toLowerCase().includes(q);
      if (!matchesQuery) return false;
      if (filter === 'all') return true;
      if (filter === 'completed') return t.completed === true;
      if (filter === 'in-progress') return t.completed === false;
      return true;
    });

    tasksContainer.innerHTML = '';
    if (filtered.length === 0) {
      const li = document.createElement('li');
      li.className = 'no-tasks';
      li.textContent = 'No tasks found. Create your first task!';
      tasksContainer.appendChild(li);
      return;
    }

    filtered.forEach(t => {
      const li = document.createElement('li');
      li.className = t.completed ? 'completed' : '';

      const left = document.createElement('div');
      left.className = 'task-left';
      
      const title = document.createElement('strong');
      title.textContent = t.title;
      
      const desc = document.createElement('p');
      desc.textContent = t.description || '';
      
      const date = document.createElement('small');
      date.className = 'task-date';
      date.textContent = formatDate(t.created_at);
      
      left.appendChild(title);
      left.appendChild(desc);
      left.appendChild(date);

      const status = document.createElement('span');
      status.className = 'task-status ' + (t.completed ? 'status-completed' : 'status-in-progress');
      status.textContent = t.completed ? 'Completed' : 'In Progress';

      li.appendChild(left);
      li.appendChild(status);
      tasksContainer.appendChild(li);
    });
  }

  function updateProgress() {
    const total = tasks.length;
    if (total === 0) {
      progressBarFill.style.width = '0%';
      progressText.textContent = 'No tasks yet. Start by creating one!';
      return;
    }
    
    const completed = tasks.filter(t => t.completed).length;
    const percent = Math.round((completed / total) * 100);
    
    progressBarFill.style.width = percent + '%';
    progressText.textContent = `${percent}% of tasks completed (${completed}/${total})`;
  }

  function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    return date.toLocaleDateString();
  }

  // Events
  searchInput.addEventListener('input', () => renderTasks());
  filterSelect.addEventListener('change', () => renderTasks());

  // Initial load
  loadTasks();

  // Refresh every 30 seconds to catch new tasks
  setInterval(loadTasks, 30000);
});