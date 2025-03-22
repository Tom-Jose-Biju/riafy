document.addEventListener('DOMContentLoaded', () => {
  // Get user from localStorage
  const user = JSON.parse(localStorage.getItem('user'));
  
  // DOM elements
  const usernameElement = document.getElementById('username');
  const logoutBtn = document.getElementById('logout-btn');
  const searchForm = document.getElementById('search-form');
  const searchInput = document.getElementById('search-input');
  const searchResults = document.getElementById('search-results');
  const favoritesList = document.getElementById('favorites-list');
  
  // OMDB API Key
  const OMDB_API_KEY = '2e25f567'; // Your API Key
  
  // Redirect if not logged in
  if (!user) {
    window.location.href = 'login.html';
    return;
  }
  
  // Set username in the UI
  usernameElement.textContent = user.username;
  
  // Logout handler
  logoutBtn.addEventListener('click', () => {
    localStorage.removeItem('user');
    window.location.href = 'login.html';
  });
  
  // Search form handler
  searchForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const searchTerm = searchInput.value.trim();
    
    if (searchTerm) {
      try {
        const response = await fetch(`https://www.omdbapi.com/?apikey=${OMDB_API_KEY}&s=${encodeURIComponent(searchTerm)}`);
        const data = await response.json();
        
        if (data.Response === 'True') {
          displaySearchResults(data.Search);
        } else {
          searchResults.innerHTML = `<p class="error-message">${data.Error || 'No results found'}</p>`;
        }
      } catch (error) {
        console.error('Error fetching search results:', error);
        searchResults.innerHTML = '<p class="error-message">An error occurred while searching. Please try again.</p>';
      }
    }
  });
  
  // Display search results
  function displaySearchResults(movies) {
    searchResults.innerHTML = '';
    
    if (!movies || movies.length === 0) {
      searchResults.innerHTML = '<p>No movies found.</p>';
      return;
    }
    
    const resultsContainer = document.createElement('div');
    resultsContainer.className = 'movies-grid';
    
    movies.forEach(movie => {
      const movieCard = document.createElement('div');
      movieCard.className = 'movie-card';
      
      const posterUrl = movie.Poster !== 'N/A' ? movie.Poster : 'placeholder-image.jpg';
      
      movieCard.innerHTML = `
        <div class="movie-poster">
          <img src="${posterUrl}" alt="${movie.Title} poster">
        </div>
        <div class="movie-info">
          <h3>${movie.Title} (${movie.Year})</h3>
          <p>Type: ${movie.Type}</p>
          <button class="btn btn-sm add-favorite" data-id="${movie.imdbID}" data-title="${movie.Title}" data-poster="${posterUrl}">
            Add to Favorites
          </button>
        </div>
      `;
      
      resultsContainer.appendChild(movieCard);
    });
    
    searchResults.appendChild(resultsContainer);
    
    // Add event listeners to "Add to Favorites" buttons
    document.querySelectorAll('.add-favorite').forEach(button => {
      button.addEventListener('click', addToFavorites);
    });
  }
  
  // Add movie to favorites
  async function addToFavorites(e) {
    const user = JSON.parse(localStorage.getItem('user'));
    
    if (!user) {
      window.location.href = 'login.html';
      return;
    }
    
    const movieId = e.target.dataset.id;
    const movieTitle = e.target.dataset.title;
    const posterUrl = e.target.dataset.poster;
    
    try {
      await fetch('/api/add-favorite', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          user_id: user.id,
          movie_id: movieId,
          movie_title: movieTitle,
          poster_url: posterUrl
        })
      });
      
      alert(`${movieTitle} has been added to your favorites!`);
      loadFavorites(user.id);
    } catch (error) {
      console.error('Error adding favorite:', error);
      alert('An error occurred while adding to favorites. Please try again.');
    }
  }
  
  // Load user's favorites
  async function loadFavorites(userId) {
    try {
      const response = await fetch('/api/favorites?user_id=' + userId);
      const data = await response.json();
      
      favoritesList.innerHTML = '';
      
      if (data.length === 0) {
        favoritesList.innerHTML = '<p>You haven\'t added any favorite movies yet.</p>';
        return;
      }
      
      const favoritesContainer = document.createElement('div');
      favoritesContainer.className = 'movies-grid';
      
      data.forEach(favorite => {
        const favoriteCard = document.createElement('div');
        favoriteCard.className = 'movie-card favorite';
        
        favoriteCard.innerHTML = `
          <div class="movie-poster">
            <img src="${favorite.poster_url}" alt="${favorite.movie_title} poster">
          </div>
          <div class="movie-info">
            <h3>${favorite.movie_title}</h3>
            <p>Added: ${new Date(favorite.added_at).toLocaleDateString()}</p>
            <button class="btn btn-sm btn-danger remove-favorite" data-id="${favorite.id}">
              Remove
            </button>
          </div>
        `;
        
        favoritesContainer.appendChild(favoriteCard);
      });
      
      favoritesList.appendChild(favoritesContainer);
      
      // Add event listeners to "Remove" buttons
      document.querySelectorAll('.remove-favorite').forEach(button => {
        button.addEventListener('click', removeFromFavorites);
      });
    } catch (error) {
      console.error('Error loading favorites:', error);
      favoritesList.innerHTML = '<p class="error-message">An error occurred while loading favorites. Please try again.</p>';
    }
  }
  
  // Remove movie from favorites
  async function removeFromFavorites(e) {
    const favoriteId = e.target.dataset.id;
    
    try {
      await fetch('/api/favorites/' + favoriteId, {
        method: 'DELETE'
      });
      
      alert('Movie has been removed from your favorites!');
      loadFavorites(JSON.parse(localStorage.getItem('user')).id);
    } catch (error) {
      console.error('Error removing favorite:', error);
      alert('An error occurred while removing from favorites. Please try again.');
    }
  }
}); 