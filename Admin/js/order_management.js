// API Configuration - FIXED
const API_BASE_URL = '../controllers';
const ORDERS_ENDPOINT = `${API_BASE_URL}/OrderController.php`;

// State management
let currentPage = 1;
let totalPages = 1;
let currentFilters = {
    search: '',
    status: 'all',
    date: 'all'
};
let currentOrderId = null;
let actionType = null;

// DOM Elements
const ordersTableBody = document.getElementById('ordersTableBody');
const orderCountSpan = document.getElementById('orderCount');
const paginationDiv = document.getElementById('pagination');
const orderDetailsModal = document.getElementById('orderDetailsModal');
const confirmationModal = document.getElementById('confirmationModal');
const searchInput = document.getElementById('search');
const statusFilter = document.getElementById('statusFilter');
const dateFilter = document.getElementById('dateFilter');
const applyFiltersBtn = document.getElementById('applyFilters');
const exportBtn = document.getElementById('exportBtn');

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    loadOrders();
    setupEventListeners();
    addStatusStyles();
});

// Setup event listeners
function setupEventListeners() {
    // Modal functionality
    const closeBtn = document.querySelector('.close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => closeOrderModal());
    }
    
    // Filter functionality
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', applyFilters);
    }
    
    // Export functionality
    if (exportBtn) {
        exportBtn.addEventListener('click', exportOrders);
    }
    
    // Order details modal buttons
    const updateStatusBtn = document.getElementById('updateStatusBtn');
    const cancelOrderBtn = document.getElementById('cancelOrderBtn');
    const printInvoiceBtn = document.getElementById('printInvoiceBtn');
    
    if (updateStatusBtn) {
        updateStatusBtn.addEventListener('click', updateOrderStatus);
    }
    if (cancelOrderBtn) {
        cancelOrderBtn.addEventListener('click', () => confirmCancelOrder());
    }
    if (printInvoiceBtn) {
        printInvoiceBtn.addEventListener('click', printInvoice);
    }
    
    // Confirmation modal
    const confirmCancel = document.getElementById('confirmCancel');
    const confirmAction = document.getElementById('confirmAction');
    
    if (confirmCancel) {
        confirmCancel.addEventListener('click', () => closeConfirmationModal());
    }
    if (confirmAction) {
        confirmAction.addEventListener('click', handleConfirmedAction);
    }
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === orderDetailsModal) closeOrderModal();
        if (e.target === confirmationModal) closeConfirmationModal();
    });
    
    // Enter key for search
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }
}

