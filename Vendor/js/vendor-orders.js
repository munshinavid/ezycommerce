// Configuration
const API_BASE_URL = '/api/vendor/orders';
const VENDOR_ID = 1; // In production, get from session/auth

// DOM Elements
const tabs = document.querySelectorAll('.tab');
const ordersContainer = document.getElementById('orders-container');
const statusModal = document.getElementById('status-modal');
const orderModal = document.getElementById('order-modal');
const closeModalBtns = document.querySelectorAll('.close-modal');
const cancelStatusBtn = document.getElementById('cancel-status');
const closeOrderDetailsBtn = document.getElementById('close-order-details');
const statusForm = document.getElementById('status-form');
const orderStatusSelect = document.getElementById('modal-order-status');
const cancelReasonGroup = document.getElementById('cancel-reason-group');
const refreshOrdersBtn = document.getElementById('refresh-orders');
const exportOrdersBtn = document.getElementById('export-orders');
const updateOrderFromDetailsBtn = document.getElementById('update-order-from-details');
const filtersContent = document.getElementById('filters-content');
const toggleFiltersBtn = document.getElementById('toggle-filters');
const applyFiltersBtn = document.getElementById('apply-filters');
const resetFiltersBtn = document.getElementById('reset-filters');

// Current state
let currentTab = 'pending';
let currentOrders = [];
let currentOrderToUpdate = null;

// Current vendor data
let currentVendor = {
    id: VENDOR_ID,
    name: "Loading...",
    email: "Loading..."
};

// Initialize the orders page
function initOrdersPage() {
    loadVendorData();
    setupEventListeners();
    loadOrdersData();
}

// Setup event listeners
function setupEventListeners() {
    // Navigation
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', (e) => {
            if (!link.getAttribute('href') || link.getAttribute('href') === '#') {
                e.preventDefault();
            }
        });
    });
    
    // Tab navigation
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
    
    // Buttons
    refreshOrdersBtn.addEventListener('click', loadOrdersData);
    exportOrdersBtn.addEventListener('click', exportOrders);
    
    // Modals
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', closeModals);
    });
    
    cancelStatusBtn.addEventListener('click', closeModals);
    closeOrderDetailsBtn.addEventListener('click', closeModals);
    
    statusForm.addEventListener('submit', updateOrderStatus);
    updateOrderFromDetailsBtn.addEventListener('click', openStatusModalFromDetails);
    
    orderStatusSelect.addEventListener('change', function() {
        cancelReasonGroup.style.display = this.value === 'cancelled' ? 'block' : 'none';
    });
    
    // Filters
    toggleFiltersBtn.addEventListener('click', toggleFilters);
    applyFiltersBtn.addEventListener('click', applyFilters);
    resetFiltersBtn.addEventListener('click', resetFilters);
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === statusModal) closeModals();
        if (e.target === orderModal) closeModals();
    });
}

// Load vendor data from API
function loadVendorData() {
    fetch(`${API_BASE_URL}?action=get_vendor_info&vendor_id=${VENDOR_ID}`)
        .then(response => response.json())
        .then(data => {
            currentVendor = {
                id: data.vendor_id,
                name: data.name,
                email: data.email
            };
            document.getElementById('vendor-name').textContent = currentVendor.name;
            document.getElementById('vendor-email').textContent = currentVendor.email;
        })
        .catch(error => {
            console.error('Error loading vendor data:', error);
        });
}

