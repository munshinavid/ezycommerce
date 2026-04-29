<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - EzyCommerce Logistics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --pending: #ffc107;
            --processing: #17a2b8;
            --shipped: #007bff;
            --delivered: #28a745;
            --cancelled: #dc3545;
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
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: white;
            color: var(--dark);
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid var(--light-gray);
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .sidebar-header i {
            margin-bottom: 10px;
            display: block;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .sidebar-stats {
            padding: 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .stat-icon.pending {
            background-color: var(--pending);
        }

        .stat-icon.processing {
            background-color: var(--processing);
        }

        .stat-icon.shipped {
            background-color: var(--shipped);
        }

        .stat-icon.delivered {
            background-color: var(--delivered);
        }

        .stat-icon.cancelled {
            background-color: var(--cancelled);
        }

        .stat-label {
            font-weight: 500;
        }

        .stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .sidebar-menu {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover, .sidebar-menu li.active a {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .menu-badge {
            background-color: var(--light-gray);
            color: var(--dark);
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.8rem;
            margin-left: auto;
        }

        .sidebar-menu li.active .menu-badge {
            background-color: var(--primary);
            color: white;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
        }

        .header h1 {
            color: var(--dark);
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Filters Section */
        .filters-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .filters-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .toggle-filters {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .filters-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .filter-group {
            margin-bottom: 10px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.9rem;
        }

        .filter-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .filter-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            grid-column: 1 / -1;
        }

        /* Table Container */
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .table-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        th {
            background-color: var(--light);
            font-weight: 600;
            color: var(--dark);
            position: sticky;
            top: 0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background-color: #cce7ff;
            color: #004085;
        }

        .status-shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Buttons */
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
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

        .btn-success:hover {
            background-color: #3aa8d4;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background-color: #d41c6c;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--light-gray);
            color: var(--dark);
        }

        .btn-outline:hover {
            background-color: var(--light-gray);
        }

        /* Bulk Actions */
        .bulk-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background-color: var(--light);
            border-bottom: 1px solid var(--light-gray);
        }

        .bulk-checkbox {
            margin-right: 5px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-top: 1px solid var(--light-gray);
        }

        .pagination-info {
            font-size: 0.9rem;
            color: var(--gray);
        }

        .pagination-controls {
            display: flex;
            gap: 5px;
        }

        .page-btn {
            padding: 5px 10px;
            border: 1px solid var(--light-gray);
            background: white;
            border-radius: 3px;
            cursor: pointer;
        }

        .page-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-btn:hover:not(.active) {
            background-color: var(--light-gray);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .modal-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        /* Order Details */
        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-section {
            margin-bottom: 15px;
        }

        .detail-section h4 {
            margin-bottom: 10px;
            color: var(--dark);
            font-size: 1rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: var(--gray);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .filters-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
            }
            
            .sidebar-menu ul {
                display: flex;
                overflow-x: auto;
                padding: 10px;
            }
            
            .sidebar-menu li {
                margin-bottom: 0;
                margin-right: 10px;
                flex-shrink: 0;
            }
            
            .sidebar-menu a {
                padding: 10px 15px;
                border-radius: 5px;
                border-left: none;
                border-bottom: 3px solid transparent;
            }
            
            .sidebar-menu a:hover, .sidebar-menu li.active a {
                border-left: none;
                border-bottom-color: var(--primary);
            }
            
            .filters-content {
                grid-template-columns: 1fr;
            }
            
            .order-details {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-truck fa-2x"></i>
                <h2>EzyCommerce Logistics</h2>
            </div>
            
            <div class="sidebar-stats">
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-value" id="sidebar-pending-count">24</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon processing">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="stat-label">Processing</div>
                    </div>
                    <div class="stat-value" id="sidebar-processing-count">18</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon shipped">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="stat-label">Shipped</div>
                    </div>
                    <div class="stat-value" id="sidebar-shipped-count">32</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon delivered">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-label">Delivered</div>
                    </div>
                    <div class="stat-value" id="sidebar-delivered-count">45</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon cancelled">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-label">Cancelled</div>
                    </div>
                    <div class="stat-value" id="sidebar-cancelled-count">3</div>
                </div>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li><a href="<?php echo url('/logistics'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="active"><a href="<?php echo url('/logistics/shipping'); ?>#all" data-status="all"><i class="fas fa-list"></i> All Orders <span class="menu-badge" id="menu-all-count">122</span></a></li>
                    <li><a href="<?php echo url('/logistics/shipping'); ?>#pending" data-status="pending"><i class="fas fa-clock" style="color: var(--pending);"></i> Pending <span class="menu-badge" id="menu-pending-count">24</span></a></li>
                    <li><a href="<?php echo url('/logistics/shipping'); ?>#processing" data-status="processing"><i class="fas fa-cog" style="color: var(--processing);"></i> Processing <span class="menu-badge" id="menu-processing-count">18</span></a></li>
                    <li><a href="<?php echo url('/logistics/shipping'); ?>#shipped" data-status="shipped"><i class="fas fa-shipping-fast" style="color: var(--shipped);"></i> Shipped <span class="menu-badge" id="menu-shipped-count">32</span></a></li>
                    <li><a href="<?php echo url('/logistics/shipping'); ?>#delivered" data-status="delivered"><i class="fas fa-check-circle" style="color: var(--delivered);"></i> Delivered <span class="menu-badge" id="menu-delivered-count">45</span></a></li>
                    <li><a href="<?php echo url('/logistics/shipping'); ?>#cancelled" data-status="cancelled"><i class="fas fa-times-circle" style="color: var(--cancelled);"></i> Cancelled <span class="menu-badge" id="menu-cancelled-count">3</span></a></li>
                    <li><a href="<?php echo url('/logistics/returns'); ?>"><i class="fas fa-undo"></i> Returns</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-chart-bar"></i> Analytics</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="<?php echo url('/logistics/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Orders Management</h1>
                <div class="user-info">
                    <div class="user-avatar">LM</div>
                    <span>Logistics Manager</span>
                </div>
            </div>
            
            <!-- Filters Section -->
            <div class="filters-container">
                <div class="filters-header">
                    <h3>Filters</h3>
                    <button class="toggle-filters" id="toggle-filters">
                        <i class="fas fa-sliders-h"></i> Toggle Filters
                    </button>
                </div>
                <div class="filters-content" id="filters-content">
                    <div class="filter-group">
                        <label for="date-range">Date Range</label>
                        <select id="date-range" class="filter-control">
                            <option value="all">All Dates</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="customer-name">Customer Name</label>
                        <input type="text" id="customer-name" class="filter-control" placeholder="Search customer...">
                    </div>
                    <div class="filter-group">
                        <label for="order-id">Order ID</label>
                        <input type="text" id="order-id" class="filter-control" placeholder="Search order ID...">
                    </div>
                    <div class="filter-group">
                        <label for="items-count">Items Count</label>
                        <select id="items-count" class="filter-control">
                            <option value="all">Any Items Count</option>
                            <option value="1">1 item</option>
                            <option value="2-5">2-5 items</option>
                            <option value="5-10">5-10 items</option>
                            <option value="10+">10+ items</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="amount-range">Amount Range</label>
                        <select id="amount-range" class="filter-control">
                            <option value="all">Any Amount</option>
                            <option value="0-50">$0 - $50</option>
                            <option value="50-100">$50 - $100</option>
                            <option value="100-200">$100 - $200</option>
                            <option value="200+">$200+</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button class="btn btn-primary" id="apply-filters">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-outline" id="reset-filters">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Bulk Actions -->
            <div class="bulk-actions" id="bulk-actions" style="display: none;">
                <input type="checkbox" id="select-all" class="bulk-checkbox">
                <span id="selected-count">0 orders selected</span>
                <select id="bulk-action" class="filter-control" style="width: auto;">
                    <option value="">Bulk Actions</option>
                    <option value="Processing">Mark as Processing</option>
                    <option value="Shipped">Mark as Shipped</option>
                    <option value="Delivered">Mark as Delivered</option>
                    <option value="Cancelled">Cancel Orders</option>
                </select>
                <button class="btn btn-primary btn-sm" id="apply-bulk-action">Apply</button>
            </div>
            
            <!-- Orders Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2 id="table-title">All Orders</h2>
                    <div class="table-actions">
                        <button class="btn btn-outline" id="export-orders">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn btn-primary" id="refresh-orders">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" id="select-all-orders"></th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="orders-body">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
                <div class="pagination">
                    <div class="pagination-info" id="pagination-info">Showing 1-10 of 122 orders</div>
                    <div class="pagination-controls">
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <button class="page-btn">4</button>
                        <button class="page-btn">5</button>
                        <button class="page-btn">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Details Modal -->
    <div class="modal" id="order-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Order Details - <span id="modal-order-id"></span></h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="order-details">
                    <div>
                        <div class="detail-section">
                            <h4>Customer Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Name:</span>
                                <span id="customer-name-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span id="customer-email-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span id="customer-phone-detail"></span>
                            </div>
                        </div>
                        <div class="detail-section">
                            <h4>Order Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Date:</span>
                                <span id="order-date-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span id="order-status-detail"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="detail-section">
                            <h4>Shipping Address</h4>
                            <div id="shipping-address-detail"></div>
                        </div>
                        <div class="detail-section">
                            <h4>Payment Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Method:</span>
                                <span id="payment-method-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span id="payment-status-detail"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4>Order Items</h4>
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="order-items-detail">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: bold;">Subtotal:</td>
                                <td id="order-subtotal-detail"></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: bold;">Shipping:</td>
                                <td id="order-shipping-detail"></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                                <td id="order-total-detail"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="form-actions">
                    <button class="btn btn-danger" id="close-order-modal">Close</button>
                    <button class="btn btn-primary" id="update-order-status">Update Status</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Update Status Modal -->
    <div class="modal" id="status-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Order Status</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="status-form">
                    <div class="form-group">
                        <label for="modal-order-id-input">Order ID</label>
                        <input type="text" id="modal-order-id-input" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modal-order-status">Status</label>
                        <select id="modal-order-status" class="form-control">
                            <option value="Pending">Pending</option>
                            <option value="Processing">Processing</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group" id="tracking-group" style="display: none;">
                        <label for="modal-tracking-number">Tracking Number</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="modal-tracking-number" class="form-control">
                            <button type="button" class="btn btn-primary" id="generate-tracking">
                                <i class="fas fa-barcode"></i> Generate
                            </button>
                        </div>
                    </div>
                    <div class="form-group" id="carrier-group" style="display: none;">
                        <label for="modal-carrier">Carrier</label>
                        <select id="modal-carrier" class="form-control">
                            <option value="UPS">UPS</option>
                            <option value="FedEx">FedEx</option>
                            <option value="USPS">USPS</option>
                            <option value="DHL">DHL</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-danger" id="cancel-status-update">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="<?php echo url('/Logistics/js/allshipping.js'); ?>"></script>
</body>
</html>