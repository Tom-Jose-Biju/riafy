<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Get favorite movie ID
    $favorite_id = (int)$_POST['id'];
    $user_id = $_SESSION['user_id'];
    
    $conn = connectDB();
    
    // Delete favorite movie (only if it belongs to the current user)
    $stmt = $conn->prepare("DELETE FROM favorite_movies WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $favorite_id, $user_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        setMessage("Movie removed from favorites!", "success");
    } else {
        setMessage("Error removing movie from favorites.", "error");
    }
    
    $stmt->close();
    $conn->close();
}

// Redirect back to dashboard
redirect('dashboard.php');
?> 