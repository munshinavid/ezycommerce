// Complete Dashboard JavaScript with Chart.js and AJAX

let salesChart = null;
let revenueChart = null;
let currentPeriod = 7;

// Initialize Dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadSalesChart(7);
    loadRevenueChart();
    setupEventListeners();
});

// Setup Event Listeners
function setupEventListeners() {
    // Period selector for sales chart
    const periodSelector = document.querySelector('.chart-header select');
    if (periodSelector) {
        periodSelector.addEventListener('change', function() {
            let days = 7;
            switch(this.value) {
                case 'Last 7 Days':
                    days = 7;
                    break;
                case 'Last 30 Days':
                    days = 30;
                    break;
                case 'Last 90 Days':
                    days = 90;
                    break;
            }
            currentPeriod = days;
            loadSalesChart(days);
        });
    }

    // Search functionality
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filterTables(searchTerm);
        }, 300));
    }

    // View All buttons
    const viewAllButtons = document.querySelectorAll('.table-header .btn-primary');
    viewAllButtons.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            if (index === 0) {
                window.location.href = 'orders.php';
            } else {
                window.location.href = 'products.php';
            }
        });
    });
}

// Load Dashboard Statistics
function loadStats() {
    fetch('../controllers/DashboardAPI.php?action=stats')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                updateStatsCards(result.data);
                animateNumbers();
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            showNotification('Error loading statistics', 'error');
        });
}

// Update Statistics Cards with Animation
function updateStatsCards(data) {
    const cards = document.querySelectorAll('.stat-card .stat-info h3');
    if (cards[0]) cards[0].setAttribute('data-target', data.total_orders);
    if (cards[1]) cards[1].setAttribute('data-target', data.total_users);
    if (cards[2]) cards[2].setAttribute('data-target', data.total_products);
    if (cards[3]) cards[3].setAttribute('data-target', data.total_revenue);
}

// Animate Numbers
function animateNumbers() {
    const cards = document.querySelectorAll('.stat-card .stat-info h3');
    cards.forEach((card, index) => {
        const target = parseFloat(card.getAttribute('data-target'));
        const isRevenue = index === 3;
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            if (isRevenue) {
                card.textContent = '$' + current.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                card.textContent = Math.floor(current);
            }
        }, 20);
    });
}

// Load Sales Chart
function loadSalesChart(days) {
    fetch(`../controllers/DashboardAPI.php?action=sales_data&days=${days}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                renderSalesChart(result.data);
            }
        })
        .catch(error => {
            console.error('Error loading sales data:', error);
            showNotification('Error loading sales chart', 'error');
        });
}

// Render Sales Chart
function renderSalesChart(data) {
    const chartContainer = document.querySelector('.chart-placeholder');
    
    // Remove placeholder text and create canvas
    if (!document.getElementById('salesChart')) {
        chartContainer.innerHTML = '<canvas id="salesChart"></canvas>';
    }

    const ctx = document.getElementById('salesChart').getContext('2d');

    // Prepare data
    const labels = data.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    const revenues = data.map(item => parseFloat(item.revenue));
    const orders = data.map(item => parseInt(item.orders));

    // Destroy existing chart
    if (salesChart) {
        salesChart.destroy();
    }

    // Create new chart
    salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue ($)',
                data: revenues,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y',
                borderWidth: 2
            }, {
                label: 'Orders',
                data: orders,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.label === 'Revenue ($)') {
                                label += '$' + context.parsed.y.toFixed(2);
                            } else {
                                label += context.parsed.y;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(0);
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
}

// Load Revenue by Category Chart
function loadRevenueChart() {
    fetch('../controllers/DashboardAPI.php?action=revenue_by_category')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                renderRevenueChart(result.data);
            }
        })
        .catch(error => {
            console.error('Error loading revenue data:', error);
            showNotification('Error loading revenue chart', 'error');
        });
}

// Render Revenue by Category Chart
function renderRevenueChart(data) {
    const chartContainers = document.querySelectorAll('.chart-placeholder');
    const chartContainer = chartContainers[1];
    
    // Remove placeholder text and create canvas
    if (!document.getElementById('revenueChart')) {
        chartContainer.innerHTML = '<canvas id="revenueChart"></canvas>';
    }

    const ctx = document.getElementById('revenueChart').getContext('2d');

    // Prepare data
    const labels = data.map(item => item.category_name);
    const revenues = data.map(item => parseFloat(item.revenue));

    // Generate colors
    const colors = generateColors(data.length);

    // Destroy existing chart
    if (revenueChart) {
        revenueChart.destroy();
    }

    // Create new chart
    revenueChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: revenues,
                backgroundColor: colors.background,
                borderColor: colors.border,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Generate Colors for Charts
function generateColors(count) {
    const baseColors = [
        'rgb(255, 99, 132)',
        'rgb(54, 162, 235)',
        'rgb(255, 206, 86)',
        'rgb(75, 192, 192)',
        'rgb(153, 102, 255)',
        'rgb(255, 159, 64)',
        'rgb(199, 199, 199)',
        'rgb(83, 102, 255)',
        'rgb(255, 99, 255)',
        'rgb(99, 255, 132)'
    ];

    const background = [];
    const border = [];

    for (let i = 0; i < count; i++) {
        const color = baseColors[i % baseColors.length];
        background.push(color.replace('rgb', 'rgba').replace(')', ', 0.6)'));
        border.push(color);
    }

    return { background, border };
}

// Filter Tables based on search
function filterTables(searchTerm) {
    const tables = document.querySelectorAll('.table-container table tbody');
    
    tables.forEach(tbody => {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// Edit Order Status
function editOrder(orderId) {
    // Show modal or redirect to edit page
    const newStatus = prompt('Enter new status (Pending, Shipped, Delivered, Cancelled):');
    if (newStatus) {
        updateOrderStatus(orderId, newStatus);
    }
}

// Update Order Status
function updateOrderStatus(orderId, status) {
    fetch('../controllers/DashboardAPI.php?action=update_order_status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ order_id: orderId, status: status })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Order status updated successfully', 'success');
            location.reload();
        } else {
            showNotification('Error updating order status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating order status', 'error');
    });
}

// Edit Product
function editProduct(productId) {
    // Redirect to product edit page
    window.location.href = `edit_product.php?id=${productId}`;
}

// Show Notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        border-radius: 4px;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Debounce Function
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

// Refresh Dashboard
function refreshDashboard() {
    loadStats();
    loadSalesChart(currentPeriod);
    loadRevenueChart();
    showNotification('Dashboard refreshed', 'success');
}

// Export Data (Placeholder)
function exportData(type) {
    showNotification(`Exporting ${type} data...`, 'info');
    // Implement export functionality
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
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
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);