// Modern Login Form Validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Real-time validation
    emailInput.addEventListener('blur', validateEmail);
    passwordInput.addEventListener('blur', validatePassword);

    function validateEmail() {
        const value = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (value === "") {
            showError(emailInput, "Email is required");
            return false;
        }
        if (!emailRegex.test(value)) {
            showError(emailInput, "Please enter a valid email address");
            return false;
        }
        clearError(emailInput);
        return true;
    }

    function validatePassword() {
        const value = passwordInput.value;
        
        if (value === "") {
            showError(passwordInput, "Password is required");
            return false;
        }
        if (value.length < 8) {
            showError(passwordInput, "Password must be at least 8 characters");
            return false;
        }
        clearError(passwordInput);
        return true;
    }

    function showError(input, message) {
        const parent = input.parentElement;
        let errorDiv = parent.querySelector('.error-message');
        
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            parent.appendChild(errorDiv);
        }
        
        errorDiv.textContent = message;
        input.classList.add('error');
    }

    function clearError(input) {
        const parent = input.parentElement;
        const errorDiv = parent.querySelector('.error-message');
        
        if (errorDiv) {
            errorDiv.remove();
        }
        input.classList.remove('error');
    }

    // Form submission
    form.addEventListener("submit", async function(e) {
        e.preventDefault();

        // Validate all fields
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();

        if (!isEmailValid || !isPasswordValid) {
            return;
        }

        // Disable submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Signing In...';

        // Submit to server using POST
        const formData = new FormData();
        formData.append('email', emailInput.value.trim());
        formData.append('password', passwordInput.value);

        try {
            const response = await fetch('controllers/login_process.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                const alert = document.createElement('div');
                alert.className = 'alert alert-success';
                alert.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Login successful! Redirecting to dashboard...';
                form.insertBefore(alert, form.firstChild);
                
                // Redirect immediately to dashboard
                window.location.href = "Dashboard.php";
            } else {
                // Show error message
                const alert = document.createElement('div');
                alert.className = 'alert alert-error';
                alert.textContent = '✗ ' + data.message;
                form.insertBefore(alert, form.firstChild);
                
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Sign In <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
                
                // Remove alert after 5 seconds
                setTimeout(() => alert.remove(), 5000);
            }
        } catch (error) {
            console.error('Error:', error);
            const alert = document.createElement('div');
            alert.className = 'alert alert-error';
            alert.textContent = '✗ Network error. Please try again.';
            form.insertBefore(alert, form.firstChild);
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Sign In <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
            
            setTimeout(() => alert.remove(), 5000);
        }
    });
});