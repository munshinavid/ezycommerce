
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - E-Commerce Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --sidebar-width: 250px;
            --header-height: 70px;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #333;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-top: 10px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid white;
        }

        .menu-item i {
            margin-right: 12px;
            font-size: 18px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
        }

        /* Header */
        .header {
            background-color: white;
            padding: 15px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background-color: #f5f7fb;
            border-radius: 20px;
            padding: 8px 15px;
            width: 300px;
        }

        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            padding: 5px 10px;
            width: 100%;
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
        }

        /* Filters */
        .filters {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 200px;
        }

        .filter-group label {
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        .filter-group select, .filter-group input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }

        /* Table */
        .table-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .order-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-processing { background-color: #cce5ff; color: #004085; }
        .status-shipped { background-color: #d4edda; color: #155724; }
        .status-delivered { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }

        .payment-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .payment-pending { background-color: #fff3cd; color: #856404; }
        .payment-completed { background-color: #d4edda; color: #155724; }
        .payment-failed { background-color: #f8d7da; color: #721c24; }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 8px;
        }

        .pagination button {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background-color: white;
            cursor: pointer;
        }

        .pagination button.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: var(--border-radius);
            width: 700px;
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 25px;
            box-shadow: var(--box-shadow);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .modal-header h3 {
            font-size: 20px;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        /* Order Details */
        .order-details {
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: 600;
            width: 150px;
        }

        .detail-value {
            flex: 1;
        }

        .order-items {
            margin: 20px 0;
        }

        .order-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 15px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .item-price {
            color: #6c757d;
        }

        .item-total {
            font-weight: 600;
        }

        /* Order Summary */
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-total {
            font-weight: 600;
            font-size: 18px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: visible;
            }
            
            .sidebar-header h2, .menu-item span {
                display: none;
            }
            
            .menu-item {
                justify-content: center;
                padding: 15px;
            }
            
            .menu-item i {
                margin-right: 0;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .filters {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .filter-group {
                width: 100%;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            .search-bar {
                width: 100%;
            }
            
            .user-profile {
                width: 100%;
                justify-content: flex-end;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Super Admin 👑</h2>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-users"></i>
                <span>Users Management</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-box"></i>
                <span>Products Management</span>
            </div>
            <div class="menu-item active">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders Management</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-tags"></i>
                <span>Discounts Management</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports & Analytics</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </div>
        </div>
    </div>

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