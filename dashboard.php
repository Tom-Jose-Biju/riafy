<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$search_results = [];
$search_error = '';
$search_query = '';

// Handle movie search
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
    $api_url = OMDB_API_URL . "?apikey=" . OMDB_API_KEY . "&s=" . urlencode($search_query);
    
    // Get data from API
    $response = file_get_contents($api_url);
    
    // Decode JSON response
    $data = json_decode($response, true);
    
    if (isset($data['Response']) && $data['Response'] === 'True') {
        $search_results = $data['Search'];
    } else {
        $search_error = isset($data['Error']) ? $data['Error'] : 'No results found';
    }
}

// Get user's favorite movies
$favorites = [];
$conn = connectDB();
$stmt = $conn->prepare("SELECT * FROM favorite_movies WHERE user_id = ? ORDER BY added_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $favorites[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Movie App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body>
    <?php 
    $messageData = getMessage();
    if ($messageData): 
    ?>
        <div class="hidden" data-message="<?= htmlspecialchars($messageData['message']) ?>" data-type="<?= htmlspecialchars($messageData['type']) ?>"></div>
    <?php endif; ?>
    
    <header>
        <div class="container">
            <nav>
                <div class="logo">Movie App</div>
                <div class="user-info">
                    Welcome, <?= $_SESSION['username'] ?>! 
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="search-section">
            <h2>Search Movies</h2>
            
            <form method="GET" action="dashboard.php" class="search-form">
                <input type="text" name="search" placeholder="Enter movie title..." value="<?= $search_query ?>" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            
            <!-- Test button for toast notifications -->
            <button type="button" class="btn btn-sm" onclick="showToast('This is a test notification', 'info')" style="margin-top: 10px;">Test Notification</button>
            
            <?php if (!empty($search_error)): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showToast('<?= addslashes($search_error) ?>', 'error');
                    });
                </script>
            <?php endif; ?>
            
            <?php if (!empty($search_results)): ?>
                <div class="search-results">
                    <div class="favorites-title">
                        <h3>Search Results</h3>
                        <div class="favorites-controls">
                            <button class="favorites-control search-scroll-left" aria-label="Scroll left">←</button>
                            <button class="favorites-control search-scroll-right" aria-label="Scroll right">→</button>
                        </div>
                    </div>
                    <div class="favorites-container">
                        <div class="favorites-scroll search-results-scroll" tabindex="0">
                            <?php foreach ($search_results as $movie): ?>
                                <div class="favorite-card search-movie-card">
                                    <div class="favorite-poster">
                                        <?php if ($movie['Poster'] !== 'N/A'): ?>
                                            <img src="<?= $movie['Poster'] ?>" alt="<?= $movie['Title'] ?> poster">
                                        <?php else: ?>
                                            <div class="no-poster">No poster</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="favorite-info">
                                        <div>
                                            <h4><?= $movie['Title'] ?> (<?= $movie['Year'] ?>)</h4>
                                            <p>Type: <?= ucfirst($movie['Type']) ?></p>
                                        </div>
                                        <div class="favorite-actions">
                                            <form method="POST" action="add_favorite.php">
                                                <input type="hidden" name="movie_id" value="<?= $movie['imdbID'] ?>">
                                                <input type="hidden" name="movie_title" value="<?= $movie['Title'] ?>">
                                                <input type="hidden" name="poster_url" value="<?= $movie['Poster'] ?>">
                                                <button type="submit" class="btn btn-sm">Add to Favorites</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="favorite-badge">Search</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>
        
        <section class="favorites-section">
            <div class="favorites-title">
                <h2>Your Favorite Movies</h2>
                <div class="favorites-controls">
                    <button class="favorites-control scroll-left" aria-label="Scroll left">←</button>
                    <button class="favorites-control scroll-right" aria-label="Scroll right">→</button>
                </div>
            </div>
            
            <?php if (empty($favorites)): ?>
                <div class="no-favorites">
                    <p>You haven't added any favorite movies yet.</p>
                </div>
            <?php else: ?>
                <div class="favorites-container">
                    <div class="favorites-scroll" tabindex="0">
                        <?php foreach ($favorites as $movie): ?>
                            <div class="favorite-card">
                                <div class="favorite-poster">
                                    <?php if ($movie['poster_url'] !== 'N/A'): ?>
                                        <img src="<?= $movie['poster_url'] ?>" alt="<?= $movie['movie_title'] ?> poster">
                                    <?php else: ?>
                                        <div class="no-poster">No poster</div>
                                    <?php endif; ?>
                                </div>
                                <div class="favorite-info">
                                    <div>
                                        <h4><?= $movie['movie_title'] ?></h4>
                                        <p>Added: <?= date('M d, Y', strtotime($movie['added_at'])) ?></p>
                                    </div>
                                    <div class="favorite-actions">
                                        <form method="POST" action="remove_favorite.php">
                                            <input type="hidden" name="id" value="<?= $movie['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="favorite-badge">Favorite</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Movie App. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 