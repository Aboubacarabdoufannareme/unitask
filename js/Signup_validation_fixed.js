// Modern Signup Form Validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signupForm');
    
    if (!form) {
        console.error('Signup form not found');
        return;
    }
    
    const fullNameInput = document.getElementById('fullName');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const termsCheckbox = document.querySelector('input[name="terms"]');

    // Real-time validation
    if (fullNameInput) fullNameInput.addEventListener('blur', validateFullName);
    if (emailInput) emailInput.addEventListener('blur', validateEmail);
    if (passwordInput) {
        passwordInput.addEventListener('blur', validatePassword);
        passwordInput.addEventListener('input', checkPasswordStrength);
    }
    if (confirmPasswordInput) confirmPasswordInput.addEventListener('blur', validateConfirmPassword);

    function validateFullName() {
        const value = fullNameInput.value.trim();
        if (value === "") {
            showError(fullNameInput, "Full name is required");
            return false;
        }
        if (value.length < 3) {
            showError(fullNameInput, "Full name must be at least 3 characters");
            return false;
        }
        clearError(fullNameInput);
        return true;
    }

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
        if (value.length < 6) {
            showError(passwordInput, "Password must be at least 6 characters");
            return false;
        }
        clearError(passwordInput);
        return true;
    }

    function validateConfirmPassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword === "") {
            showError(confirmPasswordInput, "Please confirm your password");
            return false;
        }
        if (password !== confirmPassword) {
            showError(confirmPasswordInput, "Passwords do not match");
            return false;
        }
        clearError(confirmPasswordInput);
        return true;
    }

    function checkPasswordStrength() {
        const value = passwordInput.value;
        const parent = passwordInput.parentElement;
        let strengthIndicator = parent.querySelector('.password-strength');
        
        if (!strengthIndicator && value.length > 0) {
            strengthIndicator = document.createElement('div');
            strengthIndicator.className = 'password-strength';
            parent.appendChild(strengthIndicator);
        }
        
        if (!strengthIndicator) return;
        
        let strength = 0;
        if (value.length >= 6) strength++;
        if (/(?=.*[a-z])/.test(value)) strength++;
        if (/(?=.*[A-Z])/.test(value)) strength++;
        if (/(?=.*\d)/.test(value)) strength++;
        if (/(?=.*[@$!%*?&])/.test(value)) strength++;
        
        const strengthLevels = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
        const strengthColors = ['#ef4444', '#f59e0b', '#eab308', '#10b981', '#059669'];
        
        strengthIndicator.textContent = `Password Strength: ${strengthLevels[strength - 1] || 'Too Short'}`;
        strengthIndicator.style.color = strengthColors[strength - 1] || '#6b7280';
        strengthIndicator.style.fontSize = '0.875rem';
        strengthIndicator.style.marginTop = '0.5rem';
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
        const isFullNameValid = validateFullName();
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();
        const isConfirmPasswordValid = validateConfirmPassword();
        
        if (termsCheckbox && !termsCheckbox.checked) {
            alert("Please agree to the Terms of Service and Privacy Policy");
            return;
        }

        if (!isFullNameValid || !isEmailValid || !isPasswordValid || !isConfirmPasswordValid) {
            return;
        }

        // Disable submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        const originalHTML = submitBtn.innerHTML;
        submitBtn.textContent = 'Creating Account...';

        // Submit to server using POST
        const formData = new FormData();
        formData.append('fullName', fullNameInput.value.trim());
        formData.append('email', emailInput.value.trim());
        formData.append('password', passwordInput.value);

        try {
            const response = await fetch('controllers/signup_process.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Show success message
                const alert = document.createElement('div');
                alert.className = 'alert alert-success';
                alert.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Account created successfully! Redirecting to dashboard...';
                form.insertBefore(alert, form.firstChild);
                
                // Redirect after 1 second
                setTimeout(() => {
                    window.location.href = "Dashboard.php";
                }, 1000);
            } else {
                // Show error message
                const alert = document.createElement('div');
                alert.className = 'alert alert-error';
                alert.textContent = '✗ ' + (data.message || 'Signup failed');
                form.insertBefore(alert, form.firstChild);
                
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
                
                // Remove alert after 5 seconds
                setTimeout(() => alert.remove(), 5000);
            }
        } catch (error) {
            console.error('Error:', error);
            const alert = document.createElement('div');
            alert.className = 'alert alert-error';
            alert.textContent = '✗ Network error: ' + error.message;
            form.insertBefore(alert, form.firstChild);
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
            
            setTimeout(() => alert.remove(), 5000);
        }
    });
});
