/**
 * Mixtape Frontend Script
 * Handles theme toggling, form switching, and mixtape creation
 */

// Messages for radio display
const messages = [
    "CREATE YOUR MIXTAPE",
    "CUSTOMIZE YOUR SOUND",
    "YOUR PERSONAL PLAYLIST"
];

// Variables to track state
let currentMessage = 0;
let isProcessing = false;
let originalMessage = "";
let messageInterval;

// Initialize theme from localStorage or default to light
function initTheme() {
    const savedTheme = localStorage.getItem('darkMode') === 'true';
    const isDarkMode = savedTheme || false;
    
    if (isDarkMode) {
        document.body.classList.add('dark-mode');
    }
    
    const themeBtn = document.querySelector('.theme-btn');
    if (themeBtn) {
        themeBtn.textContent = isDarkMode ? '☀️ Light Mode' : '🌙 Dark Mode';
    }
}

// Create and set up the theme toggle button
function setupThemeToggle() {
    const themeBtn = document.createElement('button');
    themeBtn.className = 'theme-btn';
    themeBtn.textContent = '🌙 Dark Mode';
    
    themeBtn.addEventListener('click', () => {
        const isDarkMode = document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', isDarkMode.toString());
        themeBtn.textContent = isDarkMode ? '☀️ Light Mode' : '🌙 Dark Mode';
    });
    
    document.body.appendChild(themeBtn);
}

// Start cycling radio messages
function startMessageCycle() {
    messageInterval = setInterval(() => {
        if (!isProcessing) {
            currentMessage = (currentMessage + 1) % messages.length;
            const radioMessage = document.getElementById('radio-message');
            if (radioMessage) {
                radioMessage.textContent = messages[currentMessage];
            }
        }
    }, 3000);
}

// Switch between login and mixtape views
function switchToMixtapeView() {
    document.getElementById('login-view').classList.add('hidden');
    document.getElementById('mixtape-view').classList.remove('hidden');
    clearInterval(messageInterval);
}


// Switch back to login view
function switchToLoginView() {
    document.getElementById('mixtape-view').classList.add('hidden');
    document.getElementById('login-view').classList.remove('hidden');
    
    // Reset login form
    document.getElementById('login-form').reset();
    
    // Show login container, hide signup container
    document.getElementById('login-container').classList.remove('hidden');
    document.getElementById('signup-container').classList.add('hidden');
    
    // Restart message cycling
    startMessageCycle();
    
    // Show welcome back message
    isProcessing = true;
    document.getElementById('radio-message').textContent = "WELCOME BACK";
    setTimeout(() => { isProcessing = false; }, 2000);
}

// Update track numbers when tracks are added/removed
function updateTrackNumbers() {
    const tracksContainer = document.getElementById('tracks-container');
    if (!tracksContainer) return;
    
    const trackItems = tracksContainer.querySelectorAll('.track-item');
    trackItems.forEach((item, index) => {
        item.querySelector('.track-number').textContent = index + 1;
    });
}

// Initialize the application when DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    // Set up theme
    initTheme();
    setupThemeToggle();
    
    // Start radio message cycle
    startMessageCycle();
    // Set up logout button functionality
