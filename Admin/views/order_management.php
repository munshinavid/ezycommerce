<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - E-Commerce Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/user_management.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        /* Additional styles for enhanced functionality */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .notification.success {
            background-color: #28a745;
        }
        
        .notification.error {
            background-color: #dc3545;
        }
        
        .notification.warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            justify-content: center;
            align-items: center;
        }
        
        .confirmation-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 400px;
            width: 90%;
        }
        
        .confirmation-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .pagination button {
            margin: 0 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
            background-color: white;
            cursor: pointer;
        }
        
        .pagination button.active {
            background-color: #007bff;
            color: white;
        }
        
        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .status-select {
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        
        .order-details-section {
            margin-bottom: 20px;
        }
        
        .order-details-section h4 {
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
    </style>
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
                <input type="text" placeholder="Search orders..." id="searchInput">
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
                <button class="btn btn-primary" id="exportBtn">
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
            <button class="btn btn-primary" id="applyFilters" style="align-self: flex-end;">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>

        <!-- Orders Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>All Orders</h3>
                <div>
                    <span id="orderCount">Loading orders...</span>
                </div>
            </div>
            <div class="table-wrapper">
                <table id="ordersTable">
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
                    <tbody id="ordersTableBody">
                        <!-- Orders will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination" id="pagination">
                <!-- Pagination will be generated here via AJAX -->
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal" id="orderDetailsModal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3 id="modalOrderTitle">Order Details</h3>
                <button class="close-btn">&times;</button>
            </div>
            
            <div class="order-details">
                <div class="order-details-section">
                    <h4>Order Information</h4>
                    <div class="detail-row">
                        <div class="detail-label">Order Date:</div>
                        <div class="detail-value" id="orderDate">Loading...</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Customer:</div>
                        <div class="detail-value" id="orderCustomer">Loading...</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Shipping Address:</div>
                        <div class="detail-value" id="orderAddress">Loading...</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Phone:</div>
                        <div class="detail-value" id="orderPhone">Loading...</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value">
                            <select id="orderStatusSelect" class="status-select">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Payment Method:</div>
                        <div class="detail-value" id="orderPaymentMethod">Loading...</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Payment Status:</div>
                        <div class="detail-value" id="orderPaymentStatus">Loading...</div>
                    </div>
                </div>
                
                <div class="order-items">
                    <h4>Order Items</h4>
                    <div id="orderItemsList">
                        <!-- Order items will be loaded here -->
                    </div>
                    
                    <div class="order-summary" id="orderSummary">
                        <!-- Order summary will be loaded here -->
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button class="btn btn-danger" id="cancelOrderBtn">Cancel Order</button>
                <button class="btn btn-primary" id="updateStatusBtn">Update Status</button>
                <button class="btn btn-warning" id="printInvoiceBtn">Print Invoice</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-content">
            <h3 id="confirmationTitle">Confirm Action</h3>
            <p id="confirmationMessage">Are you sure you want to perform this action?</p>
            <div class="confirmation-actions">
                <button class="btn btn-danger" id="confirmCancel">Cancel</button>
                <button class="btn btn-primary" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/order_management.js"></script>
</body>
</html>