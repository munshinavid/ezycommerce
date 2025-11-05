// Configuration
const API_BASE_URL = '../controllers/DashboarAPI.php';

// DOM Elements
const recentOrdersBody = document.getElementById('recent-orders-body');
const returnRequestsBody = document.getElementById('return-requests-body');
const shippingManagementBody = document.getElementById('shipping-management-body');
const statusModal = document.getElementById('status-modal');
const returnModal = document.getElementById('return-modal');
const statusForm = document.getElementById('status-form');
const returnForm = document.getElementById('return-form');
const closeModalBtns = document.querySelectorAll('.close-modal');
const refreshOrdersBtn = document.getElementById('refresh-orders');
const refreshReturnsBtn = document.getElementById('refresh-returns');
const refreshShippingBtn = document.getElementById('refresh-shipping');
const cancelUpdateBtn = document.getElementById('cancel-update');
const cancelReturnBtn = document.getElementById('cancel-return');
const generateTrackingBtn = document.getElementById('generate-tracking');
const orderStatusSelect = document.getElementById('modal-order-status');
const trackingGroup = document.getElementById('tracking-group');

// Charts
let ordersChart, shippingChart;

// Initialize the dashboard
function initDashboard() {
    loadDashboardData();
    setupEventListeners();
    initializeCharts();
}

// Setup event listeners
function setupEventListeners() {
    refreshOrdersBtn.addEventListener('click', () => loadDashboardData());
    refreshReturnsBtn.addEventListener('click', () => loadDashboardData());
    refreshShippingBtn.addEventListener('click', () => loadDashboardData());
    
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', closeModals);
    });
    
    cancelUpdateBtn.addEventListener('click', closeModals);
    cancelReturnBtn.addEventListener('click', closeModals);
    
    statusForm.addEventListener('submit', updateOrderStatus);
    returnForm.addEventListener('submit', processReturnRequest);
    
    orderStatusSelect.addEventListener('change', function() {
        trackingGroup.style.display = this.value === 'Shipped' ? 'block' : 'none';
    });
    
    generateTrackingBtn.addEventListener('click', generateTrackingNumber);
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === statusModal) closeModals();
        if (e.target === returnModal) closeModals();
    });
}

// Load dashboard data
function loadDashboardData() {
    fetch(`${API_BASE_URL}?endpoint=dashboard`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;
                updateDashboardCards(data.counts, data.returns);
                renderRecentOrders(data.recentOrders);
                renderReturnRequests(data.returns);
                renderShippingManagement(data.shipping);
                updateCharts(data.counts, data.shipping);
            } else {
                throw new Error(result.error || 'Failed to load dashboard data');
            }
        })
        .catch(error => {
            console.error('Error loading dashboard data:', error);
            alert('Error loading dashboard data. Please try again.');
        });
}

// Update dashboard cards with counts
function updateDashboardCards(counts, returnsData) {
    document.getElementById('pending-count').textContent = counts.pending;
    document.getElementById('processing-count').textContent = counts.processing;
    document.getElementById('shipped-count').textContent = counts.shipped;
    document.getElementById('returns-count').textContent = returnsData.length;
}

// Render recent orders table
function renderRecentOrders(orders) {
    recentOrdersBody.innerHTML = '';
    
    if (orders.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="6" style="text-align: center;">No recent orders found</td>`;
        recentOrdersBody.appendChild(row);
        return;
    }
    
    orders.forEach(order => {
        console.log(order);
        const statusClass = `status-${order.status.toLowerCase()}`;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${order.order_id}</td>
            <td>${order.customer_name}</td>
            <td>${formatDate(order.created_at)}</td>
            <td><span class="status-badge ${statusClass}">${order.status}</span></td>
            <td>$${order.total_amount}</td>
            <td>
                <button class="btn btn-primary btn-sm update-order" data-id="${order.order_id}" data-status="${order.status}">
                    <i class="fas fa-edit"></i> Update
                </button>
            </td>
        `;
        recentOrdersBody.appendChild(row);
    });
    
    // Add event listeners to update buttons
    document.querySelectorAll('.update-order').forEach(button => {
        button.addEventListener('click', openStatusModal);
    });
}

// Render return requests table
function renderReturnRequests(returns) {
    returnRequestsBody.innerHTML = '';
    
    if (returns.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="5" style="text-align: center;">No return requests found</td>`;
        returnRequestsBody.appendChild(row);
        return;
    }
    
    returns.forEach(returnReq => {
        const statusClass = `status-return-${returnReq.status.toLowerCase()}`;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${returnReq.return_id}</td>
            <td>${returnReq.order_id}</td>
            <td>${returnReq.reason.substring(0, 50)}${returnReq.reason.length > 50 ? '...' : ''}</td>
            <td><span class="status-badge ${statusClass}">${returnReq.status}</span></td>
            <td>
                <button class="btn btn-primary btn-sm process-return" 
                        data-id="${returnReq.return_id}" 
                        data-order-id="${returnReq.order_id}"
                        data-reason="${returnReq.reason}"
                        data-status="${returnReq.status}">
                    <i class="fas fa-cog"></i> Process
                </button>
            </td>
        `;
        returnRequestsBody.appendChild(row);
    });
    
    // Add event listeners to process buttons
    document.querySelectorAll('.process-return').forEach(button => {
        button.addEventListener('click', openReturnModal);
    });
}

