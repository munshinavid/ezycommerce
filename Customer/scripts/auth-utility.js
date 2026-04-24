// this is Customer/scripts/auth-utils.js
class AuthManager {
    constructor() {
        this.apiBaseUrl = '../controllers/AuthController.php';
        this.userData = this.getUserData();
    }

    // Get user data from localStorage
    getUserData() {
        const userData = localStorage.getItem('userData');
        return userData ? JSON.parse(userData) : null;
    }

    // Check if user is authenticated
    isAuthenticated() {
        return !!this.userData;
    }

    // Get current user info
    getCurrentUser() {
        return this.userData;
    }

    async verifySession() {
        try {
            const response = await fetch(`${this.apiBaseUrl}?endpoint=verify`, {
                method: 'GET',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Session verification failed');
            }

            const data = await response.json();
            if (data.valid && data.user) {
                this.userData = data.user;
                localStorage.setItem('userData', JSON.stringify(data.user));
                return data.user;
            } else {
                throw new Error('Invalid session');
            }
        } catch (error) {
            console.error('Session verification error:', error);
            this.clearAuth();
            throw error;
        }
    }

    // Logout user
    async logout() {
        try {
            await fetch(`${this.apiBaseUrl}?endpoint=logout`, {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' }
            });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.clearAuth();
            window.location.href = 'login.php';
        }
    }

    // Login
    async login(email, password) {
        const response = await fetch(`${this.apiBaseUrl}?endpoint=login`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await response.json();
        if (response.ok && data.user) {
            this.userData = data.user;
            localStorage.setItem('userData', JSON.stringify(data.user));
        }
        return data;
    }

    // Register
    async register(data) {
        const response = await fetch(`${this.apiBaseUrl}?endpoint=register`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return await response.json();
    }

    // Clear auth info
    clearAuth() {
        this.userData = null;
        localStorage.removeItem('userData');
    }

    // Redirect to login if not authenticated
    requireAuth(redirectUrl = null) {
        if (!this.isAuthenticated()) {
            const loginUrl = redirectUrl ?
                `login.php?redirect=${encodeURIComponent(redirectUrl)}` :
                'login.php';
            window.location.href = loginUrl;
            return false;
        }
        return true;
    }

    // Check role
    hasRole(role) {
        return this.userData && this.userData.role === role;
    }

    // Authenticated API request helper
    async apiRequest(url, options = {}) {
        const config = {
            ...options,
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        };

        const response = await fetch(url, config);

        if (response.status === 401) {
            this.clearAuth();
            window.location.href = 'login.php';
            throw new Error('Session expired');
        }

        return response;
    }
}

// Global instance
const authManager = new AuthManager();

// Utility functions
function isLoggedIn() { return authManager.isAuthenticated(); }
function getCurrentUser() { return authManager.getCurrentUser(); }
function requireLogin(redirectUrl = null) { return authManager.requireAuth(redirectUrl); }
function logout() { return authManager.logout(); }
function hasRole(role) { return authManager.hasRole(role); }

// DOM helpers
function initializeAuth() {
    const isLoginPage = window.location.pathname.includes('login.php');

    if (isLoginPage) {
        authManager.verifySession().then(() => {
            window.location.href = 'index.php';
        }).catch(() => {});
        return true;
    }

    authManager.verifySession().then(() => {
        updateUserInterface();
    }).catch(() => {
        const currentUrl = window.location.href;
        window.location.href = `login.php?redirect=${encodeURIComponent(currentUrl)}`;
    });

    return true;
}

function updateUserInterface() {
    const user = getCurrentUser();
    if (!user) return;

    document.querySelectorAll('.user-name').forEach(el => el.textContent = user.firstName + ' ' + (user.lastName || '') || user.username);
    document.querySelectorAll('.user-avatar').forEach(el => { if (user.avatar) el.src = user.avatar; });
    document.querySelectorAll('.user-email').forEach(el => el.textContent = user.email);

    if (hasRole('Admin')) document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'block');
    if (hasRole('Customer')) document.querySelectorAll('.customer-only').forEach(el => el.style.display = 'block');
}

// Logout buttons
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.logout-btn, [data-action="logout"]').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) logout();
        });
    });

    if (isLoggedIn()) updateUserInterface();
});

document.addEventListener('DOMContentLoaded', initializeAuth);

// Redirect after login
function handleLoginRedirect() {
    const redirectUrl = new URLSearchParams(window.location.search).get('redirect');
    if (redirectUrl) window.location.href = decodeURIComponent(redirectUrl);
    else window.location.href = 'index.php';
}

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { authManager, isLoggedIn, getCurrentUser, requireLogin, logout, hasRole, initializeAuth, updateUserInterface, handleLoginRedirect };
}
