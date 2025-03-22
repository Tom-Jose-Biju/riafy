/**
 * Toast Notification System for Movie Favorites App
 */

// Film Booking Site Theme - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the toast notification system
    initToasts();
    
    // Add movie card hover effects
    initMovieCards();
    
    // Initialize favorites scroll controls
    initFavoritesScroll();
    
    // Check for message data attributes in hidden divs
    const messageDiv = document.querySelector('div[data-message]');
    if (messageDiv) {
        const message = messageDiv.getAttribute('data-message');
        const type = messageDiv.getAttribute('data-type') || 'info';
        
        if (message) {
            showToast(message, type);
        }
    }
    
    // Add cinematic splash effect on page load
    addCinematicSplash();
});

// Initialize favorites scroll functionality
function initFavoritesScroll() {
    // Handle favorites scroll
    const favoritesScrollContainer = document.querySelector('.favorites-scroll');
    if (favoritesScrollContainer) {
        const leftBtn = document.querySelector('.scroll-left');
        const rightBtn = document.querySelector('.scroll-right');
        
        if (leftBtn && rightBtn) {
            // Scroll left button click handler
            leftBtn.addEventListener('click', () => {
                favoritesScrollContainer.scrollBy({
                    left: -300,
                    behavior: 'smooth'
                });
            });
            
            // Scroll right button click handler
            rightBtn.addEventListener('click', () => {
                favoritesScrollContainer.scrollBy({
                    left: 300,
                    behavior: 'smooth'
                });
            });
        }
        
        // Add keyboard navigation for accessibility
        favoritesScrollContainer.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                favoritesScrollContainer.scrollBy({
                    left: -100,
                    behavior: 'smooth'
                });
            } else if (e.key === 'ArrowRight') {
                favoritesScrollContainer.scrollBy({
                    left: 100,
                    behavior: 'smooth'
                });
            }
        });
    }
    
    // Handle search results scroll
    const searchScrollContainer = document.querySelector('.search-results-scroll');
    if (searchScrollContainer) {
        const searchLeftBtn = document.querySelector('.search-scroll-left');
        const searchRightBtn = document.querySelector('.search-scroll-right');
        
        if (searchLeftBtn && searchRightBtn) {
            // Scroll left button click handler
            searchLeftBtn.addEventListener('click', () => {
                searchScrollContainer.scrollBy({
                    left: -300,
                    behavior: 'smooth'
                });
            });
            
            // Scroll right button click handler
            searchRightBtn.addEventListener('click', () => {
                searchScrollContainer.scrollBy({
                    left: 300,
                    behavior: 'smooth'
                });
            });
        }
        
        // Add keyboard navigation for accessibility
        searchScrollContainer.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                searchScrollContainer.scrollBy({
                    left: -100,
                    behavior: 'smooth'
                });
            } else if (e.key === 'ArrowRight') {
                searchScrollContainer.scrollBy({
                    left: 100,
                    behavior: 'smooth'
                });
            }
        });
    }
}

// Initialize toast notification system
function initToasts() {
    // Create toast container if it doesn't exist
    if (!document.querySelector('.toast-container')) {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }
}

// Show a toast notification
function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container');
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Set icon based on type
    let icon = '?';
    if (type === 'success') icon = '✓';
    else if (type === 'error') icon = '!';
    else if (type === 'info') icon = 'i';
    
    // Create toast content
    toast.innerHTML = `
        <div class="toast-icon">${icon}</div>
        <div class="toast-message">${message}</div>
        <button class="toast-close">×</button>
    `;
    
    // Add toast to container
    toastContainer.appendChild(toast);
    
    // Show toast after a short delay (for animation)
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    // Set automatic dismiss timer
    const dismissTime = 5000; // 5 seconds
    const dismissTimer = setTimeout(() => {
        dismissToast(toast);
    }, dismissTime);
    
    // Add click event to close button
    toast.querySelector('.toast-close').addEventListener('click', () => {
        clearTimeout(dismissTimer);
        dismissToast(toast);
    });
}

// Dismiss a toast notification
function dismissToast(toast) {
    toast.classList.add('hide');
    toast.classList.remove('show');
    
    // Remove from DOM after animation
    setTimeout(() => {
        if (toast.parentElement) {
            toast.parentElement.removeChild(toast);
        }
    }, 300);
}

// Initialize movie card animations and effects
function initMovieCards() {
    const movieCards = document.querySelectorAll('.movie-card');
    
    movieCards.forEach(card => {
        // Create 3D tilt effect on movie cards
        card.addEventListener('mousemove', (e) => {
            const cardRect = card.getBoundingClientRect();
            const cardCenterX = cardRect.left + cardRect.width / 2;
            const cardCenterY = cardRect.top + cardRect.height / 2;
            const mouseX = e.clientX - cardCenterX;
            const mouseY = e.clientY - cardCenterY;
            
            // Calculate rotation values based on mouse position
            const rotateY = mouseX / 20;
            const rotateX = -mouseY / 20;
            
            // Apply the transform
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
        });
        
        // Reset card position when mouse leaves
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            setTimeout(() => {
                card.style.transition = 'transform 0.5s ease';
            }, 100);
        });
        
        // Remove transition on mouse enter for smooth movement
        card.addEventListener('mouseenter', () => {
            card.style.transition = 'none';
        });
    });
}

// Add cinematic splash effect on page load
function addCinematicSplash() {
    // Only add splash effect to main pages (not login/register)
    if (document.querySelector('header')) {
        const splash = document.createElement('div');
        splash.className = 'cinematic-splash';
        splash.innerHTML = `
            <div class="cinematic-splash-inner"></div>
        `;
        document.body.appendChild(splash);
        
        // Add CSS for the splash
        const style = document.createElement('style');
        style.textContent = `
            .cinematic-splash {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: black;
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
                animation: splash-fade 1.5s ease-out forwards;
                pointer-events: none;
            }
            
            .cinematic-splash-inner {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background-color: #e50914;
                animation: splash-pulse 1.2s ease-in-out;
            }
            
            @keyframes splash-fade {
                0% { opacity: 1; }
                70% { opacity: 1; }
                100% { opacity: 0; }
            }
            
            @keyframes splash-pulse {
                0% { transform: scale(0); opacity: 1; }
                50% { transform: scale(1); opacity: 1; }
                100% { transform: scale(50); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
        
        // Remove splash after animation
        setTimeout(() => {
            document.body.removeChild(splash);
            document.head.removeChild(style);
        }, 1600);
    }
} 