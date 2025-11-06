// Configuration
const API_BASE_URL = '../controllers/AllShippingAPI.php';

// DOM Elements
const sidebarMenuItems = document.querySelectorAll('.sidebar-menu a[data-status]');
const filtersContent = document.getElementById('filters-content');
const toggleFiltersBtn = document.getElementById('toggle-filters');
const applyFiltersBtn = document.getElementById('apply-filters');
const resetFiltersBtn = document.getElementById('reset-filters');
const bulkActions = document.getElementById('bulk-actions');
const selectAllCheckbox = document.getElementById('select-all-orders');
const ordersBody = document.getElementById('orders-body');
const tableTitle = document.getElementById('table-title');
const orderModal = document.getElementById('order-modal');
const statusModal = document.getElementById('status-modal');
const closeModalBtns = document.querySelectorAll('.close-modal');
const closeOrderModalBtn = document.getElementById('close-order-modal');
const cancelStatusUpdateBtn = document.getElementById('cancel-status-update');
const statusForm = document.getElementById('status-form');
const orderStatusSelect = document.getElementById('modal-order-status');
const trackingGroup = document.getElementById('tracking-group');
const carrierGroup = document.getElementById('carrier-group');
const generateTrackingBtn = document.getElementById('generate-tracking');
const updateOrderStatusBtn = document.getElementById('update-order-status');
const refreshOrdersBtn = document.getElementById('refresh-orders');
const exportOrdersBtn = document.getElementById('export-orders');

// Current filter state
let currentStatusFilter = 'all';
let currentOrders = [];
let allOrders = []; // Store all orders for filtering

// Initialize the page
function initOrdersPage() {
    loadOrdersCounts();
    loadOrders('all');
    setupEventListeners();
}

// Setup event listeners
function setupEventListeners() {
    // Sidebar menu navigation
    sidebarMenuItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const status = item.getAttribute('data-status');
            
            // Update active state
            sidebarMenuItems.forEach(i => i.parentElement.classList.remove('active'));
            item.parentElement.classList.add('active');
            
            // Update current filter and reload orders from server
            currentStatusFilter = status;
            
            // Clear selection when switching filters
            clearOrderSelection();
            
            // Fetch fresh data from server
            loadOrders(status);
        });
    });
    
    // Filters
    toggleFiltersBtn.addEventListener('click', toggleFilters);
    applyFiltersBtn.addEventListener('click', applyFilters);
    resetFiltersBtn.addEventListener('click', resetFilters);
    
    // Bulk actions
    selectAllCheckbox.addEventListener('change', handleSelectAll);
    document.getElementById('apply-bulk-action').addEventListener('click', applyBulkAction);
    
    // Modals
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', closeModals);
    });
    
    closeOrderModalBtn.addEventListener('click', closeModals);
    cancelStatusUpdateBtn.addEventListener('click', closeModals);
    updateOrderStatusBtn.addEventListener('click', openStatusModal);
    
    statusForm.addEventListener('submit', updateOrderStatus);
    
    orderStatusSelect.addEventListener('change', function() {
        trackingGroup.style.display = this.value === 'Shipped' ? 'block' : 'none';
        carrierGroup.style.display = this.value === 'Shipped' ? 'block' : 'none';
    });
    
    generateTrackingBtn.addEventListener('click', generateTrackingNumber);
    
    // Refresh and export buttons
    refreshOrdersBtn.addEventListener('click', refreshOrders);
    exportOrdersBtn.addEventListener('click', exportOrders);
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === orderModal) closeModals();
        if (e.target === statusModal) closeModals();
    });
}

// ============================================
// API FUNCTIONS
// ============================================

// Load orders counts
async function loadOrdersCounts() {
    try {
        const response = await fetch(`${API_BASE_URL}?action=counts`);
        const result = await response.json();
        
        if (result.success) {
            const counts = result.data;
            
            // Update sidebar counts
            document.getElementById('sidebar-pending-count').textContent = counts.pending;
            document.getElementById('sidebar-processing-count').textContent = counts.processing;
            document.getElementById('sidebar-shipped-count').textContent = counts.shipped;
            document.getElementById('sidebar-delivered-count').textContent = counts.delivered;
            document.getElementById('sidebar-cancelled-count').textContent = counts.cancelled;
            
            // Update menu counts
            document.getElementById('menu-all-count').textContent = counts.all;
            document.getElementById('menu-pending-count').textContent = counts.pending;
            document.getElementById('menu-processing-count').textContent = counts.processing;
            document.getElementById('menu-shipped-count').textContent = counts.shipped;
            document.getElementById('menu-delivered-count').textContent = counts.delivered;
            document.getElementById('menu-cancelled-count').textContent = counts.cancelled;
        }
    } catch (error) {
        console.error('Error loading counts:', error);
        showNotification('Failed to load order counts', 'error');
    }
}