// Load orders from API
function loadOrders(page = 1) {
    showLoadingState(true);
    
    const params = new URLSearchParams({
        page: page,
        limit: 10,
        search: currentFilters.search,
        status: currentFilters.status,
        date: currentFilters.date
    });
    
    fetch(`${ORDERS_ENDPOINT}?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch orders');
            }
            return response.json();
        })
        .then(data => {
            if (data.orders) {
                displayOrders(data.orders);
                updatePagination(data.pagination);
                currentPage = data.pagination.current_page;
                totalPages = data.pagination.total_pages;
                updateOrderCount(data.pagination.total);
            } else {
                throw new Error('Invalid response format');
            }
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            showNotification('Failed to load orders: ' + error.message, 'error');
            displayOrders([]);
        })
        .finally(() => {
            showLoadingState(false);
        });
}

// Get status badge HTML with icon
function getStatusBadge(status) {
    const statusConfig = {
        'pending': {
            icon: '⏳',
            color: '#ff9800',
            bg: '#fff3e0',
            text: 'Pending'
        },
        'processing': {
            icon: '⚙️',
            color: '#2196f3',
            bg: '#e3f2fd',
            text: 'Processing'
        },
        'shipped': {
            icon: '🚚',
            color: '#9c27b0',
            bg: '#f3e5f5',
            text: 'Shipped'
        },
        'delivered': {
            icon: '✅',
            color: '#4caf50',
            bg: '#e8f5e9',
            text: 'Delivered'
        },
        'cancelled': {
            icon: '❌',
            color: '#f44336',
            bg: '#ffebee',
            text: 'Cancelled'
        }
    };
    
    const config = statusConfig[status.toLowerCase()] || statusConfig['pending'];
    
    return `
        <span class="status-badge" style="
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: ${config.bg};
            color: ${config.color};
            border: 1px solid ${config.color}33;
        ">
            <span style="font-size: 16px;">${config.icon}</span>
            ${config.text}
        </span>
    `;
}

// Get payment status badge HTML
function getPaymentBadge(paymentStatus) {
    const statusConfig = {
        'pending': {
            icon: '💳',
            color: '#ff9800',
            bg: '#fff3e0',
            text: 'Pending'
        },
        'paid': {
            icon: '💰',
            color: '#4caf50',
            bg: '#e8f5e9',
            text: 'Paid'
        },
        'failed': {
            icon: '⚠️',
            color: '#f44336',
            bg: '#ffebee',
            text: 'Failed'
        },
        'refunded': {
            icon: '↩️',
            color: '#9e9e9e',
            bg: '#f5f5f5',
            text: 'Refunded'
        }
    };
    
    const config = statusConfig[paymentStatus.toLowerCase()] || statusConfig['pending'];
    
    return `
        <span class="payment-badge" style="
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: ${config.bg};
            color: ${config.color};
            border: 1px solid ${config.color}33;
        ">
            <span style="font-size: 16px;">${config.icon}</span>
            ${config.text}
        </span>
    `;
}

// Display orders in the table
function displayOrders(orders) {
    if (!ordersTableBody) return;
    
    if (orders.length === 0) {
        ordersTableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">No orders found</td></tr>';
        return;
    }
    
    ordersTableBody.innerHTML = orders.map(order => `
        <tr>
            <td><strong>#${order.id}</strong></td>
            <td>${escapeHtml(order.customer_name)}</td>
            <td>${formatDate(order.created_at)}</td>
            <td><strong>$${parseFloat(order.total_amount).toFixed(2)}</strong></td>
            <td>${getStatusBadge(order.status)}</td>
            <td>${getPaymentBadge(order.payment_status)}</td>
            <td class="action-buttons">
                <button class="btn btn-success btn-sm view-order-btn" data-id="${order.id}">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn btn-primary btn-sm edit-order-btn" data-id="${order.id}">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </td>
        </tr>
    `).join('');
    
    // Add event listeners to action buttons
    document.querySelectorAll('.view-order-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const orderId = e.target.closest('.view-order-btn').dataset.id;
            viewOrderDetails(orderId);
        });
    });
    
    document.querySelectorAll('.edit-order-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const orderId = e.target.closest('.edit-order-btn').dataset.id;
            editOrder(orderId);
        });
    });
}

// Update pagination controls
function updatePagination(pagination) {
    if (!paginationDiv) return;
    
    const { current_page, total_pages } = pagination;
    
    let paginationHTML = '';
    
    // Previous button
    if (current_page > 1) {
        paginationHTML += `<button onclick="loadOrders(${current_page - 1})">&laquo; Prev</button>`;
    } else {
        paginationHTML += `<button disabled>&laquo; Prev</button>`;
    }
    
    // Page numbers
    const startPage = Math.max(1, current_page - 2);
    const endPage = Math.min(total_pages, current_page + 2);
    
    if (startPage > 1) {
        paginationHTML += `<button onclick="loadOrders(1)">1</button>`;
        if (startPage > 2) {
            paginationHTML += `<span>...</span>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === current_page) {
            paginationHTML += `<button class="active">${i}</button>`;
        } else {
            paginationHTML += `<button onclick="loadOrders(${i})">${i}</button>`;
        }
    }
    
    if (endPage < total_pages) {
        if (endPage < total_pages - 1) {
            paginationHTML += `<span>...</span>`;
        }
        paginationHTML += `<button onclick="loadOrders(${total_pages})">${total_pages}</button>`;
    }
    
    // Next button
    if (current_page < total_pages) {
        paginationHTML += `<button onclick="loadOrders(${current_page + 1})">Next &raquo;</button>`;
    } else {
        paginationHTML += `<button disabled>Next &raquo;</button>`;
    }
    
    paginationDiv.innerHTML = paginationHTML;
}

