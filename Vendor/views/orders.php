<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Vendor Dashboard</title>
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
            --ready-to-ship: #17a2b8;
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

        .vendor-info {
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
            text-align: center;
        }

        .vendor-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .vendor-email {
            color: var(--gray);
            font-size: 0.9rem;
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

        .header-actions {
            display: flex;
            gap: 10px;
        }

        /* Tabs Navigation */
        .tabs {
            display: flex;
            background: white;
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .tab {
            flex: 1;
            padding: 12px 20px;
            text-align: center;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .tab.active {
            background-color: var(--primary);
            color: white;
        }

        .tab:hover:not(.active) {
            background-color: var(--light-gray);
        }

        .tab-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.8rem;
            margin-left: 5px;
        }

        .tab:not(.active) .tab-badge {
            background-color: var(--light-gray);
            color: var(--dark);
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

        /* Orders Cards */
        .orders-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .order-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-info {
            display: flex;
            flex-direction: column;
        }

        .order-id {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .order-date {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .order-status {
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

        .status-ready-to-ship {
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

        .order-customer {
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .customer-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .customer-email {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .order-products {
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .products-title {
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: var(--dark);
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
        }

        .product-details {
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-weight: 500;
            font-size: 0.9rem;
        }

        .product-sku {
            color: var(--gray);
            font-size: 0.8rem;
        }

        .product-quantity {
            font-weight: 600;
            color: var(--dark);
        }

        .order-totals {
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .total-label {
            color: var(--gray);
        }

        .total-value {
            font-weight: 600;
        }

        .vendor-total {
            background-color: var(--light);
            padding: 8px 12px;
            border-radius: 5px;
            margin-top: 5px;
        }

        .order-actions {
            padding: 15px 20px;
            display: flex;
            gap: 10px;
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

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--light-gray);
            margin-bottom: 15px;
        }

        .empty-state h3 {
            color: var(--gray);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--gray);
            margin-bottom: 20px;
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
            max-width: 500px;
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

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .filters-content {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .orders-container {
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
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
            
            .tabs {
                flex-direction: column;
            }
            
            .filters-content {
                grid-template-columns: 1fr;
            }
            
            .orders-container {
                grid-template-columns: 1fr;
            }
            
            .order-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-store fa-2x"></i>
                <h2>Vendor Dashboard</h2>
            </div>
            
            <div class="vendor-info">
                <div class="vendor-name" id="vendor-name">TechGadgets Inc.</div>
                <div class="vendor-email" id="vendor-email">contact@techgadgets.com</div>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li><a href="vendor-dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="vendor-products.html"><i class="fas fa-box"></i> Products</a></li>
                    <li class="active"><a href="#"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="vendor-sales.html"><i class="fas fa-chart-line"></i> Sales Analytics</a></li>
                    <li><a href="vendor-returns.html"><i class="fas fa-undo"></i> Returns</a></li>
                    <li><a href="vendor-discounts.html"><i class="fas fa-tag"></i> Discounts</a></li>
                    <li><a href="vendor-profile.html"><i class="fas fa-user"></i> Profile</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Orders Management</h1>
                <div class="header-actions">
                    <button class="btn btn-outline" id="export-orders">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button class="btn btn-primary" id="refresh-orders">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            
            <!-- Tabs Navigation -->
            <div class="tabs">
                <div class="tab active" data-tab="pending">
                    Pending Orders <span class="tab-badge" id="pending-count">5</span>
                </div>
                <div class="tab" data-tab="ready-to-ship">
                    Ready to Ship <span class="tab-badge" id="ready-to-ship-count">3</span>
                </div>
                <div class="tab" data-tab="shipped">
                    Shipped <span class="tab-badge" id="shipped-count">8</span>
                </div>
                <div class="tab" data-tab="delivered">
                    Delivered <span class="tab-badge" id="delivered-count">12</span>
                </div>
                <div class="tab" data-tab="cancelled">
                    Cancelled <span class="tab-badge" id="cancelled-count">2</span>
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
                        <label for="product-name">Product Name</label>
                        <input type="text" id="product-name" class="filter-control" placeholder="Search product...">
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
            
            <!-- Orders Container -->
            <div class="orders-container" id="orders-container">
                <!-- Orders will be populated by JavaScript -->
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
                        <label for="modal-order-id">Order ID</label>
                        <input type="text" id="modal-order-id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modal-order-status">Status</label>
                        <select id="modal-order-status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="ready-to-ship">Ready to Ship</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group" id="cancel-reason-group" style="display: none;">
                        <label for="cancel-reason">Cancellation Reason</label>
                        <textarea id="cancel-reason" class="form-control" rows="3" placeholder="Please provide a reason for cancellation..."></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-danger" id="cancel-status">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Order Details Modal -->
    <div class="modal" id="order-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Order Details - <span id="detail-order-id"></span></h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="order-info" style="margin-bottom: 20px;">
                    <div><strong>Order Date:</strong> <span id="detail-order-date"></span></div>
                    <div><strong>Customer:</strong> <span id="detail-customer-name"></span> (<span id="detail-customer-email"></span>)</div>
                    <div><strong>Status:</strong> <span id="detail-order-status"></span></div>
                </div>
                
                <h4 style="margin-bottom: 15px;">Your Products in This Order</h4>
                <div id="detail-products-list">
                    <!-- Products will be populated by JavaScript -->
                </div>
                
                <div class="vendor-total" style="margin-top: 15px;">
                    <div><strong>Your Total:</strong> $<span id="detail-vendor-total"></span></div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" id="close-order-details">Close</button>
                    <button type="button" class="btn btn-primary" id="update-order-from-details">Update Status</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../js/vendor-orders.js"></script>
</body>
</html>