// Load orders by status
async function loadOrders(status) {
    try {
        // Show loading state
        ordersBody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin"></i> Loading orders...</td></tr>';
        
        const response = await fetch(`${API_BASE_URL}?action=list&status=${status}`);
        const result = await response.json();
        
        if (result.success) {
            allOrders = result.data; // Store all orders
            currentOrders = result.data;
            renderOrdersTable(status, currentOrders);
            
            // Show success notification for filter changes (except initial load)
            if (document.readyState === 'complete') {
                const statusNames = {
                    'all': 'All Orders',
                    'pending': 'Pending Orders',
                    'processing': 'Processing Orders',
                    'shipped': 'Shipped Orders',
                    'delivered': 'Delivered Orders',
                    'cancelled': 'Cancelled Orders'
                };
                showNotification(`Loaded ${result.total} ${statusNames[status]}`, 'success');
            }
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        ordersBody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: red; padding: 40px;"><i class="fas fa-exclamation-triangle"></i> Failed to load orders</td></tr>';
        showNotification('Failed to load orders', 'error');
    }
}

// Get order details
async function loadOrderDetails(orderId) {
    try {
        const response = await fetch(`${API_BASE_URL}?action=details&id=${orderId}`);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        console.error('Error loading order details:', error);
        showNotification('Failed to load order details', 'error');
        return null;
    }
}

// Update order status
async function updateOrderStatusAPI(orderId, status, trackingNumber = null, carrier = null) {
    try {
        const payload = {
            order_id: orderId,
            status: status
        };
        
        if (trackingNumber) {
            payload.tracking_number = trackingNumber;
        }
        
        if (carrier) {
            payload.carrier = carrier;
        }
        
        const response = await fetch(`${API_BASE_URL}?action=update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error);
        }
        
        return result;
    } catch (error) {
        console.error('Error updating order status:', error);
        throw error;
    }
}

// Bulk update orders
async function bulkUpdateOrdersAPI(orderIds, status) {
    try {
        const response = await fetch(`${API_BASE_URL}?action=bulk-update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order_ids: orderIds,
                status: status
            })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error);
        }
        
        return result;
    } catch (error) {
        console.error('Error bulk updating orders:', error);
        throw error;
    }
}

// ============================================
// UI FUNCTIONS
// ============================================

