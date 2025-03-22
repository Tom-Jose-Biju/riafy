// Modify this file to use your API instead of Firebase
document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');
  const errorMessage = document.getElementById('error-message');

  // Handle login form submission
  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      
      try {
        // Change this URL to your actual API endpoint
        // For Option A: Use your PHP API URL
        // const response = await fetch('https://your-php-host.com/login_api.php', {
        
        // For Option B: Use Netlify Function
        const response = await fetch('/.netlify/functions/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        
        if (data.success) {
          // Store user info in localStorage
          localStorage.setItem('user', JSON.stringify(data.user));
          // Redirect to dashboard
          window.location.href = 'dashboard.html';
        } else {
          errorMessage.textContent = data.message;
        }
      } catch (error) {
        errorMessage.textContent = "An error occurred. Please try again.";
        console.error(error);
      }
    });
  }

  // Handle register form submission
  if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const username = document.getElementById('username').value;
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm-password').value;
      
      // Check if passwords match
      if (password !== confirmPassword) {
        errorMessage.textContent = "Passwords do not match";
        return;
      }
      
      try {
        // Create user in Firebase
        const userCredential = await auth.createUserWithEmailAndPassword(email, password);
        
        // Update profile to add username
        await userCredential.user.updateProfile({
          displayName: username
        });
        
        // Redirect to dashboard after successful registration
        window.location.href = 'dashboard.html';
      } catch (error) {
        errorMessage.textContent = error.message;
      }
    });
  }

  // Function to check if user is logged in
  function checkAuth() {
    const user = JSON.parse(localStorage.getItem('user'));
    
    if (user) {
      // User is logged in
      console.log('User is logged in:', user.username);
      
      // If on login or register page, redirect to dashboard
      if (window.location.pathname.includes('login.html') || 
          window.location.pathname.includes('register.html')) {
        window.location.href = 'dashboard.html';
      }
    } else {
      // User is logged out
      console.log('User is logged out');
      
      // If on dashboard page, redirect to login
      if (window.location.pathname.includes('dashboard.html')) {
        window.location.href = 'login.html';
      }
    }
  }
  
  // Check auth status when page loads
  checkAuth();
}); 