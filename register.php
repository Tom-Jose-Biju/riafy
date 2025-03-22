<?php
require_once 'config/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate form data
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $conn = connectDB();
        
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username or email already exists";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                setMessage("Registration successful! You can now login.", "success");
                redirect('login.php');
            } else {
                $error = "Error: " . $stmt->error;
            }
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
    <title>Register - Movie App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body class="register-page">
    <?php 
    $messageData = getMessage();
    if ($messageData): 
    ?>
        <div class="hidden" data-message="<?= htmlspecialchars($messageData['message']) ?>" data-type="<?= htmlspecialchars($messageData['type']) ?>"></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="hidden" data-message="<?= htmlspecialchars($error) ?>" data-type="error"></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="hidden" data-message="<?= htmlspecialchars($success) ?>" data-type="success"></div>
    <?php endif; ?>
    
    <div class="container register-container">
        <div class="register-header">
            <h1>Create Account</h1>
            <p>Join our movie community to track your favorite films, get personalized recommendations, and more.</p>
        </div>
        
        <div class="validation-info">
            <h3>Registration Requirements</h3>
            <ul>
                <li><strong>Username:</strong> Must be unique and not already taken</li>
                <li><strong>Email:</strong> Must be a valid email format and not already registered</li>
                <li><strong>Password:</strong> Must be at least 6 characters long</li>
                <li><strong>Confirm Password:</strong> Must match your password</li>
            </ul>
        </div>
        
        <form method="POST" action="register.php" class="register-form">
            <div class="form-columns">
                <div class="form-column">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Choose a unique username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Your email address" required>
                    </div>
                </div>
                
                <div class="form-column">
                    <div class="form-group">
                        <label for="password">Create Password</label>
                        <input type="password" id="password" name="password" placeholder="Minimum 6 characters" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat your password" required>
                    </div>
                </div>
            </div>
            
            <div class="register-actions">
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        </form>
        
        <div class="form-footer">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form elements
            const form = document.querySelector('form');
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            
            // Create validation message containers for each field
            const fields = [usernameInput, emailInput, passwordInput, confirmPasswordInput];
            
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
                    } else if (field.value.length < 3) {
                        showError(field, validationMessage, 'Username must be at least 3 characters', formGroup);
                    } else {
                        showSuccess(field, validationMessage, 'Username looks good!', formGroup);
                    }
                }
                
                // Email validation
                if (field.id === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (field.value.trim() === '') {
                        showError(field, validationMessage, 'Email is required', formGroup);
                    } else if (!emailRegex.test(field.value)) {
                        showError(field, validationMessage, 'Please enter a valid email', formGroup);
                    } else {
                        showSuccess(field, validationMessage, 'Email format is valid', formGroup);
                    }
                }
                
                // Password validation
                if (field.id === 'password') {
                    if (field.value === '') {
                        showError(field, validationMessage, 'Password is required', formGroup);
                    } else if (field.value.length < 6) {
                        showError(field, validationMessage, 'Password must be at least 6 characters', formGroup);
                    } else {
                        showSuccess(field, validationMessage, 'Password is strong enough', formGroup);
                        
                        // Also check confirm password if it has a value
                        if (confirmPasswordInput.value !== '') {
                            validateField(confirmPasswordInput);
                        }
                    }
                }
                
                // Confirm password validation
                if (field.id === 'confirm_password') {
                    if (field.value === '') {
                        showError(field, validationMessage, 'Please confirm your password', formGroup);
                    } else if (field.value !== passwordInput.value) {
                        showError(field, validationMessage, 'Passwords do not match', formGroup);
                    } else {
                        showSuccess(field, validationMessage, 'Passwords match!', formGroup);
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