// Render orders table
function renderOrdersTable(status, orders) {
    ordersBody.innerHTML = '';
    
    // Update table title
    const statusTitles = {
        'all': 'All Orders',
        'pending': 'Pending Orders',
        'processing': 'Processing Orders',
        'shipped': 'Shipped Orders',
        'delivered': 'Delivered Orders',
        'cancelled': 'Cancelled Orders'
    };
    tableTitle.textContent = statusTitles[status] || 'All Orders';
    
    if (orders.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">
            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
            No orders found
        </td>`;
        ordersBody.appendChild(row);
        return;
    }
    
    orders.forEach(order => {
        const statusClass = `status-${order.status}`;
        const statusText = order.status.charAt(0).toUpperCase() + order.status.slice(1);
        
        const row = document.createElement('tr');
        
        // Add event listener for row click (to view details)
        row.addEventListener('click', (e) => {
            // Don't trigger if clicking on a button or checkbox
            if (!e.target.closest('button') && !e.target.closest('input[type="checkbox"]')) {
                openOrderModal(order.id);
            }
        });
        
        row.innerHTML = `
            <td><input type="checkbox" class="order-checkbox" data-order-id="${order.id}"></td>
            <td>${order.id}</td>
            <td>${order.customer}</td>
            <td>${formatDate(order.date)}</td>
            <td>${order.items} item(s)</td>
            <td>$${order.amount.toFixed(2)}</td>
            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
            <td>
                <button class="btn btn-primary btn-sm view-order-btn" data-id="${order.id}">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn btn-success btn-sm update-status-btn" data-id="${order.id}">
                    <i class="fas fa-edit"></i> Update
                </button>
            </td>
        `;
        
        ordersBody.appendChild(row);
    });
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.order-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    // Add event listeners to buttons
    document.querySelectorAll('.view-order-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const orderId = button.getAttribute('data-id');
            openOrderModal(orderId);
        });
    });
    
    document.querySelectorAll('.update-status-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const orderId = button.getAttribute('data-id');
            const order = currentOrders.find(o => o.id === orderId);
            if (order) {
                document.getElementById('modal-order-id-input').value = order.id;
                document.getElementById('modal-order-status').value = order.status.charAt(0).toUpperCase() + order.status.slice(1);
                statusModal.style.display = 'flex';
            }
        });
    });
    
    // Update pagination info
    document.getElementById('pagination-info').textContent = `Showing 1-${Math.min(orders.length, 10)} of ${orders.length} orders`;
}

// Filter orders based on current filters
function filterOrders() {
    const dateRange = document.getElementById('date-range').value;
    const customerName = document.getElementById('customer-name').value.toLowerCase();
    const orderId = document.getElementById('order-id').value.toLowerCase();
    const itemsCount = document.getElementById('items-count').value;
    const amountRange = document.getElementById('amount-range').value;
    
    let filtered = [...allOrders];
    
    // Filter by customer name
    if (customerName) {
        filtered = filtered.filter(order => 
            order.customer.toLowerCase().includes(customerName) ||
            order.email.toLowerCase().includes(customerName)
        );
    }
    
    // Filter by order ID
    if (orderId) {
        filtered = filtered.filter(order => 
            order.id.toLowerCase().includes(orderId)
        );
    }
    
    // Filter by date range
    if (dateRange !== 'all') {
        const now = new Date();
        const orderDate = new Date();
        
        filtered = filtered.filter(order => {
            const oDate = new Date(order.date);
            
            switch(dateRange) {
                case 'today':
                    return oDate.toDateString() === now.toDateString();
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    return oDate >= weekAgo;
                case 'month':
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    return oDate >= monthAgo;
                case 'year':
                    return oDate.getFullYear() === now.getFullYear();
                default:
                    return true;
            }
        });
    }
    
    // Filter by items count
    if (itemsCount !== 'all') {
        filtered = filtered.filter(order => {
            switch(itemsCount) {
                case '1':
                    return order.items === 1;
                case '2-5':
                    return order.items >= 2 && order.items <= 5;
                case '6+':
                    return order.items >= 6;
                default:
                    return true;
            }
        });
    }
    
    // Filter by amount range
    if (amountRange !== 'all') {
        filtered = filtered.filter(order => {
            switch(amountRange) {
                case '0-50':
                    return order.amount <= 50;
                case '51-100':
                    return order.amount > 50 && order.amount <= 100;
                case '101-500':
                    return order.amount > 100 && order.amount <= 500;
                case '500+':
                    return order.amount > 500;
                default:
                    return true;
            }
        });
    }
    
    currentOrders = filtered;
    renderOrdersTable(currentStatusFilter, currentOrders);
}

// Clear order selection
function clearOrderSelection() {
    // Uncheck select all
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
    }
    
    // Hide bulk actions
    bulkActions.style.display = 'none';
}

// Open order details modal
async function openOrderModal(orderId) {
    const orderDetails = await loadOrderDetails(orderId);
    
    if (!orderDetails) {
        return;
    }
    
    // Populate modal with order details
    document.getElementById('modal-order-id').textContent = orderDetails.id;
    document.getElementById('customer-name-detail').textContent = orderDetails.customer.name;
    document.getElementById('customer-email-detail').textContent = orderDetails.customer.email;
    document.getElementById('customer-phone-detail').textContent = orderDetails.customer.phone;
    document.getElementById('order-date-detail').textContent = formatDate(orderDetails.date);
    document.getElementById('order-status-detail').textContent = orderDetails.status;
    document.getElementById('shipping-address-detail').textContent = orderDetails.shipping;
    document.getElementById('payment-method-detail').textContent = orderDetails.payment.method;
    document.getElementById('payment-status-detail').textContent = orderDetails.payment.status;
    
    // Populate order items
    const itemsContainer = document.getElementById('order-items-detail');
    itemsContainer.innerHTML = '';
    orderDetails.items.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>${item.quantity}</td>
            <td>$${item.price.toFixed(2)}</td>
            <td>$${item.total.toFixed(2)}</td>
        `;
        itemsContainer.appendChild(row);
    });
    
    document.getElementById('order-subtotal-detail').textContent = `$${orderDetails.subtotal.toFixed(2)}`;
    document.getElementById('order-shipping-detail').textContent = `$${orderDetails.shipping_cost.toFixed(2)}`;
    document.getElementById('order-total-detail').textContent = `$${orderDetails.total.toFixed(2)}`;
    
    orderModal.style.display = 'flex';
}

