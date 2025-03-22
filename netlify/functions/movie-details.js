const fetch = require('node-fetch');

exports.handler = async function(event, context) {
  // Get movie ID from query parameter
  const movieId = event.queryStringParameters.id;
  const apiKey = process.env.OMDB_API_KEY; // Store API key in Netlify environment variables
  
  if (!movieId) {
    return {
      statusCode: 400,
      body: JSON.stringify({ error: "Movie ID is required" })
    };
  }
  
  try {
    const response = await fetch(`https://www.omdbapi.com/?apikey=${apiKey}&i=${movieId}`);
    const data = await response.json();
    
    return {
      statusCode: 200,
      body: JSON.stringify(data)
    };
  } catch (error) {
    return {
      statusCode: 500,
      body: JSON.stringify({ error: "Failed to fetch movie details" })
    };
  }
}; 