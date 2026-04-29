<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - E-Commerce Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 500;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .card-footer {
            font-size: 0.8rem;
            color: var(--gray);
        }

        /* Charts Section */
        .charts-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .chart-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        /* Tables Section */
        .tables-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
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

        .stock-low {
            background-color: #f8d7da;
            color: #721c24;
        }

        .stock-medium {
            background-color: #fff3cd;
            color: #856404;
        }

        .stock-high {
            background-color: #d4edda;
            color: #155724;
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

        /* Image Upload Styles */
        .image-upload-container {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #fafafa;
            transition: all 0.3s ease;
        }

        .image-upload-container.dragover {
            border-color: #4361ee;
            background: #f0f4ff;
        }

        .image-preview {
            margin-bottom: 15px;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 4px;
            display: none;
        }

        .image-preview i {
            font-size: 48px;
            margin-bottom: 10px;
            color: #adb5bd;
        }

        .upload-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .upload-hint {
            margin-top: 8px;
            text-align: center;
            color: #6c757d;
        }

        .file-input {
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .charts-container, .tables-container {
                grid-template-columns: 1fr;
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
            
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
                <i class="fas fa-store fa-2x"></i>
                <h2>Vendor Dashboard</h2>
            </div>
            
            <div class="vendor-info">
                <div class="vendor-name" id="vendor-name">TechGadgets Inc.</div>
                <div class="vendor-email" id="vendor-email">contact@techgadgets.com</div>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li class="active"><a href="<?php echo url('/vendor'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="<?php echo url('/vendor/products'); ?>" id="nav-products"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="<?php echo url('/vendor/orders'); ?>" id="nav-orders"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="#" id="nav-sales" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-chart-line"></i> Sales Analytics</a></li>
                    <li><a href="#" id="nav-returns" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-undo"></i> Returns</a></li>
                    <li><a href="<?php echo url('/vendor/discounts'); ?>" id="nav-discounts"><i class="fas fa-tag"></i> Discounts</a></li>
                    <li><a href="#" id="nav-profile" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="<?php echo url('/vendor/logout'); ?>" style="color: #e63946;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Vendor Dashboard</h1>
                <div class="header-actions">
                    <button class="btn btn-outline" id="refresh-data">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-primary" id="add-product">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
            </div>
            
            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Total Products</div>
                        <div class="card-icon" style="background: var(--primary);">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                    <div class="card-value" id="total-products">24</div>
                    <div class="card-footer">Active products in your catalog</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Pending Orders</div>
                        <div class="card-icon" style="background: var(--warning);">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="card-value" id="pending-orders">8</div>
                    <div class="card-footer">Orders awaiting processing</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Monthly Revenue</div>
                        <div class="card-icon" style="background: var(--success);">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="card-value" id="monthly-revenue">$4,250</div>
                    <div class="card-footer">+12% from last month</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Low Stock Items</div>
                        <div class="card-icon" style="background: var(--danger);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="card-value" id="low-stock">5</div>
                    <div class="card-footer">Products needing restock</div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="charts-container">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Sales Performance</h3>
                        <select id="sales-period" class="form-control" style="width: auto;">
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="sales-chart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Top Products</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="products-chart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Tables Section -->
            <div class="tables-container">
                <!-- Recent Orders Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Recent Orders</h2>
                        <button class="btn btn-outline" id="view-all-orders">
                            View All
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recent-orders-body">
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Low Stock Products Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Low Stock Products</h2>
                        <button class="btn btn-outline" id="manage-stock">
                            Manage Stock
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="low-stock-body">
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- All Products Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Your Products</h2>
                    <div class="table-actions">
                        <button class="btn btn-outline" id="export-products">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn btn-primary" id="refresh-products">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-body">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Product Modal -->
    <div class="modal" id="product-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="product-modal-title">Add New Product</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="product-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-name">Product Name</label>
                            <input type="text" id="product-name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="product-category">Category</label>
                            <select id="product-category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="electronics">Electronics</option>
                                <option value="clothing">Clothing</option>
                                <option value="home">Home & Garden</option>
                                <option value="sports">Sports & Outdoors</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product-description">Description</label>
                        <textarea id="product-description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-price">Price ($)</label>
                            <input type="number" id="product-price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="product-stock">Stock Quantity</label>
                            <input type="number" id="product-stock" class="form-control" min="0" required>
                        </div>
                    </div>
                    
                    <!-- Image Upload Section -->
                    <div class="form-group">
                        <label for="product-image">Product Image</label>
                        <div class="image-upload-container" id="image-upload-container">
                            <div class="image-preview" id="image-preview">
                                <i class="fas fa-image"></i>
                                <span>No image selected</span>
                            </div>
                            <div class="upload-actions">
                                <input type="file" id="product-image" class="file-input" accept="image/*" hidden>
                                <button type="button" class="btn btn-outline" id="browse-btn">
                                    <i class="fas fa-folder-open"></i> Browse
                                </button>
                                <button type="button" class="btn btn-danger" id="remove-image" style="display: none;">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                        <div class="upload-hint">
                            <small>Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-danger" id="cancel-product">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Stock Update Modal -->
    <div class="modal" id="stock-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Stock - <span id="stock-product-name"></span></h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="stock-form">
                    <div class="form-group">
                        <label for="current-stock">Current Stock</label>
                        <input type="number" id="current-stock" class="form-control" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock-action">Action</label>
                        <select id="stock-action" class="form-control">
                            <option value="add">Add Stock</option>
                            <option value="set">Set Stock</option>
                            <option value="deduct">Deduct Stock</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock-quantity">Quantity</label>
                        <input type="number" id="stock-quantity" class="form-control" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock-reason">Reason (Optional)</label>
                        <input type="text" id="stock-reason" class="form-control" placeholder="Restock, Sale, etc.">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-danger" id="cancel-stock">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="<?php echo url('/Vendor/js/vendor-dashboard.js'); ?>"></script>
</body>
</html>