// Open status update modal from order details
function openStatusModal() {
    const orderId = document.getElementById('modal-order-id').textContent;
    const currentStatus = document.getElementById('order-status-detail').textContent;
    
    document.getElementById('modal-order-id-input').value = orderId;
    document.getElementById('modal-order-status').value = currentStatus;
    
    // Show/hide tracking field based on status
    trackingGroup.style.display = currentStatus === 'Shipped' ? 'block' : 'none';
    carrierGroup.style.display = currentStatus === 'Shipped' ? 'block' : 'none';
    
    orderModal.style.display = 'none';
    statusModal.style.display = 'flex';
}

// Close all modals
function closeModals() {
    orderModal.style.display = 'none';
    statusModal.style.display = 'none';
}

// Update order status
async function updateOrderStatus(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('modal-order-id-input').value;
    const status = document.getElementById('modal-order-status').value;
    const trackingNumber = document.getElementById('modal-tracking-number').value;
    const carrier = document.getElementById('modal-carrier').value;
    
    // Validate tracking number for shipped orders
    if (status === 'Shipped' && !trackingNumber) {
        showNotification('Please enter or generate a tracking number for shipped orders', 'error');
        return;
    }
    
    try {
        await updateOrderStatusAPI(orderId, status, trackingNumber, carrier);
        showNotification(`Order ${orderId} status updated to ${status}`, 'success');
        closeModals();
        refreshOrders();
    } catch (error) {
        showNotification('Error updating order status: ' + error.message, 'error');
    }
}

// Toggle filters visibility
function toggleFilters() {
    if (filtersContent.style.display === 'none') {
        filtersContent.style.display = 'grid';
        toggleFiltersBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Hide Filters';
    } else {
        filtersContent.style.display = 'none';
        toggleFiltersBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Show Filters';
    }
}

// Apply filters
function applyFilters() {
    filterOrders();
    showNotification(`Showing ${currentOrders.length} filtered orders`, 'success');
    
    // Close filters on mobile
    if (window.innerWidth < 768) {
        filtersContent.style.display = 'none';
        toggleFiltersBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Show Filters';
    }
}

// Reset filters
function resetFilters() {
    document.getElementById('date-range').value = 'all';
    document.getElementById('customer-name').value = '';
    document.getElementById('order-id').value = '';
    document.getElementById('items-count').value = 'all';
    document.getElementById('amount-range').value = 'all';
    
    currentOrders = allOrders;
    renderOrdersTable(currentStatusFilter, currentOrders);
    showNotification('Filters reset!', 'info');
}

// Handle select all checkboxes
function handleSelectAll(e) {
    const isChecked = e.target.checked;
    
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    
    updateBulkActions();
}

// Update bulk actions based on selected items
function updateBulkActions() {
    const selectedCount = document.querySelectorAll('.order-checkbox:checked').length;
    
    if (selectedCount > 0) {
        bulkActions.style.display = 'flex';
        document.getElementById('selected-count').textContent = `${selectedCount} orders selected`;
    } else {
        bulkActions.style.display = 'none';
        selectAllCheckbox.checked = false;
    }
}

// Apply bulk action
async function applyBulkAction() {
    const action = document.getElementById('bulk-action').value;
    const selectedOrders = [];
    
    document.querySelectorAll('.order-checkbox:checked').forEach(checkbox => {
        const orderId = checkbox.getAttribute('data-order-id');
        selectedOrders.push(orderId);
    });
    
    if (!action || selectedOrders.length === 0) {
        showNotification('Please select an action and at least one order', 'error');
        return;
    }
    
    try {
        const result = await bulkUpdateOrdersAPI(selectedOrders, action);
        showNotification(result.message, 'success');
        
        // Reset selection
        document.querySelectorAll('.order-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        bulkActions.style.display = 'none';
        
        refreshOrders();
    } catch (error) {
        showNotification('Error applying bulk action: ' + error.message, 'error');
    }
}

// Generate tracking number
function generateTrackingNumber() {
    const tracking = 'TRK' + Date.now().toString().substring(7) + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    document.getElementById('modal-tracking-number').value = tracking;
}

// Refresh orders
function refreshOrders() {
    loadOrdersCounts();
    loadOrders(currentStatusFilter);
}

// Export orders
function exportOrders() {
    window.location.href = `${API_BASE_URL}?action=export&status=${currentStatusFilter}`;
    showNotification('Exporting orders...', 'info');
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS for animations
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
`;
document.head.appendChild(style);

// Initialize the page when loaded
document.addEventListener('DOMContentLoaded', initOrdersPage);