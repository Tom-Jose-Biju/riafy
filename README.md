# Movie Favorites App

A PHP web application that allows users to search for movies using the OMDB API and save their favorite movies.

## Features

- User Authentication (Register/Login)
- Search movies using OMDB API
- Add/Remove favorite movies
- Responsive design

## Requirements

- PHP 7.4+
- MySQL
- Web server (Apache/Nginx)
- OMDB API Key

## Installation

1. Clone this repository to your web server directory:
   ```bash
   git clone https://github.com/yourusername/movie-app.git
   ```

2. Import the database schema:
   - Create a new MySQL database
   - Configure database credentials in `config/database.php`
   - Run the database setup script by visiting `http://yoursite.com/db_setup.php`

3. Get an OMDB API key:
   - Visit [OMDB API](https://www.omdbapi.com/apikey.aspx) to request a free API key
   - Update `config/config.php` with your API key:
     ```php
     define('OMDB_API_KEY', 'your_api_key_here');
     ```

4. Update the base URL in `config/config.php` to match your domain:
   ```php
   define('BASE_URL', 'http://yourdomain.com');
   ```

5. Set appropriate file permissions:
   ```bash
   chmod 755 -R /path/to/movie-app
   ```

## Usage

1. Register for a new account
2. Login with your credentials
3. Search for movies using the search bar
4. Click "Add to Favorites" to save a movie to your list
5. View and manage your favorite movies in the dashboard

## Security Features

- Password hashing using PHP's password_hash()
- Prepared statements to prevent SQL injection
- Input sanitization for XSS prevention
- Session management for authentication

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Favorite Movies Table
```sql
CREATE TABLE favorite_movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_title VARCHAR(255) NOT NULL,
    movie_id VARCHAR(50) NOT NULL,
    poster_url VARCHAR(255),
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Free Hosting Options

You can deploy this application on:
- [InfinityFree](https://infinityfree.net/)
- [000webhost](https://www.000webhost.com/)
- [Heroku](https://www.heroku.com/) (with ClearDB MySQL add-on)

## License

This project is open-source, feel free to use and modify it. 