// Render shipping management table
function renderShippingManagement(shippingData) {
    console.log(shippingData);
    shippingManagementBody.innerHTML = '';
    
    if (shippingData.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="6" style="text-align: center;">No shipping data found</td>`;
        shippingManagementBody.appendChild(row);
        return;
    }
    
    shippingData.forEach(item => {
        const statusClass = `status-${item.shipping_status.toLowerCase()}`;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.order_id}</td>
            <td>${item.customer_name}</td>
            <td><span class="status-badge ${statusClass}">${item.shipping_status}</span></td>
            <td>${item.tracking_number || 'Not assigned'}</td>
            <td>${formatDate(item.updated_at)}</td>
            <td>
                <button class="btn btn-primary btn-sm update-shipping" 
                        data-id="${item.order_id}"
                        data-status="${item.shipping_status}"
                        data-tracking="${item.tracking_number || ''}">
                    <i class="fas fa-edit"></i> Update
                </button>
            </td>
        `;
        shippingManagementBody.appendChild(row);
    });
    
    // Add event listeners to update buttons
    document.querySelectorAll('.update-shipping').forEach(button => {
        button.addEventListener('click', openShippingModal);
    });
}

// Open status update modal for orders
function openStatusModal(e) {
    const button = e.target.closest('button');
    const orderId = button.getAttribute('data-id');
    const currentStatus = button.getAttribute('data-status');
    
    document.getElementById('modal-order-id').value = orderId;
    document.getElementById('modal-order-status').value = currentStatus;
    document.getElementById('modal-tracking-number').value = '';
    
    // Show/hide tracking field based on status
    trackingGroup.style.display = currentStatus === 'Shipped' ? 'block' : 'none';
    
    statusModal.style.display = 'flex';
}

// Open shipping update modal
function openShippingModal(e) {
    const button = e.target.closest('button');
    const orderId = button.getAttribute('data-id');
    const currentStatus = button.getAttribute('data-status');
    const trackingNumber = button.getAttribute('data-tracking');
    
    document.getElementById('modal-order-id').value = orderId;
    document.getElementById('modal-order-status').value = currentStatus;
    document.getElementById('modal-tracking-number').value = trackingNumber;
    
    // Show tracking field for shipped status
    trackingGroup.style.display = currentStatus === 'Shipped' ? 'block' : 'none';
    
    statusModal.style.display = 'flex';
}

// Open return processing modal
function openReturnModal(e) {
    const button = e.target.closest('button');
    const returnId = button.getAttribute('data-id');
    const orderId = button.getAttribute('data-order-id');
    const reason = button.getAttribute('data-reason');
    const status = button.getAttribute('data-status');
    
    document.getElementById('modal-return-id').value = returnId;
    document.getElementById('modal-return-order-id').value = orderId;
    document.getElementById('modal-return-reason').value = reason;
    document.getElementById('modal-return-status').value = status;
    
    returnModal.style.display = 'flex';
}

// Close all modals
function closeModals() {
    statusModal.style.display = 'none';
    returnModal.style.display = 'none';
}

// Update order status
function updateOrderStatus(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('modal-order-id').value;
    const status = document.getElementById('modal-order-status').value;
    const trackingNumber = document.getElementById('modal-tracking-number').value;
    
    // Validate tracking number for shipped orders
    if (status === 'Shipped' && !trackingNumber) {
        alert('Please enter or generate a tracking number for shipped orders.');
        return;
    }
    
    const data = {
        order_id: orderId,
        status: status,
        tracking_number: trackingNumber
    };
    
    fetch(`${API_BASE_URL}?endpoint=orders&action=update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(`Order ${orderId} status updated to ${status}`);
            closeModals();
            loadDashboardData();
        } else {
            throw new Error(result.error || 'Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating order status: ' + error.message);
    });
}

// Process return request
function processReturnRequest(e) {
    e.preventDefault();
    
    const returnId = document.getElementById('modal-return-id').value;
    const status = document.getElementById('modal-return-status').value;
    
    const data = {
        return_id: returnId,
        status: status
    };
    
    fetch(`${API_BASE_URL}?endpoint=returns&action=process`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(`Return request ${returnId} has been ${status.toLowerCase()}`);
            closeModals();
            loadDashboardData();
        } else {
            throw new Error(result.error || 'Failed to process return');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing return: ' + error.message);
    });
}

// Generate tracking number
function generateTrackingNumber() {
    const tracking = 'TRK' + Date.now().toString().substring(7) + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    document.getElementById('modal-tracking-number').value = tracking;
}

// Initialize charts
function initializeCharts() {
    const ordersCtx = document.getElementById('orders-chart').getContext('2d');
    const shippingCtx = document.getElementById('shipping-chart').getContext('2d');
    
    // Orders chart
    ordersChart = new Chart(ordersCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
            datasets: [{
                data: [0, 0, 0, 0, 0],
                backgroundColor: [
                    '#fff3cd',
                    '#cce7ff',
                    '#d1ecf1',
                    '#d4edda',
                    '#f8d7da'
                ],
                borderColor: [
                    '#856404',
                    '#004085',
                    '#0c5460',
                    '#155724',
                    '#721c24'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Shipping performance chart
    shippingChart = new Chart(shippingCtx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Orders Shipped',
                data: [0, 0, 0, 0, 0, 0, 0],
                backgroundColor: '#4361ee',
                borderColor: '#3f37c9',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5
                    }
                }
            }
        }
    });
}

// Update charts with data
function updateCharts(counts, shippingData) {
    // Update orders chart
    ordersChart.data.datasets[0].data = [
        counts.pending,
        counts.processing,
        counts.shipped,
        counts.delivered,
        counts.cancelled
    ];
    ordersChart.update();
    
    // Update shipping chart with mock data for demonstration
    const shippedData = [12, 19, 15, 17, 14, 8, 5];
    shippingChart.data.datasets[0].data = shippedData;
    shippingChart.update();
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Initialize the dashboard when the page loads
document.addEventListener('DOMContentLoaded', initDashboard);