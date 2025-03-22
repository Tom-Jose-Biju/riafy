<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get movie data
    $movie_id = sanitize($_POST['movie_id']);
    $movie_title = sanitize($_POST['movie_title']);
    $poster_url = sanitize($_POST['poster_url']);
    $user_id = $_SESSION['user_id'];
    
    $conn = connectDB();
    
    // Check if movie is already in favorites
    $stmt = $conn->prepare("SELECT id FROM favorite_movies WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("is", $user_id, $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Movie already in favorites
        setMessage("This movie is already in your favorites.", "error");
    } else {
        // Add movie to favorites
        $stmt = $conn->prepare("INSERT INTO favorite_movies (user_id, movie_title, movie_id, poster_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $movie_title, $movie_id, $poster_url);
        
        if ($stmt->execute()) {
            setMessage("Movie added to favorites!", "success");
        } else {
            setMessage("Error adding movie to favorites.", "error");
        }
    }
    
    $stmt->close();
    $conn->close();
}

// Redirect back to dashboard
redirect('dashboard.php');
?> 