// Switch between tabs
function switchTab(tabName) {
    currentTab = tabName;
    
    // Update active tab
    tabs.forEach(tab => {
        if (tab.getAttribute('data-tab') === tabName) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Filter and render orders
    const filteredOrders = currentOrders.filter(order => order.status === tabName);
    renderOrders(filteredOrders);
}

// Load orders data from API
function loadOrdersData() {
    // Show loading state
    ordersContainer.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-spinner fa-spin"></i>
            <h3>Loading orders...</h3>
        </div>
    `;
    
    fetch(`${API_BASE_URL}?action=get_orders&vendor_id=${VENDOR_ID}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            currentOrders = data.orders;
            updateOrderCounts(data.orders);
            
            // Filter orders by current tab
            const filteredOrders = data.orders.filter(order => order.status === currentTab);
            renderOrders(filteredOrders);
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            ordersContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Error loading orders</h3>
                    <p>${error.message}</p>
                </div>
            `;
        });
}

// Update order counts in tabs
function updateOrderCounts(orders) {
    const counts = {
        pending: 0,
        'ready-to-ship': 0,
        shipped: 0,
        delivered: 0,
        cancelled: 0
    };
    
    orders.forEach(order => {
        counts[order.status]++;
    });
    
    document.getElementById('pending-count').textContent = counts.pending;
    document.getElementById('ready-to-ship-count').textContent = counts['ready-to-ship'];
    document.getElementById('shipped-count').textContent = counts.shipped;
    document.getElementById('delivered-count').textContent = counts.delivered;
    document.getElementById('cancelled-count').textContent = counts.cancelled;
}

// Render orders
function renderOrders(orders) {
    ordersContainer.innerHTML = '';
    
    if (orders.length === 0) {
        ordersContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h3>No ${currentTab.replace('-', ' ')} orders</h3>
                <p>There are no ${currentTab.replace('-', ' ')} orders matching your criteria.</p>
            </div>
        `;
        return;
    }
    
    orders.forEach(order => {
        const orderCard = document.createElement('div');
        orderCard.className = 'order-card';
        
        // Calculate vendor total
        const vendorTotal = order.vendor_total;
        const shipping = vendorTotal * 0.1;
        const total = vendorTotal + shipping;
        
        // Check if vendor can update status
        const canUpdate = order.can_update_status && 
                         (order.status === 'pending' || order.status === 'ready-to-ship');
        
        orderCard.innerHTML = `
            <div class="order-header">
                <div class="order-info">
                    <div class="order-id">${order.id}</div>
                    <div class="order-date">${formatDate(order.date)}</div>
                </div>
                <div class="order-status status-${order.status}">${formatStatus(order.status)}</div>
            </div>
            
            <div class="order-customer">
                <div class="customer-name">${order.customer.name}</div>
                <div class="customer-email">${order.customer.email}</div>
            </div>
            
            <div class="order-products">
                <div class="products-title">Your Products (${order.vendor_products.length})</div>
                ${order.vendor_products.map(product => `
                    <div class="product-item">
                        <div class="product-info">
                            <img src="${product.image}" alt="${product.name}" class="product-image">
                            <div class="product-details">
                                <div class="product-name">${product.name}</div>
                                <div class="product-sku">${product.sku}</div>
                            </div>
                        </div>
                        <div class="product-quantity">Qty: ${product.quantity}</div>
                    </div>
                `).join('')}
            </div>
            
            <div class="order-totals">
                <div class="total-item">
                    <span class="total-label">Subtotal:</span>
                    <span class="total-value">$${vendorTotal.toFixed(2)}</span>
                </div>
                <div class="total-item">
                    <span class="total-label">Shipping:</span>
                    <span class="total-value">$${shipping.toFixed(2)}</span>
                </div>
                <div class="vendor-total">
                    <div class="total-item">
                        <span class="total-label">Your Total:</span>
                        <span class="total-value">$${total.toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <div class="order-actions">
                <button class="btn btn-outline btn-sm view-order-details" data-id="${order.id}">
                    <i class="fas fa-eye"></i> View Details
                </button>
                ${canUpdate ? `
                    <button class="btn btn-primary btn-sm update-order-status" data-id="${order.id}">
                        <i class="fas fa-edit"></i> Update Status
                    </button>
                ` : !order.can_update_status ? `
                    <button class="btn btn-secondary btn-sm" disabled title="Status locked after marking Ready to Ship">
                        <i class="fas fa-lock"></i> Status Locked
                    </button>
                ` : ''}
            </div>
        `;
        
        ordersContainer.appendChild(orderCard);
    });
    
    // Add event listeners to action buttons
    document.querySelectorAll('.view-order-details').forEach(button => {
        button.addEventListener('click', (e) => {
            const orderId = e.target.closest('button').getAttribute('data-id');
            viewOrderDetails(orderId);
        });
    });
    
    document.querySelectorAll('.update-order-status').forEach(button => {
        button.addEventListener('click', (e) => {
            const orderId = e.target.closest('button').getAttribute('data-id');
            openStatusModal(orderId);
        });
    });
}

// View order details
function viewOrderDetails(orderId) {
    const order = currentOrders.find(o => o.id === orderId);
    if (!order) return;
    
    const vendorTotal = order.vendor_total;
    const shipping = vendorTotal * 0.1;
    const total = vendorTotal + shipping;
    
    // Populate modal with order details
    document.getElementById('detail-order-id').textContent = order.id;
    document.getElementById('detail-order-date').textContent = formatDate(order.date);
    document.getElementById('detail-customer-name').textContent = order.customer.name;
    document.getElementById('detail-customer-email').textContent = order.customer.email;
    document.getElementById('detail-order-status').textContent = formatStatus(order.status);
    document.getElementById('detail-vendor-total').textContent = total.toFixed(2);
    
    // Populate products list
    const productsList = document.getElementById('detail-products-list');
    productsList.innerHTML = '';
    
    order.vendor_products.forEach(product => {
        const productItem = document.createElement('div');
        productItem.className = 'product-item';
        productItem.innerHTML = `
            <div class="product-info">
                <img src="${product.image}" alt="${product.name}" class="product-image">
                <div class="product-details">
                    <div class="product-name">${product.name}</div>
                    <div class="product-sku">${product.sku}</div>
                    <div>Price: $${product.price} | Qty: ${product.quantity}</div>
                </div>
            </div>
            <div class="total-value">$${(product.price * product.quantity).toFixed(2)}</div>
        `;
        productsList.appendChild(productItem);
    });
    
    // Store current order for status update
    currentOrderToUpdate = order;
    
    // Show/hide update button based on status
    const updateBtn = document.getElementById('update-order-from-details');
    if (order.can_update_status && (order.status === 'pending' || order.status === 'ready-to-ship')) {
        updateBtn.style.display = 'inline-flex';
    } else {
        updateBtn.style.display = 'none';
    }
    
    orderModal.style.display = 'flex';
}

// Open status modal
function openStatusModal(orderId) {
    const order = currentOrders.find(o => o.id === orderId);
    if (!order) return;
    
    // Check if status can be updated
    if (!order.can_update_status) {
        alert('Cannot update status. This order has already been marked as Ready to Ship and handed over to logistics.');
        return;
    }
    
    document.getElementById('modal-order-id').value = order.id;
    document.getElementById('modal-order-status').value = order.status;
    document.getElementById('cancel-reason').value = '';
    cancelReasonGroup.style.display = 'none';
    
    // Store current order for status update
    currentOrderToUpdate = order;
    
    statusModal.style.display = 'flex';
}

// Open status modal from details view
function openStatusModalFromDetails() {
    if (!currentOrderToUpdate) return;
    
    if (!currentOrderToUpdate.can_update_status) {
        alert('Cannot update status. This order has already been marked as Ready to Ship and handed over to logistics.');
        return;
    }
    
    document.getElementById('modal-order-id').value = currentOrderToUpdate.id;
    document.getElementById('modal-order-status').value = currentOrderToUpdate.status;
    document.getElementById('cancel-reason').value = '';
    cancelReasonGroup.style.display = 'none';
    
    orderModal.style.display = 'none';
    statusModal.style.display = 'flex';
}

// Close all modals
function closeModals() {
    statusModal.style.display = 'none';
    orderModal.style.display = 'none';
    currentOrderToUpdate = null;
}

// Update order status via API
function updateOrderStatus(e) {
    e.preventDefault();
    
    if (!currentOrderToUpdate) return;
    
    const newStatus = document.getElementById('modal-order-status').value;
    const cancelReason = document.getElementById('cancel-reason').value;
    
    // Validate cancellation reason
    if (newStatus === 'cancelled' && !cancelReason) {
        alert('Please provide a reason for cancellation.');
        return;
    }
    
    // Prepare data
    const data = {
        order_id: currentOrderToUpdate.id,
        status: newStatus,
        cancel_reason: cancelReason
    };
    
    // Send API request
    fetch(`${API_BASE_URL}?action=update_status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            throw new Error(result.error);
        }
        
        alert(`Order ${currentOrderToUpdate.id} status updated to ${formatStatus(newStatus)}`);
        closeModals();
        loadOrdersData(); // Refresh orders
    })
    .catch(error => {
        alert('Error updating order status: ' + error.message);
    });
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
    const dateRange = document.getElementById('date-range').value;
    const customerName = document.getElementById('customer-name').value;
    const orderId = document.getElementById('order-id').value;
    const productName = document.getElementById('product-name').value;
    
    console.log('Applying filters:', { dateRange, customerName, orderId, productName });
    
    alert('Filters applied! In a real application, this would filter the order data.');
    
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
    document.getElementById('product-name').value = '';
    
    alert('Filters reset!');
}

// Export orders
function exportOrders() {
    alert('Exporting orders data...');
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Format status for display
function formatStatus(status) {
    return status.split('-').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
}

// Initialize the page when loaded
document.addEventListener('DOMContentLoaded', initOrdersPage);