<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ShopEasy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <style>
        :root {
            --primary: #3a86ff;
            --primary-dark: #2563eb;
            --secondary: #ff006e;
            --accent: #8338ec;
            --success: #4cc9f0;
            --warning: #ffbe0b;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            padding: 25px;
            text-align: center;
        }

        .auth-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .auth-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .auth-tabs {
            display: flex;
            border-bottom: 1px solid var(--light-gray);
        }

        .auth-tab {
            flex: 1;
            padding: 18px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            color: var(--gray);
            position: relative;
            transition: var(--transition);
        }

        .auth-tab.active {
            color: var(--primary);
        }

        .auth-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary);
        }

        .auth-content {
            padding: 30px;
        }

        .auth-form {
            display: none;
        }

        .auth-form.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--light-gray);
            background-color: #f9f9f9;
            font-size: 16px;
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.2);
        }

        .password-field {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            margin-right: 8px;
            width: auto;
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border-radius: var(--border-radius-sm);
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn:disabled {
            background-color: var(--light-gray);
            color: var(--gray);
            cursor: not-allowed;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
            font-size: 14px;
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .password-requirements {
            background-color: #f8f9fa;
            border-radius: var(--border-radius-sm);
            padding: 15px;
            margin-top: 15px;
            font-size: 13px;
            color: var(--gray);
        }

        .password-requirements ul {
            list-style: none;
            margin-top: 8px;
        }

        .password-requirements li {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .password-requirements i {
            margin-right: 8px;
            font-size: 12px;
        }

        .requirement-met {
            color: var(--success);
        }

        .requirement-not-met {
            color: var(--gray);
        }

        /* Notification Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .toast {
            background-color: white;
            border-radius: var(--border-radius-sm);
            padding: 15px 20px;
            margin-bottom: 10px;
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease, fadeOut 0.5s ease 2.5s forwards;
            min-width: 300px;
        }

        .toast-success {
            border-left: 4px solid var(--success);
        }

        .toast-error {
            border-left: 4px solid var(--secondary);
        }

        .toast-warning {
            border-left: 4px solid var(--warning);
        }

        .toast-icon {
            margin-right: 15px;
            font-size: 20px;
        }

        .toast-success .toast-icon {
            color: var(--success);
        }

        .toast-error .toast-icon {
            color: var(--secondary);
        }

        .toast-warning .toast-icon {
            color: var(--warning);
        }

        .toast-content {
            flex: 1;
        }

        .toast-message {
            font-size: 14px;
            color: var(--gray);
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .auth-container {
                max-width: 100%;
            }
            
            .auth-content {
                padding: 20px;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>Welcome to ShopEasy</h1>
            <p>Sign in or create an account to continue</p>
        </div>
        
        <div class="auth-tabs">
            <button class="auth-tab active" data-tab="login">Login</button>
            <button class="auth-tab" data-tab="register">Register</button>
        </div>

        <div class="auth-content">
            <!-- Login Form -->
            <form class="auth-form active" id="login-form">
                <div class="form-group">
                    <label for="login-email">Email Address</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <div class="password-field">
                        <input type="password" id="login-password" name="password" required>
                        <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember-me" name="remember_me">
                        <label for="remember-me">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>
                <button type="submit" class="btn btn-primary" id="login-btn">Login</button>
                <div class="form-footer">
                    Don't have an account? <a href="#" id="switch-to-register">Register now</a>
                </div>
            </form>

            <!-- Register Form -->
            <form class="auth-form" id="register-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="register-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="register-last-name">Last Name</label>
                    <input type="text" id="register-last-name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email Address</label>
                    <input type="email" id="register-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Password</label>
                    <div class="password-field">
                        <input type="password" id="register-password" name="password" required>
                        <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-requirements">
                        <p>Password must meet the following requirements:</p>
                        <ul>
                            <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                            <li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                            <li id="req-number"><i class="fas fa-circle"></i> One number</li>
                            <li id="req-special"><i class="fas fa-circle"></i> One special character</li>
                        </ul>
                    </div>
                </div>
                <div class="form-group">
                    <label for="register-confirm-password">Confirm Password</label>
                    <div class="password-field">
                        <input type="password" id="register-confirm-password" name="confirm_password" required>
                        <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="register-btn" disabled>Create Account</button>
                <div class="form-footer">
                    Already have an account? <a href="#" id="switch-to-login">Login now</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification Toast Container -->
    <div class="toast-container" id="toast-container">
        <!-- Toasts will be added here dynamically -->
    </div>

    <script>
        // DOM Content Loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeAuth();
        });

        // Initialize the authentication functionality
        function initializeAuth() {
            console.log('Auth modal initialized');
            
            // Setup event listeners
            setupAuthEventListeners();
        }

        // Setup event listeners
        function setupAuthEventListeners() {
            // Tab switching
            const authTabs = document.querySelectorAll('.auth-tab');
            const authForms = document.querySelectorAll('.auth-form');
            
            authTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.dataset.tab;
                    
                    // Update active tab
                    authTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show corresponding form
                    authForms.forEach(form => {
                        form.classList.remove('active');
                        if (form.id === `${tabName}-form`) {
                            form.classList.add('active');
                        }
                    });
                });
            });
            
            // Switch to register form
            const switchToRegister = document.getElementById('switch-to-register');
            if (switchToRegister) {
                switchToRegister.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector('[data-tab="register"]').click();
                });
            }
            
            // Switch to login form
            const switchToLogin = document.getElementById('switch-to-login');
            if (switchToLogin) {
                switchToLogin.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector('[data-tab="login"]').click();
                });
            }
            
            // Toggle password visibility
            const togglePasswordButtons = document.querySelectorAll('.toggle-password');
            togglePasswordButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const icon = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
            
            // Password validation
            const passwordInput = document.getElementById('register-password');
            if (passwordInput) {
                passwordInput.addEventListener('input', validatePassword);
            }
            
            // Confirm password validation
            const confirmPasswordInput = document.getElementById('register-confirm-password');
            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', validateConfirmPassword);
            }
            
            // Form submissions
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleLogin(this);
                });
            }
            
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleRegistration(this);
                });
            }
        }

        // Validate password strength
        function validatePassword() {
            const password = document.getElementById('register-password').value;
            const registerBtn = document.getElementById('register-btn');
            
            // Check requirements
            const hasMinLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            
            // Update requirement indicators
            updateRequirementIndicator('req-length', hasMinLength);
            updateRequirementIndicator('req-uppercase', hasUppercase);
            updateRequirementIndicator('req-number', hasNumber);
            updateRequirementIndicator('req-special', hasSpecialChar);
            
            // Enable/disable register button
            const isValid = hasMinLength && hasUppercase && hasNumber && hasSpecialChar;
            registerBtn.disabled = !isValid;
            
            return isValid;
        }
        
        // Update requirement indicator
        function updateRequirementIndicator(elementId, isMet) {
            const element = document.getElementById(elementId);
            if (element) {
                const icon = element.querySelector('i');
                if (isMet) {
                    icon.classList.add('requirement-met');
                    icon.classList.remove('requirement-not-met');
                } else {
                    icon.classList.add('requirement-not-met');
                    icon.classList.remove('requirement-met');
                }
            }
        }
        
        // Validate confirm password
        function validateConfirmPassword() {
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            
            if (confirmPassword && password !== confirmPassword) {
                showToast('Passwords do not match', 'error');
                return false;
            }
            
            return true;
        }

        // Handle login form submission
        function handleLogin(form) {
            const formData = new FormData(form);
            const email = formData.get('email');
            const password = formData.get('password');
            const rememberMe = formData.get('remember_me') === 'on';
            
            // Basic validation
            if (!email || !password) {
                showToast('Please fill in all fields', 'error');
                return;
            }
            
            if (!validateEmail(email)) {
                showToast('Please enter a valid email address', 'error');
                return;
            }
            
            // Show loading state
            const loginBtn = document.getElementById('login-btn');
            const originalText = loginBtn.textContent;
            loginBtn.textContent = 'Logging in...';
            loginBtn.disabled = true;
            
            // In a real application, this would be an AJAX call to your backend
            loginUser(email, password, rememberMe)
                .then(response => {
                    showToast('Login successful! Redirecting...', 'success');
                    
                    // Store auth token
                    localStorage.setItem('authToken', response.token);
                    localStorage.setItem('userData', JSON.stringify(response.user));
                    
                    // Redirect to homepage or intended destination
                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 1500);
                })
                .catch(error => {
                    showToast(error.message, 'error');
                })
                .finally(() => {
                    loginBtn.textContent = originalText;
                    loginBtn.disabled = false;
                });
        }

        // Handle registration form submission
        function handleRegistration(form) {
            const formData = new FormData(form);
            const firstName = formData.get('first_name');
            const lastName = formData.get('last_name');
            const email = formData.get('email');
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            // Basic validation
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                showToast('Please fill in all fields', 'error');
                return;
            }
            
            if (!validateEmail(email)) {
                showToast('Please enter a valid email address', 'error');
                return;
            }
            
            if (!validatePassword()) {
                showToast('Password does not meet requirements', 'error');
                return;
            }
            
            if (!validateConfirmPassword()) {
                return;
            }
            
            // Show loading state
            const registerBtn = document.getElementById('register-btn');
            const originalText = registerBtn.textContent;
            registerBtn.textContent = 'Creating account...';
            registerBtn.disabled = true;
            
            // In a real application, this would be an AJAX call to your backend
            registerUser(firstName, lastName, email, password)
                .then(response => {
                    showToast('Account created successfully! You can now login.', 'success');
                    
                    // Switch to login form
                    document.querySelector('[data-tab="login"]').click();
                    
                    // Pre-fill email field
                    document.getElementById('login-email').value = email;
                    
                    // Clear form
                    form.reset();
                    
                    // Reset password validation
                    resetPasswordValidation();
                })
                .catch(error => {
                    showToast(error.message, 'error');
                })
                .finally(() => {
                    registerBtn.textContent = originalText;
                    registerBtn.disabled = false;
                });
        }
        
        // Reset password validation UI
        function resetPasswordValidation() {
            const requirements = ['req-length', 'req-uppercase', 'req-number', 'req-special'];
            requirements.forEach(req => {
                updateRequirementIndicator(req, false);
            });
        }

        // Login user (API call)
        function loginUser(email, password, rememberMe) {
            return new Promise((resolve, reject) => {
                // This is where you would implement your actual AJAX call
                // Example using fetch:
                /*
                fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password, remember_me: rememberMe })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Login failed');
                    }
                    return response.json();
                })
                .then(data => resolve(data))
                .catch(error => reject(error));
                */
                
                // For demo purposes, simulate API call
                setTimeout(() => {
                    // Simulate successful login
                    if (email && password) {
                        resolve({
                            token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ',
                            user: {
                                id: Math.floor(Math.random() * 1000),
                                firstName: 'User',
                                lastName: 'Demo',
                                email: email
                            }
                        });
                    } else {
                        reject(new Error('Invalid email or password'));
                    }
                }, 1000);
            });
        }

        // Register user (API call)
        function registerUser(firstName, lastName, email, password) {
            return new Promise((resolve, reject) => {
                // This is where you would implement your actual AJAX call
                // Example using fetch:
                /*
                fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ first_name: firstName, last_name: lastName, email, password })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Registration failed');
                    }
                    return response.json();
                })
                .then(data => resolve(data))
                .catch(error => reject(error));
                */
                
                // For demo purposes, simulate API call
                setTimeout(() => {
                    // Simulate successful registration
                    resolve({
                        message: 'User registered successfully',
                        userId: Math.floor(Math.random() * 1000)
                    });
                }, 1000);
            });
        }

        // Validate email format
        function validateEmail(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle'
            };
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas ${icons[type]}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            toastContainer.appendChild(toast);
            
            // Remove toast after 3 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        }
    </script>
</body>
</html>