// API Configuration
const API_BASE_URL = '../controllers/UserController.php';
const USERS_ENDPOINT = `${API_BASE_URL}/users`;

// State management
let currentPage = 1;
let totalPages = 1;
let currentFilters = {
    search: '',
    role: 'all'
};
let userToDelete = null;

// DOM Elements
const usersTableBody = document.getElementById('usersTableBody');
const userCountSpan = document.getElementById('userCount');
const paginationDiv = document.getElementById('pagination');
const userModal = document.getElementById('userModal');
const confirmationModal = document.getElementById('confirmationModal');
const userForm = document.getElementById('userForm');
const modalTitle = document.getElementById('modalTitle');
const submitBtn = document.getElementById('submitBtn');
const searchInput = document.getElementById('search');
const roleFilter = document.getElementById('roleFilter');
const applyFiltersBtn = document.getElementById('applyFilters');

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Modal functionality
    document.getElementById('addUserBtn').addEventListener('click', () => openUserModal());
    document.querySelector('.close-btn').addEventListener('click', () => closeUserModal());
    document.getElementById('cancelBtn').addEventListener('click', () => closeUserModal());
    
    // Form submission
    userForm.addEventListener('submit', handleUserFormSubmit);
    
    // Filter functionality
    applyFiltersBtn.addEventListener('click', applyFilters);
    
    // Search on Enter key
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });
    
    // Confirmation modal
    document.getElementById('confirmCancel').addEventListener('click', () => closeConfirmationModal());
    document.getElementById('confirmAction').addEventListener('click', handleConfirmedAction);
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === userModal) closeUserModal();
        if (e.target === confirmationModal) closeConfirmationModal();
    });
}

// Load users from API
function loadUsers(page = 1) {
    showLoadingState(true);
    
    const params = new URLSearchParams({
        page: page,
        limit: 10,
        search: currentFilters.search,
        role: currentFilters.role
    });
    
    fetch(`${USERS_ENDPOINT}?${params}`)
        .then(response => {
            const contentType = response.headers.get('content-type');
            
            // Check if response is JSON
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Non-JSON response received:', text);
                    throw new Error('Server returned an error. Please check if the database is configured correctly.');
                });
            }
            
            return response.json().then(data => {
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to fetch users');
                }
                return data;
            });
        })
        .then(data => {
            if (!data.users || !data.pagination) {
                throw new Error('Invalid response format from server');
            }
            displayUsers(data.users);
            updatePagination(data.pagination);
            currentPage = data.pagination.current_page;
            totalPages = data.pagination.total_pages;
            updateUserCount(data.pagination.total);
        })
        .catch(error => {
            console.error('Error loading users:', error);
            showNotification(error.message || 'Failed to load users', 'error');
            usersTableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: red;">
                ${escapeHtml(error.message)}<br>
                <small>Check browser console for details</small>
            </td></tr>`;
        })
        .finally(() => {
            showLoadingState(false);
        });
}

// Display users in the table
function displayUsers(users) {
    if (users.length === 0) {
        usersTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No users found</td></tr>';
        return;
    }
    
    usersTableBody.innerHTML = users.map(user => `
        <tr>
            <td>#${user.id}</td>
            <td>${escapeHtml(user.username)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td><span class="role-badge role-${user.role}">${capitalizeFirst(user.role)}</span></td>
            <td>${formatDate(user.created_at)}</td>
            <td class="action-buttons">
                <button class="btn btn-success btn-sm edit-btn" data-id="${user.id}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="${user.id}">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');
    
    // Add event listeners to action buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const userId = e.target.closest('.edit-btn').dataset.id;
            editUser(userId);
        });
    });
    
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const userId = e.target.closest('.delete-btn').dataset.id;
            confirmDeleteUser(userId);
        });
    });
}

// Update pagination controls
function updatePagination(pagination) {
    const { current_page, total_pages } = pagination;
    
    if (total_pages <= 1) {
        paginationDiv.innerHTML = '';
        return;
    }
    
    let paginationHTML = '';
    
    // Previous button
    if (current_page > 1) {
        paginationHTML += `<button onclick="loadUsers(${current_page - 1})">&laquo;</button>`;
    } else {
        paginationHTML += `<button disabled>&laquo;</button>`;
    }
    
    // Page numbers with smart display
    const maxButtons = 5;
    let startPage = Math.max(1, current_page - Math.floor(maxButtons / 2));
    let endPage = Math.min(total_pages, startPage + maxButtons - 1);
    
    if (endPage - startPage < maxButtons - 1) {
        startPage = Math.max(1, endPage - maxButtons + 1);
    }
    
    // First page
    if (startPage > 1) {
        paginationHTML += `<button onclick="loadUsers(1)">1</button>`;
        if (startPage > 2) {
            paginationHTML += `<button disabled>...</button>`;
        }
    }
    
    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
        if (i === current_page) {
            paginationHTML += `<button class="active">${i}</button>`;
        } else {
            paginationHTML += `<button onclick="loadUsers(${i})">${i}</button>`;
        }
    }
    
    // Last page
    if (endPage < total_pages) {
        if (endPage < total_pages - 1) {
            paginationHTML += `<button disabled>...</button>`;
        }
        paginationHTML += `<button onclick="loadUsers(${total_pages})">${total_pages}</button>`;
    }
    
    // Next button
    if (current_page < total_pages) {
        paginationHTML += `<button onclick="loadUsers(${current_page + 1})">&raquo;</button>`;
    } else {
        paginationHTML += `<button disabled>&raquo;</button>`;
    }
    
    paginationDiv.innerHTML = paginationHTML;
}

