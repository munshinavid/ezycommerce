<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discount Management - Vendor Dashboard</title>
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
            --active: #28a745;
            --inactive: #6c757d;
            --expired: #dc3545;
            --upcoming: #17a2b8;
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

        /* Stats Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-footer {
            font-size: 0.8rem;
            color: var(--gray);
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

        /* Discounts Grid */
        .discounts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .discount-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .discount-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .discount-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .discount-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .discount-type {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .discount-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-expired {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-upcoming {
            background-color: #cce7ff;
            color: #004085;
        }

        .discount-details {
            padding: 15px 20px;
        }

        .discount-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .discount-dates {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 0;
            border-top: 1px solid var(--light-gray);
            border-bottom: 1px solid var(--light-gray);
        }

        .date-item {
            text-align: center;
        }

        .date-label {
            font-size: 0.8rem;
            color: var(--gray);
            margin-bottom: 5px;
        }

        .date-value {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .discount-scope {
            margin-bottom: 15px;
        }

        .scope-label {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .scope-value {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .products-count {
            background-color: var(--light);
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .discount-actions {
            display: flex;
            gap: 8px;
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

        /* Products Selection */
        .products-selection {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            padding: 10px;
        }

        .product-checkbox {
            display: flex;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid var(--light-gray);
        }

        .product-checkbox:last-child {
            border-bottom: none;
        }

        .product-checkbox input {
            margin-right: 10px;
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

        .product-price {
            color: var(--gray);
            font-size: 0.8rem;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .discounts-grid {
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
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
            
            .discounts-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .discount-actions {
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
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-chart-line"></i> Sales Analytics</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-undo"></i> Returns</a></li>
                    <li class="active"><a href="v-discount.php"><i class="fas fa-tag"></i> Discounts</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="/ezycommerce/Vendor/logout.php" style="color: #e63946;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Discount Management</h1>
                <div class="header-actions">
                    <button class="btn btn-outline" id="export-discounts">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button class="btn btn-primary" id="add-discount">
                        <i class="fas fa-plus"></i> Create Discount
                    </button>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Active Discounts</div>
                        <div class="stat-icon" style="background: var(--active);">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="active-count">3</div>
                    <div class="stat-footer">Currently running promotions</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Upcoming Discounts</div>
                        <div class="stat-icon" style="background: var(--upcoming);">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="upcoming-count">2</div>
                    <div class="stat-footer">Scheduled for future</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Expired Discounts</div>
                        <div class="stat-icon" style="background: var(--expired);">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="expired-count">5</div>
                    <div class="stat-footer">Past promotions</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Total Products</div>
                        <div class="stat-icon" style="background: var(--primary);">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="total-products">24</div>
                    <div class="stat-footer">In your catalog</div>
                </div>
            </div>
            
            <!-- Tabs Navigation -->
            <div class="tabs">
                <div class="tab active" data-tab="active">
                    Active Discounts <span class="tab-badge" id="active-tab-count">3</span>
                </div>
                <div class="tab" data-tab="upcoming">
                    Upcoming <span class="tab-badge" id="upcoming-tab-count">2</span>
                </div>
                <div class="tab" data-tab="inactive">
                    Inactive <span class="tab-badge" id="inactive-tab-count">1</span>
                </div>
                <div class="tab" data-tab="expired">
                    Expired <span class="tab-badge" id="expired-tab-count">5</span>
                </div>
                <div class="tab" data-tab="all">
                    All Discounts <span class="tab-badge" id="all-tab-count">11</span>
                </div>
            </div>
            
            <!-- Discounts Grid -->
            <div class="discounts-grid" id="discounts-container">
                <!-- Discounts will be populated by JavaScript -->
            </div>
        </div>
    </div>
    
    <!-- Create/Edit Discount Modal -->
    <div class="modal" id="discount-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="discount-modal-title">Create New Discount</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="discount-form">
                    <div class="form-group">
                        <label for="discount-name">Discount Name *</label>
                        <input type="text" id="discount-name" class="form-control" placeholder="Summer Sale, Black Friday, etc." required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="discount-type">Discount Type *</label>
                            <select id="discount-type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount ($)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="discount-value">Discount Value *</label>
                            <input type="number" id="discount-value" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start-date">Start Date *</label>
                            <input type="datetime-local" id="start-date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="end-date">End Date *</label>
                            <input type="datetime-local" id="end-date" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="apply-to">Apply To *</label>
                        <select id="apply-to" class="form-control" required>
                            <option value="all">All Products</option>
                            <option value="selected">Selected Products</option>
                            <option value="categories">Product Categories</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="products-selection-group" style="display: none;">
                        <label>Select Products</label>
                        <div class="products-selection" id="products-selection">
                            <!-- Products will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <div class="form-group" id="categories-selection-group" style="display: none;">
                        <label>Select Categories</label>
                        <div class="products-selection" id="categories-selection">
                            <!-- Categories will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="discount-status">Status</label>
                        <select id="discount-status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-danger" id="cancel-discount">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Discount</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal" id="delete-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Delete</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the discount <strong id="delete-discount-name"></strong>? This action cannot be undone.</p>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="cancel-delete">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete Discount</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../js/vendor-discount.js"></script>
</body>
</html>