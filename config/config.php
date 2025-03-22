<?php
// Configure session settings first, before starting the session
ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Lax'); // Prevents CSRF

// Now start the session after configuring it
session_start();

// Make sure session_regenerate_id is called at least once per session (prevents session fixation)
if (!isset($_SESSION['initialized'])) {
    session_regenerate_id();
    $_SESSION['initialized'] = true;
}

// Application URL - change this to your domain when deploying
define('BASE_URL', 'http://localhost/riafy'); // Updated to match the local project path

// OMDB API Config
define('OMDB_API_KEY', '2e25f567'); // API key provided by user
define('OMDB_API_URL', 'https://www.omdbapi.com/');

// Include database configuration
require_once 'database.php';

// Function to prevent XSS attacks
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Function to validate if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to redirect
function redirect($path) {
    header("Location: " . BASE_URL . "/" . $path);
    exit();
}

// Functions for handling session messages consistently
function setMessage($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
        
        // Clear the message after retrieving it
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        
        return ['message' => $message, 'type' => $type];
    }
    return null;
} 