// Update user count display
function updateUserCount(count) {
    userCountSpan.textContent = `Total users: ${count}`;
}

// Open user modal for adding or editing
function openUserModal(user = null) {
    if (user) {
        // Editing existing user
        modalTitle.textContent = 'Edit User';
        submitBtn.textContent = 'Update User';
        document.getElementById('userId').value = user.id;
        document.getElementById('username').value = user.username;
        document.getElementById('email').value = user.email;
        document.getElementById('role').value = user.role;
        document.getElementById('password').required = false;
        
        // Add placeholder for password
        document.getElementById('password').placeholder = 'Leave blank to keep current password';
    } else {
        // Adding new user
        modalTitle.textContent = 'Add New User';
        submitBtn.textContent = 'Add User';
        userForm.reset();
        document.getElementById('userId').value = '';
        document.getElementById('password').required = true;
        document.getElementById('password').placeholder = 'Enter password';
    }
    
    userModal.style.display = 'flex';
}

// Close user modal
function closeUserModal() {
    userModal.style.display = 'none';
    userForm.reset();
}

// Handle user form submission (add/edit)
function handleUserFormSubmit(e) {
    e.preventDefault();
    
    const userId = document.getElementById('userId').value;
    const userData = {
        username: document.getElementById('username').value.trim(),
        email: document.getElementById('email').value.trim(),
        role: document.getElementById('role').value
    };
    
    // Include password only if provided
    const password = document.getElementById('password').value;
    if (password && password.trim() !== '') {
        userData.password = password;
    }
    
    // Disable submit button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.textContent = userId ? 'Updating...' : 'Adding...';
    
    if (userId) {
        // Update existing user
        updateUser(userId, userData);
    } else {
        // Add new user
        if (!userData.password) {
            showNotification('Password is required for new users', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Add User';
            return;
        }
        addUser(userData);
    }
}

// Add new user via API
function addUser(userData) {
    fetch(USERS_ENDPOINT, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status >= 200 && status < 300) {
            showNotification('User added successfully', 'success');
            closeUserModal();
            loadUsers(currentPage);
        } else {
            throw new Error(body.message || 'Failed to add user');
        }
    })
    .catch(error => {
        console.error('Error adding user:', error);
        showNotification(error.message || 'Failed to add user', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Add User';
    });
}

// Edit user - fetch user data and open modal
function editUser(userId) {
    fetch(`${USERS_ENDPOINT}/${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch user data');
            }
            return response.json();
        })
        .then(user => {
            openUserModal(user);
        })
        .catch(error => {
            console.error('Error fetching user:', error);
            showNotification('Failed to fetch user data', 'error');
        });
}

// Update user via API
function updateUser(userId, userData) {
    fetch(`${USERS_ENDPOINT}/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status >= 200 && status < 300) {
            showNotification('User updated successfully', 'success');
            closeUserModal();
            loadUsers(currentPage);
        } else {
            throw new Error(body.message || 'Failed to update user');
        }
    })
    .catch(error => {
        console.error('Error updating user:', error);
        showNotification(error.message || 'Failed to update user', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Update User';
    });
}

// Confirm user deletion
function confirmDeleteUser(userId) {
    userToDelete = userId;
    document.getElementById('confirmationTitle').textContent = 'Confirm Deletion';
    document.getElementById('confirmationMessage').textContent = 'Are you sure you want to delete this user? This action cannot be undone.';
    confirmationModal.style.display = 'flex';
}

// Handle confirmed actions (currently only deletion)
function handleConfirmedAction() {
    if (userToDelete) {
        deleteUser(userToDelete);
    }
    closeConfirmationModal();
}

// Close confirmation modal
function closeConfirmationModal() {
    confirmationModal.style.display = 'none';
    userToDelete = null;
}

// Delete user via API
function deleteUser(userId) {
    fetch(`${USERS_ENDPOINT}/${userId}`, {
        method: 'DELETE'
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status >= 200 && status < 300) {
            showNotification('User deleted successfully', 'success');
            // If current page becomes empty, go to previous page
            if (currentPage > 1 && usersTableBody.querySelectorAll('tr').length === 1) {
                loadUsers(currentPage - 1);
            } else {
                loadUsers(currentPage);
            }
        } else {
            throw new Error(body.message || 'Failed to delete user');
        }
    })
    .catch(error => {
        console.error('Error deleting user:', error);
        showNotification(error.message || 'Failed to delete user', 'error');
    });
}

// Apply filters
function applyFilters() {
    currentFilters = {
        search: searchInput.value.trim(),
        role: roleFilter.value
    };
    loadUsers(1); // Reset to first page when applying filters
}

// Show loading state
function showLoadingState(loading) {
    const tableContainer = document.querySelector('.table-container');
    if (loading) {
        tableContainer.classList.add('loading');
        usersTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
    } else {
        tableContainer.classList.remove('loading');
    }
}

// Show notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${escapeHtml(message)}</span>
    `;
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Capitalize first letter
function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}