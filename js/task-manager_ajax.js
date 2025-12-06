document.addEventListener("DOMContentLoaded", function () {
    // Modal elements
    const taskModal = document.getElementById('taskModal');
    const deleteModal = document.getElementById('deleteModal');
    const taskForm = document.getElementById('taskForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtnText = document.getElementById('submitBtnText');
    const formAction = document.getElementById('formAction');
    const taskIdInput = document.getElementById('taskId');
    
    // Buttons
    const createTaskBtn = document.getElementById('createTaskBtn');
    const emptyStateCreateBtn = document.getElementById('emptyStateCreateBtn');
    const closeModal = document.getElementById('closeModal');
    const closeDeleteModal = document.getElementById('closeDeleteModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    // Filter buttons
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    // Variables
    let currentDeleteTaskId = null;
    
    // Open create task modal
    function openCreateModal() {
        taskForm.reset();
        taskIdInput.value = '';
        formAction.value = 'create';
        modalTitle.textContent = 'Create New Task';
        submitBtnText.textContent = 'Create Task';
        taskModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    // Open edit task modal
    function openEditModal(taskId) {
        fetch(`controllers/task_ajax_handler.php?action=get&task_id=${taskId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const task = data.task;
                    taskIdInput.value = task.id;
                    document.getElementById('taskTitle').value = task.title;
                    document.getElementById('taskDescription').value = task.description || '';
                    document.getElementById('taskDueDate').value = task.due_date || '';
                    formAction.value = 'update';
                    modalTitle.textContent = 'Edit Task';
                    submitBtnText.textContent = 'Update Task';
                    taskModal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                } else {
                    alert('Failed to load task: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading the task.');
            });
    }
    
    // Close modals
    function closeModals() {
        taskModal.classList.remove('active');
        deleteModal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Handle form submission
    taskForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(taskForm);
        const action = formAction.value;
        formData.append('action', action);
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = action === 'create' ? 'Creating...' : 'Updating...';
        
        fetch('controllers/task_ajax_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span id="submitBtnText">${action === 'create' ? 'Create Task' : 'Update Task'}</span>`;
            
            if (data.success) {
                closeModals();
                location.reload(); // Reload to show updated tasks
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span id="submitBtnText">${action === 'create' ? 'Create Task' : 'Update Task'}</span>`;
            alert('An error occurred. Please try again.');
        });
    });
    
    // Handle task completion toggle
    document.querySelectorAll('.task-complete-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const taskCard = this.closest('.task-card');
            const isChecked = this.checked;
            
            const formData = new FormData();
            formData.append('action', 'toggle_complete');
            formData.append('task_id', taskId);
            
            fetch('controllers/task_ajax_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const status = data.status;
                    taskCard.dataset.status = status;
                    taskCard.classList.toggle('completed', status === 'completed');
                    
                    const statusBadge = taskCard.querySelector('.task-status-badge');
                    if (statusBadge) {
                        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                        statusBadge.className = `task-status-badge ${status}`;
                    }
                } else {
                    this.checked = !isChecked; // Revert checkbox
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !isChecked; // Revert checkbox
                alert('An error occurred. Please try again.');
            });
        });
    });
    
    // Handle edit button clicks
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            openEditModal(taskId);
        });
    });
    
    // Handle delete button clicks
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentDeleteTaskId = this.dataset.taskId;
            deleteModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });
    
    // Confirm delete
    confirmDeleteBtn.addEventListener('click', function() {
        if (!currentDeleteTaskId) return;
        
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('task_id', currentDeleteTaskId);
        
        this.disabled = true;
        this.textContent = 'Deleting...';
        
        fetch('controllers/task_ajax_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.disabled = false;
            this.textContent = 'Delete';
            
            if (data.success) {
                closeModals();
                location.reload(); // Reload to show updated tasks
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.disabled = false;
            this.textContent = 'Delete';
            alert('An error occurred. Please try again.');
        });
    });
    
    // Event listeners
    if (createTaskBtn) {
        createTaskBtn.addEventListener('click', openCreateModal);
    }
    
    if (emptyStateCreateBtn) {
        emptyStateCreateBtn.addEventListener('click', openCreateModal);
    }
    
    if (closeModal) {
        closeModal.addEventListener('click', closeModals);
    }
    
    if (closeDeleteModal) {
        closeDeleteModal.addEventListener('click', closeModals);
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModals);
    }
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', closeModals);
    }
    
    // Close modals when clicking outside
    taskModal.addEventListener('click', function(e) {
        if (e.target === taskModal) {
            closeModals();
        }
    });
    
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            closeModals();
        }
    });
    
    // Filter functionality
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter tasks
            const taskCards = document.querySelectorAll('.task-card');
            taskCards.forEach(card => {
                const status = card.dataset.status || 'pending';
                if (filter === 'all') {
                    card.style.display = 'flex';
                } else if (filter === status) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
