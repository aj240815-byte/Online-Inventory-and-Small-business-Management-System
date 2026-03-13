// Authentication utilities
class AuthManager {
    constructor() {
        this.tokenKey = 'jims_token';
        this.userKey = 'jims_user';
    }
    
    // Check if user is authenticated
    isAuthenticated() {
        const token = localStorage.getItem(this.tokenKey);
        const user = localStorage.getItem(this.userKey);
        return !!(token && user);
    }
    
    // Get current user
    getCurrentUser() {
        const userData = localStorage.getItem(this.userKey);
        return userData ? JSON.parse(userData) : null;
    }
    
    // Get token
    getToken() {
        return localStorage.getItem(this.tokenKey);
    }
    
    // Logout user
    logout() {
        localStorage.removeItem(this.tokenKey);
        localStorage.removeItem(this.userKey);
        window.location.href = '../login.html';
    }
    
    // Verify token with server
    async verifyToken() {
        const token = this.getToken();
        if (!token) return false;
        
        try {
            const response = await fetch('../api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'verify',
                    token: token
                })
            });
            
            const data = await response.json();
            return data.success;
        } catch (error) {
            console.error('Token verification failed:', error);
            return false;
        }
    }
    
    // Redirect to login if not authenticated
    requireAuth() {
        if (!this.isAuthenticated()) {
            window.location.href = '../login.html';
            return false;
        }
        return true;
    }
    
    // Update UI with user info
    updateUserInterface() {
        const user = this.getCurrentUser();
        if (user) {
            // Update user display name
            const userElements = document.querySelectorAll('.nav-user, .user-name');
            userElements.forEach(element => {
                element.textContent = user.full_name || user.username;
            });
            
            // Update user role if element exists
            const roleElements = document.querySelectorAll('.user-role');
            roleElements.forEach(element => {
                element.textContent = user.role || 'User';
            });
        }
        
        // Add logout functionality
        const logoutButtons = document.querySelectorAll('.logout-btn, [data-action="logout"]');
        logoutButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.logout();
            });
        });
    }
}

// Initialize auth manager
const auth = new AuthManager();

// Auto-redirect to login if not authenticated
document.addEventListener('DOMContentLoaded', async function() {
    // Check if current page requires authentication (all pages in /pages/ directory)
    const currentPath = window.location.pathname;
    const requiresAuth = currentPath.includes('/pages/');
    
    if (requiresAuth) {
        // First check local storage
        if (!auth.isAuthenticated()) {
            window.location.href = '../login.html';
            return;
        }
        
        // Then verify with server
        const isValid = await auth.verifyToken();
        if (!isValid) {
            auth.logout();
            return;
        }
        
        // Update UI with user info
        auth.updateUserInterface();
    }
});

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AuthManager;
}
