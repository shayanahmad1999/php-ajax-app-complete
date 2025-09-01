// Global variables
let users = [];
let editingUserId = null;

// DOM elements
const userForm = document.getElementById('userForm');
const usersList = document.getElementById('usersList');
const searchInput = document.getElementById('searchInput');
const notification = document.getElementById('notification');

// API base URL
const API_URL = 'public/api.php';

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Form submission
    userForm.addEventListener('submit', handleFormSubmit);
    
    // Search functionality
    searchInput.addEventListener('input', debounce(handleSearch, 300));
    
    // Clear search on escape
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            loadUsers();
        }
    });
}

// Handle form submission
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(userForm);
    const userData = {
        name: formData.get('name').trim(),
        email: formData.get('email').trim(),
        phone: formData.get('phone').trim()
    };
    
    // Validation
    if (!userData.name || !userData.email) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    if (!isValidEmail(userData.email)) {
        showNotification('Please enter a valid email address', 'error');
        return;
    }
    
    try {
        if (editingUserId) {
            await updateUser(editingUserId, userData);
        } else {
            await createUser(userData);
        }
    } catch (error) {
        console.error('Form submission error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    }
}

// Create new user
async function createUser(userData) {
  try {
    showLoading(true);

    const response = await fetch(`${API_URL}?action=create`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(userData)
    });

    const contentType = response.headers.get('content-type') || '';
    const raw = await response.text(); // read once

    let result;
    if (contentType.includes('application/json')) {
      try {
        result = JSON.parse(raw);
      } catch {
        throw new Error('Server returned invalid JSON:\n' + raw.slice(0, 300));
      }
    } else {
      // Not JSON — likely an HTML error page or PHP warning
      throw new Error('Unexpected response (not JSON):\n' + raw.slice(0, 300));
    }

    if (response.ok) {
      showNotification(result.message || 'User created successfully!', 'success');
      userForm.reset();
      loadUsers();
    } else {
      showNotification(result.message || `Failed to create user (${response.status})`, 'error');
    }
  } catch (err) {
    console.error('Create user error:', err);
    showNotification(err.message || 'Network error. Please check your connection.', 'error');
  } finally {
    showLoading(false);
  }
}

// Update existing user
async function updateUser(userId, userData) {
    try {
        showLoading(true);
        
        const response = await fetch(`${API_URL}?action=update`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: userId,
                ...userData
            })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showNotification('User updated successfully!', 'success');
            cancelEdit();
            loadUsers();
        } else {
            showNotification(result.message || 'Failed to update user', 'error');
        }
    } catch (error) {
        console.error('Update user error:', error);
        showNotification('Network error. Please check your connection.', 'error');
    } finally {
        showLoading(false);
    }
}

// Load all users
async function loadUsers() {
    try {
        showLoading(true);
        
        const response = await fetch(`${API_URL}`);
        const result = await response.json();
        
        if (response.ok && result.records) {
            users = result.records;
            renderUsers(users);
        } else {
            users = [];
            renderEmptyState();
        }
    } catch (error) {
        console.error('Load users error:', error);
        showNotification('Failed to load users', 'error');
        renderEmptyState();
    } finally {
        showLoading(false);
    }
}

// Search users
async function handleSearch(e) {
    const searchTerm = e.target.value.trim();
    
    if (searchTerm === '') {
        loadUsers();
        return;
    }
    
    try {
        showLoading(true);
        
        const response = await fetch(`${API_URL}?action=search&q=${encodeURIComponent(searchTerm)}`);
        const result = await response.json();
        
        if (response.ok && result.records) {
            renderUsers(result.records);
        } else {
            renderEmptyState('No users found matching your search.');
        }
    } catch (error) {
        console.error('Search error:', error);
        showNotification('Search failed', 'error');
    } finally {
        showLoading(false);
    }
}

// Delete user
async function deleteUser(userId, userName) {
    if (!confirm(`Are you sure you want to delete ${userName}?`)) {
        return;
    }
    
    try {
        showLoading(true);
        
        const response = await fetch(`${API_URL}?action=delete&id=${userId}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showNotification('User deleted successfully!', 'success');
            loadUsers();
        } else {
            showNotification(result.message || 'Failed to delete user', 'error');
        }
    } catch (error) {
        console.error('Delete user error:', error);
        showNotification('Network error. Please check your connection.', 'error');
    } finally {
        showLoading(false);
    }
}

// Edit user
function editUser(userId) {
    const user = users.find(u => u.id == userId);
    if (!user) return;
    
    // Populate form
    document.getElementById('name').value = user.name;
    document.getElementById('email').value = user.email;
    document.getElementById('phone').value = user.phone || '';
    
    // Update form state
    editingUserId = userId;
    const submitBtn = userForm.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-save"></i> Update User';
    submitBtn.classList.remove('btn-primary');
    submitBtn.classList.add('btn-secondary');
    
    // Add cancel button
    if (!document.getElementById('cancelBtn')) {
        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.id = 'cancelBtn';
        cancelBtn.className = 'btn btn-secondary';
        cancelBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
        cancelBtn.onclick = cancelEdit;
        submitBtn.parentNode.appendChild(cancelBtn);
    }
    
    // Scroll to form
    document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
}

// Cancel edit
function cancelEdit() {
    editingUserId = null;
    userForm.reset();
    
    const submitBtn = userForm.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-plus"></i> Add User';
    submitBtn.classList.remove('btn-secondary');
    submitBtn.classList.add('btn-primary');
    
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.remove();
    }
}

// Render users list
function renderUsers(usersData) {
    if (!usersData || usersData.length === 0) {
        renderEmptyState();
        return;
    }
    
    const usersHTML = usersData.map(user => `
        <div class="user-card" data-user-id="${user.id}">
            <div class="user-info">
                <div class="user-details">
                    <h3>${escapeHtml(user.name)}</h3>
                    <p><i class="fas fa-envelope"></i> ${escapeHtml(user.email)}</p>
                    ${user.phone ? `<p><i class="fas fa-phone"></i> ${escapeHtml(user.phone)}</p>` : ''}
                    <p><i class="fas fa-calendar"></i> ${formatDate(user.created_at)}</p>
                </div>
                <div class="user-actions">
                    <button class="btn btn-secondary btn-small" onclick="editUser(${user.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-small" onclick="deleteUser(${user.id}, '${escapeHtml(user.name)}')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    usersList.innerHTML = usersHTML;
}

// Render empty state
function renderEmptyState(message = 'No users found. Add your first user!') {
    usersList.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <p>${message}</p>
        </div>
    `;
}

// Show notification
function showNotification(message, type = 'info') {
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 4000);
}

// Show/hide loading state
function showLoading(show) {
    const submitBtn = userForm.querySelector('button[type="submit"]');
    
    if (show) {
        submitBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        submitBtn.dataset.originalText = originalText;
        submitBtn.innerHTML = '<span class="loading"></span> Loading...';
    } else {
        submitBtn.disabled = false;
        if (submitBtn.dataset.originalText) {
            submitBtn.innerHTML = submitBtn.dataset.originalText;
        }
    }
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Handle network errors globally
window.addEventListener('online', function() {
    showNotification('Connection restored', 'success');
});

window.addEventListener('offline', function() {
    showNotification('Connection lost. Please check your internet.', 'error');
});