// Update order count display
function updateOrderCount(count) {
    if (orderCountSpan) {
        orderCountSpan.textContent = `Total orders: ${count}`;
    }
}

// View order details - FIXED URL
function viewOrderDetails(orderId) {
    showLoadingState(true);
    
    fetch(`${ORDERS_ENDPOINT}?id=${orderId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch order details');
            }
            return response.json();
        })
        .then(order => {
            if (order.id) {
                displayOrderDetails(order);
                currentOrderId = orderId;
                if (orderDetailsModal) {
                    orderDetailsModal.style.display = 'flex';
                }
            } else {
                throw new Error('Invalid order data');
            }
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            showNotification('Failed to fetch order details: ' + error.message, 'error');
        })
        .finally(() => {
            showLoadingState(false);
        });
}

// Display order details in modal
function displayOrderDetails(order) {
    const modalOrderTitle = document.getElementById('modalOrderTitle');
    const orderDate = document.getElementById('orderDate');
    const orderCustomer = document.getElementById('orderCustomer');
    const orderAddress = document.getElementById('orderAddress');
    const orderPhone = document.getElementById('orderPhone');
    const orderStatusSelect = document.getElementById('orderStatusSelect');
    const orderPaymentMethod = document.getElementById('orderPaymentMethod');
    const orderPaymentStatus = document.getElementById('orderPaymentStatus');
    const orderItemsList = document.getElementById('orderItemsList');
    const orderSummary = document.getElementById('orderSummary');
    const cancelBtn = document.getElementById('cancelOrderBtn');
    
    if (modalOrderTitle) modalOrderTitle.textContent = `Order Details - #${order.id}`;
    if (orderDate) orderDate.textContent = formatDateTime(order.created_at);
    if (orderCustomer) orderCustomer.textContent = `${escapeHtml(order.customer_name)} (${escapeHtml(order.customer_email)})`;
    if (orderAddress) orderAddress.textContent = escapeHtml(order.shipping_address);
    if (orderPhone) orderPhone.textContent = escapeHtml(order.customer_phone);
    if (orderStatusSelect) orderStatusSelect.value = order.status;
    if (orderPaymentMethod) orderPaymentMethod.textContent = escapeHtml(order.payment_method);
    if (orderPaymentStatus) {
        orderPaymentStatus.innerHTML = getPaymentBadge(order.payment_status);
    }
    
    // Display order items
    if (orderItemsList && order.items) {
        orderItemsList.innerHTML = order.items.map(item => `
            <div class="order-item">
                <img src="${escapeHtml(item.image_url || 'https://via.placeholder.com/60')}" class="item-image" alt="${escapeHtml(item.name)}">
                <div class="item-details">
                    <div class="item-name">${escapeHtml(item.name)}</div>
                    <div class="item-price">$${parseFloat(item.price).toFixed(2)} × ${item.quantity}</div>
                </div>
                <div class="item-total">$${(parseFloat(item.price) * item.quantity).toFixed(2)}</div>
            </div>
        `).join('');
    }
    
    // Display order summary
    if (orderSummary) {
        orderSummary.innerHTML = `
            <div class="summary-row">
                <div>Subtotal:</div>
                <div>$${parseFloat(order.subtotal).toFixed(2)}</div>
            </div>
            <div class="summary-row">
                <div>Shipping:</div>
                <div>$${parseFloat(order.shipping_cost).toFixed(2)}</div>
            </div>
            <div class="summary-row">
                <div>Tax:</div>
                <div>$${parseFloat(order.tax_amount).toFixed(2)}</div>
            </div>
            ${order.discount > 0 ? `
            <div class="summary-row">
                <div>Discount:</div>
                <div>-$${parseFloat(order.discount).toFixed(2)}</div>
            </div>
            ` : ''}
            <div class="summary-row summary-total">
                <div>Total:</div>
                <div>$${parseFloat(order.total_amount).toFixed(2)}</div>
            </div>
        `;
    }
    
    // Show/hide cancel button based on order status
    if (cancelBtn) {
        if (order.status === 'cancelled' || order.status === 'delivered') {
            cancelBtn.style.display = 'none';
        } else {
            cancelBtn.style.display = 'inline-block';
        }
    }
}

// Edit order
function editOrder(orderId) {
    viewOrderDetails(orderId);
}

// Close order modal
function closeOrderModal() {
    if (orderDetailsModal) {
        orderDetailsModal.style.display = 'none';
    }
    currentOrderId = null;
}

// Update order status - FIXED URL
function updateOrderStatus() {
    if (!currentOrderId) return;
    
    const orderStatusSelect = document.getElementById('orderStatusSelect');
    if (!orderStatusSelect) return;
    
    const newStatus = orderStatusSelect.value;
    
    fetch(`${ORDERS_ENDPOINT}?id=${currentOrderId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Order status updated successfully', 'success');
            closeOrderModal();
            loadOrders(currentPage);
        } else {
            throw new Error(data.error || 'Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error updating order status:', error);
        showNotification(error.message || 'Failed to update order status', 'error');
    });
}

// Confirm order cancellation
function confirmCancelOrder() {
    if (!currentOrderId) return;
    
    actionType = 'cancel';
    const confirmationTitle = document.getElementById('confirmationTitle');
    const confirmationMessage = document.getElementById('confirmationMessage');
    
    if (confirmationTitle) confirmationTitle.textContent = 'Confirm Order Cancellation';
    if (confirmationMessage) confirmationMessage.textContent = 'Are you sure you want to cancel this order? This action cannot be undone.';
    if (confirmationModal) confirmationModal.style.display = 'flex';
}

// Handle confirmed actions
function handleConfirmedAction() {
    if (actionType === 'cancel') {
        cancelOrder();
    }
    closeConfirmationModal();
}

// Close confirmation modal
function closeConfirmationModal() {
    if (confirmationModal) {
        confirmationModal.style.display = 'none';
    }
    actionType = null;
}

// Cancel order - FIXED URL
function cancelOrder() {
    if (!currentOrderId) return;
    
    fetch(`${ORDERS_ENDPOINT}?id=${currentOrderId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: 'cancelled'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Order cancelled successfully', 'success');
            closeOrderModal();
            loadOrders(currentPage);
        } else {
            throw new Error(data.error || 'Failed to cancel order');
        }
    })
    .catch(error => {
        console.error('Error cancelling order:', error);
        showNotification(error.message || 'Failed to cancel order', 'error');
    });
}