const logoutBtn = document.getElementById('logout-btn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        switchToLoginView();
    });
}

    // Form toggling functionality - Switch to signup form
    document.getElementById('show-signup').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('login-container').classList.add('hidden');
        document.getElementById('signup-container').classList.remove('hidden');
        
        // Pause message cycling and show specific message
        isProcessing = true;
        originalMessage = document.getElementById('radio-message').textContent;
        document.getElementById('radio-message').textContent = "CREATE NEW ACCOUNT";
        
        // Resume message cycling after delay
        setTimeout(() => { isProcessing = false; }, 2000);
    });
    
    // Form toggling functionality - Switch to login form
    document.getElementById('show-login').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('signup-container').classList.add('hidden');
        document.getElementById('login-container').classList.remove('hidden');
        
        // Pause message cycling and show specific message
        isProcessing = true;
        originalMessage = document.getElementById('radio-message').textContent;
        document.getElementById('radio-message').textContent = "WELCOME BACK";
        
        // Resume message cycling after delay
        setTimeout(() => { isProcessing = false; }, 2000);
    });

    // Handle login form submission
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        isProcessing = true;
        document.getElementById('radio-message').textContent = "LOGGING IN...";
    
        const formData = new FormData(document.getElementById('login-form'));
    
        fetch('../backend/auth/login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('radio-message').textContent = "Login successful!";
                switchToMixtapeView();
            } else {
                document.getElementById('radio-message').textContent = data.message || "Login failed.";
            }
        })
        .catch(error => {
            console.error("Login error:", error);
            document.getElementById('radio-message').textContent = "Server error.";
        })
        .finally(() => {
            isProcessing = false;
        });
    });
    
    // Handle signup form submission
    document.getElementById('signup-form').addEventListener('submit', function(e) {
        e.preventDefault();
        isProcessing = true;
        document.getElementById('radio-message').textContent = "CREATING ACCOUNT...";
        
        const formData = new FormData(document.getElementById('signup-form'));

        fetch('../backend/auth/register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // use .text() to debug first
        .then(text => {
            console.log('Raw response:', text); // Log raw response for debugging
            try {
                const data = JSON.parse(text);
                if (data.status === 'success') {
                    document.getElementById('radio-message').textContent = "Registered successfully!";
                    switchToMixtapeView();
                } else {
                    document.getElementById('radio-message').textContent = data.message || "Registration failed.";
                }
            } catch (e) {
                console.error("JSON parse error:", e);
                console.error("Raw response:", text);
                document.getElementById('radio-message').textContent = "Server error.";
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            document.getElementById('radio-message').textContent = "Network error.";
        })
        .finally(() => {
            isProcessing = false;
        });        
    });

    // Get references to mixtape view elements
    const mixtapeTitle = document.getElementById('mixtape-title');
    const tapeTitle = document.getElementById('tape-title');
    const addTrackBtn = document.getElementById('add-track');
    const tracksContainer = document.getElementById('tracks-container');
    const createMixtapeBtn = document.getElementById('create-mixtape');
    
    // Update title when user types in title field
    if (mixtapeTitle && tapeTitle) {
        mixtapeTitle.addEventListener('input', function() {
            tapeTitle.textContent = this.value || "Your Mixtape Title";
        });
    }
    
    // Add new track when add button is clicked
    if (addTrackBtn && tracksContainer) {
        addTrackBtn.addEventListener('click', () => {
            // Check if maximum tracks limit is reached
            const currentTracks = tracksContainer.querySelectorAll('.track-item').length;
            if (currentTracks >= 10) {
                alert("Maximum 10 tracks allowed!");
                return;
            }
            
            // Create and append new track item
            const trackItem = document.createElement('div');
            trackItem.className = 'track-item';
            trackItem.innerHTML = `
                <div class="track-number">${currentTracks + 1}</div>
                <div class="track-input-container">
                    <input type="text" class="youtube-link" placeholder="Paste YouTube link here...">
                </div>
                <button class="remove-track" title="Remove this track">✕</button>
            `;
            tracksContainer.appendChild(trackItem);
            updateTrackNumbers();
        });
    }
    
    // Handle track removal using event delegation
    if (tracksContainer) {
        tracksContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-track')) {
                // If it's the last track, just clear the input instead of removing
                if (tracksContainer.querySelectorAll('.track-item').length <= 1) {
                    const input = e.target.closest('.track-item').querySelector('.youtube-link');
                    if (input) input.value = '';
                    return;
                }
                // Otherwise remove the track item completely
                e.target.closest('.track-item').remove();
                updateTrackNumbers();
            }
        });
    }
    
    // Handle mixtape creation when button is clicked
    if (createMixtapeBtn && tracksContainer) {
        createMixtapeBtn.addEventListener('click', () => {
            if (!mixtapeTitle) return;
            
            // Validate mixtape title
            const title = mixtapeTitle.value.trim();
            if (!title) {
                alert("Please provide a title for your mixtape!");
                return;
            }
            
            // Collect and validate tracks
            const trackInputs = tracksContainer.querySelectorAll('.youtube-link');
            const tracks = [];
            let hasEmptyTracks = false;
            
            // Process each track input
            trackInputs.forEach(input => {
                const value = input.value.trim();
                if (value) tracks.push(value);
                else hasEmptyTracks = true;
            });
            
            // Ensure at least one track is added
            if (tracks.length === 0) {
                alert("Please add at least one track to your mixtape!");
                return;
            }
            
            
           
        });
    }
});
