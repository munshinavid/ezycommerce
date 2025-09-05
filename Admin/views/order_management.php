
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - E-Commerce Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/user_management.css">
    <link rel="stylesheet" href="../css/sidebar.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include '../layout/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search orders...">
            </div>
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=Super+Admin&background=random" alt="Admin">
                <div>
                    <h4>Super Admin</h4>
                </div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Orders Management</h1>
            <div>
                <button class="btn btn-primary">
                    <i class="fas fa-file-export"></i> Export Orders
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <label for="search">Search Orders</label>
                <input type="text" id="search" placeholder="Search by order ID or customer">
            </div>
            <div class="filter-group">
                <label for="statusFilter">Filter by Status</label>
                <select id="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="dateFilter">Filter by Date</label>
                <select id="dateFilter">
                    <option value="all">All Dates</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter">This Quarter</option>
                </select>
            </div>
            <button class="btn btn-primary" style="align-self: flex-end;">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>

        <!-- Orders Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>All Orders</h3>
                <div>
                    <span>Showing 1-10 of 84 orders</span>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#ORD-7842</td>
                        <td>John Smith</td>
                        <td>Jun 24, 2023</td>
                        <td>$128.99</td>
                        <td><span class="order-status status-delivered">Delivered</span></td>
                        <td><span class="payment-status payment-completed">Completed</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-7841</td>
                        <td>Emma Johnson</td>
                        <td>Jun 24, 2023</td>
                        <td>$89.50</td>
                        <td><span class="order-status status-shipped">Shipped</span></td>
                        <td><span class="payment-status payment-completed">Completed</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-7840</td>
                        <td>Michael Brown</td>
                        <td>Jun 23, 2023</td>
                        <td>$245.75</td>
                        <td><span class="order-status status-processing">Processing</span></td>
                        <td><span class="payment-status payment-completed">Completed</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-7839</td>
                        <td>Sarah Wilson</td>
                        <td>Jun 23, 2023</td>
                        <td>$54.25</td>
                        <td><span class="order-status status-cancelled">Cancelled</span></td>
                        <td><span class="payment-status payment-failed">Failed</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-7838</td>
                        <td>David Miller</td>
                        <td>Jun 22, 2023</td>
                        <td>$189.99</td>
                        <td><span class="order-status status-pending">Pending</span></td>
                        <td><span class="payment-status payment-pending">Pending</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-7837</td>
                        <td>Jennifer Lopez</td>
                        <td>Jun 22, 2023</td>
                        <td>$321.40</td>
                        <td><span class="order-status status-delivered">Delivered</span></td>
                        <td><span class="payment-status payment-completed">Completed</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-7836</td>
                        <td>Robert Taylor</td>
                        <td>Jun 21, 2023</td>
                        <td>$76.80</td>
                        <td><span class="order-status status-shipped">Shipped</span></td>
                        <td><span class="payment-status payment-completed">Completed</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-7835</td>
                        <td>Amanda Clark</td>
                        <td>Jun 21, 2023</td>
                        <td>$145.30</td>
                        <td><span class="order-status status-processing">Processing</span></td>
                        <td><span class="payment-status payment-completed">Completed</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-7834</td>
                        <td>James Anderson</td>
                        <td>Jun 20, 2023</td>
                        <td>$92.45</td>
                        <td><span class="order-status status-cancelled">Cancelled</span></td>
                        <td><span class="payment-status payment-failed">Failed</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-7833</td>
                        <td>Lisa Garcia</td>
                        <td>Jun 20, 2023</td>
                        <td>$210.20</td>
                        <td><span class="order-status status-delivered">Delivered</span></td>
                        <td><span class="payment-status payment-completed">Completed</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-order-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <button>&laquo;</button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button>4</button>
                <button>5</button>
                <button>&raquo;</button>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal" id="orderDetailsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Order Details - #ORD-7842</h3>
                <button class="close-btn">&times;</button>
            </div>
            
            <div class="order-details">
                <div class="detail-row">
                    <div class="detail-label">Order Date:</div>
                    <div class="detail-value">June 24, 2023 at 14:32</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Customer:</div>
                    <div class="detail-value">John Smith (john.smith@example.com)</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Shipping Address:</div>
                    <div class="detail-value">123 Main St, Apt 4B, New York, NY 10001, United States</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value">+1 (555) 123-4567</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value"><span class="order-status status-delivered">Delivered</span></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Payment Method:</div>
                    <div class="detail-value">Credit Card (Visa ending in 7654)</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Payment Status:</div>
                    <div class="detail-value"><span class="payment-status payment-completed">Completed</span></div>
                </div>
            </div>
            
            <div class="order-items">
                <h4>Order Items</h4>
                <div class="order-item">
                    <img src="https://via.placeholder.com/60" class="item-image" alt="Wireless Headphones">
                    <div class="item-details">
                        <div class="item-name">Wireless Headphones</div>
                        <div class="item-price">$129.99 × 1</div>
                    </div>
                    <div class="item-total">$129.99</div>
                </div>
                
                <div class="order-summary">
                    <div class="summary-row">
                        <div>Subtotal:</div>
                        <div>$129.99</div>
                    </div>
                    <div class="summary-row">
                        <div>Shipping:</div>
                        <div>$5.99</div>
                    </div>
                    <div class="summary-row">
                        <div>Tax:</div>
                        <div>$7.80</div>
                    </div>
                    <div class="summary-row">
                        <div>Discount:</div>
                        <div>-$15.00</div>
                    </div>
                    <div class="summary-row summary-total">
                        <div>Total:</div>
                        <div>$128.78</div>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button class="btn btn-danger">Cancel Order</button>
                <button class="btn btn-primary">Update Status</button>
                <button class="btn btn-warning">Print Invoice</button>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const viewOrderBtns = document.querySelectorAll('.view-order-btn');
        const orderDetailsModal = document.getElementById('orderDetailsModal');
        const closeBtn = document.querySelector('.close-btn');

        viewOrderBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                orderDetailsModal.style.display = 'flex';
            });
        });

        closeBtn.addEventListener('click', () => {
            orderDetailsModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === orderDetailsModal) {
                orderDetailsModal.style.display = 'none';
            }
        });

        // Filter functionality
        document.getElementById('search').addEventListener('input', (e) => {
            const searchValue = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const orderId = row.children[0].textContent.toLowerCase();
                const customer = row.children[1].textContent.toLowerCase();
                
                if (orderId.includes(searchValue) || customer.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.getElementById('statusFilter').addEventListener('change', (e) => {
            const statusValue = e.target.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const status = row.children[4].textContent.toLowerCase();
                
                if (statusValue === 'all' || status.includes(statusValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>