// Apply filters
function applyFilters() {
    currentFilters = {
        search: searchInput ? searchInput.value : '',
        status: statusFilter ? statusFilter.value : 'all',
        date: dateFilter ? dateFilter.value : 'all'
    };
    loadOrders(1);
}

// Export orders
function exportOrders() {
    showNotification('Preparing export...', 'warning');
    
    const params = new URLSearchParams({
        export: 'csv',
        search: currentFilters.search,
        status: currentFilters.status,
        date: currentFilters.date
    });
    
    window.location.href = `${ORDERS_ENDPOINT}?${params}`;
    
    setTimeout(() => {
        showNotification('Orders exported successfully', 'success');
    }, 1000);
}

// Print invoice
function printInvoice() {
    if (!currentOrderId) return;
    showNotification('Print functionality - Opening invoice view...', 'info');
}

// Show loading state
function showLoadingState(loading) {
    const tableContainer = document.querySelector('.table-container');
    if (tableContainer) {
        if (loading) {
            tableContainer.classList.add('loading');
        } else {
            tableContainer.classList.remove('loading');
        }
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4caf50' : type === 'error' ? '#f44336' : type === 'warning' ? '#ff9800' : '#2196f3'};
        color: white;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Format date and time for display
function formatDateTime(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Format status for display
function formatStatus(status) {
    return status.charAt(0).toUpperCase() + status.slice(1);
}

// Format payment status for display
function formatPaymentStatus(paymentStatus) {
    return paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1);
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
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

// Get authentication token (if needed)
function getAuthToken() {
    return localStorage.getItem('authToken');
}

// Add status badge styles
function addStatusStyles() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        .status-badge, .payment-badge {
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        
        .status-badge:hover, .payment-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
    `;
    document.head.appendChild(style);
}