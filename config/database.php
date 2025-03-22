<?php
define('DB_HOST', 'sql12.freesqldatabase.com');  // freesqldatabase.com server
define('DB_USER', 'sql12768949'); // Database username
define('DB_PASS', 'MvnHCafq92'); // Database password
define('DB_NAME', 'sql12768949'); // Database name

// Create a database connection
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
} 