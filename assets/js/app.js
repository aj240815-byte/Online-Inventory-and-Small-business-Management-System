/**
 * JIMS - Jewellery Inventory Management System
 * Main Application JavaScript - Modular Architecture
 */

// Global Variables
let currentUser = null;
let currentPage = 'dashboard';
let apiBase = './api';

// DOM Elements
const sidebarLinks = document.querySelectorAll('.sidebar-link');
const contentArea = document.getElementById('content');
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const sidebar = document.querySelector('.sidebar');

// Initialize Application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    setupEventListeners();
    // Removed automatic loadDashboard() to prevent infinite loading
});

function initializeApp() {
    // Check if user is logged in
    const userData = localStorage.getItem('jims_user');
    if (userData) {
        currentUser = JSON.parse(userData);
        updateUserInterface();
    } else {
        // Redirect to login if needed
        console.log('User not logged in');
    }
    
    // Set up API base URL
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        apiBase = './api';
    }
}

function setupEventListeners() {
    // Remove sidebar link click interception to allow normal navigation
    // sidebarLinks.forEach(link => {
    //     link.addEventListener('click', function(e) {
    //         e.preventDefault();
    //         const page = this.getAttribute('data-page');
    //         navigateToPage(page);
    //     });
    // });
    
    // Mobile menu toggle
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Close modals on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
    
    // Close modals on outside click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeAllModals();
        }
        
        // Close mobile sidebar when clicking outside
        if (window.innerWidth <= 768 && sidebar && !sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });
}

// Utility Functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function getStatusClass(status) {
    switch(status.toLowerCase()) {
        case 'paid': return 'success';
        case 'pending': return 'warning';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getStockStatusClass(stock, reorderLevel) {
    if (stock === 0) return 'danger';
    if (stock <= reorderLevel) return 'warning';
    return 'success';
}

function renderPagination(pagination) {
    if (!pagination || pagination.pages <= 1) return '';
    
    let html = '<div class="pagination">';
    
    // Previous button
    if (pagination.page > 1) {
        html += `<a href="#" class="pagination-item" onclick="changePage(${pagination.page - 1})">Previous</a>`;
    } else {
        html += '<span class="pagination-item disabled">Previous</span>';
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.pages; i++) {
        const activeClass = i === pagination.page ? 'active' : '';
        html += `<a href="#" class="pagination-item ${activeClass}" onclick="changePage(${i})">${i}</a>`;
    }
    
    // Next button
    if (pagination.page < pagination.pages) {
        html += `<a href="#" class="pagination-item" onclick="changePage(${pagination.page + 1})">Next</a>`;
    } else {
        html += '<span class="pagination-item disabled">Next</span>';
    }
    
    html += '</div>';
    return html;
}

function showLoading() {
    contentArea.innerHTML = '<div class="spinner"></div>';
}

function hideLoading() {
    // Loading is hidden when content is rendered
}

function showError(message) {
    const alertHtml = `
        <div class="alert alert-danger">
            <strong>Error:</strong> ${message}
        </div>
    `;
    
    // Insert at the top of content area
    if (contentArea) {
        contentArea.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            const alert = contentArea.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
}

function showSuccess(message) {
    const alertHtml = `
        <div class="alert alert-success">
            <strong>Success:</strong> ${message}
        </div>
    `;
    
    if (contentArea) {
        contentArea.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = contentArea.querySelector('.alert');
            if (alert) alert.remove();
        }, 3000);
    }
}

// Modal Functions
function showModal(title, content, footer = '') {
    const modalHtml = `
        <div class="modal show" id="dynamicModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">${title}</h4>
                    <button class="modal-close" onclick="closeModal('dynamicModal')">&times;</button>
                </div>
                <div class="modal-body">
                    ${content}
                </div>
                ${footer ? `<div class="modal-footer">${footer}</div>` : ''}
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.remove();
}

function closeAllModals() {
    document.querySelectorAll('.modal').forEach(modal => modal.remove());
}

// Global functions for page navigation (to be used by page modules)
window.navigateToPage = navigateToPage;
window.showModal = showModal;
window.closeModal = closeModal;
window.closeAllModals = closeAllModals;
window.showError = showError;
window.showSuccess = showSuccess;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.formatDate = formatDate;
window.formatCurrency = formatCurrency;
window.getStatusClass = getStatusClass;
window.getStockStatusClass = getStockStatusClass;
window.renderPagination = renderPagination;
window.apiRequest = apiRequest;

// Update user interface
function updateUserInterface() {
    console.log('Update UI for user:', currentUser);
}

// Placeholder for customers page
function loadCustomers() {
    contentArea.innerHTML = '<h2>Customers</h2><p>Customer management will be implemented here</p>';
}
