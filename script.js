/**
 * Mixtape Frontend Script
 * Handles form toggling and radio display messages
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

// Start cycling messages
function startMessageCycle() {
    messageInterval = setInterval(() => {
        if (!isProcessing) {
            currentMessage = (currentMessage + 1) % messages.length;
            document.getElementById('radio-message').textContent = messages[currentMessage];
        }
    }, 3000);
}


document.addEventListener('DOMContentLoaded', () => {
    startMessageCycle();

    // Toggle between login and signup forms
    document.getElementById('show-signup').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('login-container').classList.add('hidden');
        document.getElementById('signup-container').classList.remove('hidden');
        
        // Save current state to temporarily pause cycling
        isProcessing = true;
        originalMessage = document.getElementById('radio-message').textContent;
        document.getElementById('radio-message').textContent = "CREATE NEW ACCOUNT";
        
        // Resume cycling after a delay
        setTimeout(() => {
            isProcessing = false;
        }, 2000);
    });
    
    document.getElementById('show-login').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('signup-container').classList.add('hidden');
        document.getElementById('login-container').classList.remove('hidden');
        
        // Save current state to temporarily pause cycling
        isProcessing = true;
        originalMessage = document.getElementById('radio-message').textContent;
        document.getElementById('radio-message').textContent = "WELCOME BACK";
        
        // Resume cycling after a delay
        setTimeout(() => {
            isProcessing = false;
        }, 2000);
    });

    // Form submission handlers 
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        
        // Show message on radio display
        isProcessing = true;
        document.getElementById('radio-message').textContent = "LOGGING IN...";
        
        
    });
    
    document.getElementById('signup-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('signup-name').value;
        const email = document.getElementById('signup-email').value;
        const password = document.getElementById('signup-password').value;
        
        // Show message on radio display
        isProcessing = true;
        document.getElementById('radio-message').textContent = "CREATING ACCOUNT...";
        
        
    });
});
