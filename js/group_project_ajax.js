document.addEventListener("DOMContentLoaded", function () {
    // Modal elements
    const projectModal = document.getElementById('projectModal');
    const deleteModal = document.getElementById('deleteModal');
    const projectForm = document.getElementById('projectForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtnText = document.getElementById('submitBtnText');
    const formAction = document.getElementById('formAction');
    const projectIdInput = document.getElementById('projectId');
    
    // Buttons
    const createProjectBtn = document.getElementById('createProjectBtn');
    const emptyStateCreateBtn = document.getElementById('emptyStateCreateBtn');
    const closeModal = document.getElementById('closeModal');
    const closeDeleteModal = document.getElementById('closeDeleteModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    // Variables
    let currentDeleteProjectId = null;
    
    // Open create project modal
    function openCreateModal() {
        projectForm.reset();
        projectIdInput.value = '';
        formAction.value = 'create';
        modalTitle.textContent = 'Create New Project';
        submitBtnText.textContent = 'Create Project';
        projectModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    // Open edit project modal
    function openEditModal(projectId) {
        // Get project data from the card
        const projectCard = document.querySelector(`[data-project-id="${projectId}"]`);
        if (!projectCard) return;
        
        const projectTitle = projectCard.querySelector('.project-title').textContent;
        const projectDescription = projectCard.querySelector('.project-description')?.textContent || '';
        const numMembers = projectCard.querySelector('.project-meta-item span')?.textContent.match(/\d+/)?.[0] || '';
        const leaderName = Array.from(projectCard.querySelectorAll('.project-meta-item span'))
            .find(span => !span.textContent.includes('Members'))?.textContent || '';
        
        projectIdInput.value = projectId;
        document.getElementById('projectName').value = projectTitle;
        document.getElementById('projectDescription').value = projectDescription;
        document.getElementById('numMembers').value = numMembers;
        document.getElementById('leaderName').value = leaderName;
        formAction.value = 'update';
        modalTitle.textContent = 'Edit Project';
        submitBtnText.textContent = 'Update Project';
        projectModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    // Close modals
    function closeModals() {
        projectModal.classList.remove('active');
        deleteModal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Handle form submission
    projectForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(projectForm);
        const action = formAction.value;
        formData.append('action', action);
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span id="submitBtnText">${action === 'create' ? 'Creating...' : 'Updating...'}</span>`;
        
        fetch('controllers/project_ajax_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span id="submitBtnText">${action === 'create' ? 'Create Project' : 'Update Project'}</span>`;
            
            if (data.success) {
                closeModals();
                location.reload(); // Reload to show updated projects
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span id="submitBtnText">${action === 'create' ? 'Create Project' : 'Update Project'}</span>`;
            alert('An error occurred. Please try again.');
        });
    });
    
    // Handle edit button clicks
    document.querySelectorAll('.edit-project-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const projectId = this.dataset.projectId;
            openEditModal(projectId);
        });
    });
    
    // Handle delete button clicks
    document.querySelectorAll('.delete-project-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentDeleteProjectId = this.dataset.projectId;
            deleteModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });
    
    // Confirm delete
    confirmDeleteBtn.addEventListener('click', function() {
        if (!currentDeleteProjectId) return;
        
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('project_id', currentDeleteProjectId);
        
        this.disabled = true;
        this.textContent = 'Deleting...';
        
        fetch('controllers/project_ajax_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.disabled = false;
            this.textContent = 'Delete';
            
            if (data.success) {
                closeModals();
                location.reload(); // Reload to show updated projects
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
    if (createProjectBtn) {
        createProjectBtn.addEventListener('click', openCreateModal);
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
    projectModal.addEventListener('click', function(e) {
        if (e.target === projectModal) {
            closeModals();
        }
    });
    
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            closeModals();
        }
    });
});

