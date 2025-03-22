<?php
require_once 'config/config.php';

$error = '';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validate form data
    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        $conn = connectDB();
        
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Set welcome message for first login
                setMessage("Welcome back, " . $user['username'] . "!", "success");
                
                // Redirect to dashboard
                redirect('dashboard.php');
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Movie App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body class="login-page">
    <?php 
    $messageData = getMessage();
    if ($messageData): 
    ?>
        <div class="hidden" data-message="<?= htmlspecialchars($messageData['message']) ?>" data-type="<?= htmlspecialchars($messageData['type']) ?>"></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="hidden" data-message="<?= htmlspecialchars($error) ?>" data-type="error"></div>
    <?php endif; ?>
    
    <div class="container register-container">
        <div class="register-header">
            <h1>Welcome Back</h1>
            <p>Log in to access your favorite movies, personalized recommendations, and more.</p>
        </div>
        
        <form method="POST" action="login.php" class="register-form">
            <div class="form-column-full">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="form-group login-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember" class="checkbox-label">Remember me</label>
                    </div>
                    <div class="forgot-password">
                        <a href="#">Forgot Password?</a>
                    </div>
                </div>
            </div>
            
            <div class="register-actions">
                <button type="submit" class="btn btn-primary">Sign In</button>
            </div>
        </form>
        
        <div class="form-footer">
            <p>Don't have an account? <a href="register.php">Create Account</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form elements
            const form = document.querySelector('form');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            
            // Create validation message containers for each field
            const fields = [usernameInput, passwordInput];
            
            fields.forEach(field => {
                // Create validation message element
                const validationMessage = document.createElement('div');
                validationMessage.className = 'field-validation';
                validationMessage.style.display = 'none';
                field.parentNode.appendChild(validationMessage);
                
                // Add input event listeners
                field.addEventListener('input', function() {
                    validateField(field);
                });
                
                // Add blur event listener
                field.addEventListener('blur', function() {
                    validateField(field);
                });
            });
            
            // Validate individual field
            function validateField(field) {
                const validationMessage = field.parentNode.querySelector('.field-validation');
                const formGroup = field.parentNode;
                
                // Reset validation state
                field.classList.remove('valid', 'invalid');
                formGroup.classList.remove('has-error', 'has-success');
                validationMessage.style.display = 'none';
                
                // Username validation
                if (field.id === 'username') {
                    if (field.value.trim() === '') {
                        showError(field, validationMessage, 'Username is required', formGroup);
                    } else {
                        showSuccess(field, validationMessage, 'Username format is valid', formGroup);
                    }
                }
                
                // Password validation
                if (field.id === 'password') {
                    if (field.value === '') {
                        showError(field, validationMessage, 'Password is required', formGroup);
                    } else {
                        showSuccess(field, validationMessage, 'Password format is valid', formGroup);
                    }
                }
            }
            
            // Show error message
            function showError(field, messageElement, message, formGroup) {
                field.classList.add('invalid');
                field.classList.remove('valid');
                formGroup.classList.add('has-error');
                formGroup.classList.remove('has-success');
                messageElement.textContent = message;
                messageElement.className = 'field-validation error-message';
                messageElement.style.display = 'block';
            }
            
            // Show success message
            function showSuccess(field, messageElement, message, formGroup) {
                field.classList.add('valid');
                field.classList.remove('invalid');
                formGroup.classList.add('has-success');
                formGroup.classList.remove('has-error');
                messageElement.textContent = message;
                messageElement.className = 'field-validation success-message';
                messageElement.style.display = 'block';
            }
            
            // Form submission validation
            form.addEventListener('submit', function(e) {
                // Validate all fields
                fields.forEach(field => validateField(field));
                
                // Check if any field is invalid
                const hasInvalidFields = form.querySelector('.invalid');
                
                if (hasInvalidFields) {
                    e.preventDefault();
                    showToast('Please fix the form errors before submitting', 'error');
                }
            });
        });
    </script>
</body>
</html> 