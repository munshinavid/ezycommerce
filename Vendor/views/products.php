<?php
// Debug: Verify url() function is accessible and working
if (!function_exists('url')) {
    die('ERROR: url() function not found. This file must be included via the front controller in public/index.php');
}
// Test the url() function output
$_DEBUG_URLS = [
    '/vendor' => url('/vendor'),
    '/vendor/products' => url('/vendor/products'),
    '/vendor/orders' => url('/vendor/orders'),
    '/vendor/discounts' => url('/vendor/discounts'),
    '/vendor/logout' => url('/vendor/logout'),
];
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - Vendor Dashboard</title>
    <!-- DEBUG: Generated URLs: <?php echo htmlspecialchars(json_encode($_DEBUG_URLS)); ?> -->
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

        /* Bulk Actions */
        .bulk-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background-color: var(--light);
            border-bottom: 1px solid var(--light-gray);
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .bulk-checkbox {
            margin-right: 5px;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
            border-bottom: 1px solid var(--light-gray);
        }

        .product-info {
            padding: 15px;
        }

        .product-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .product-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .product-sku {
            color: var(--gray);
            font-size: 0.8rem;
        }

        .product-price {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary);
        }

        .product-category {
            display: inline-block;
            background: var(--light);
            color: var(--dark);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .product-description {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 0;
            border-top: 1px solid var(--light-gray);
            border-bottom: 1px solid var(--light-gray);
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--gray);
        }

        .product-actions {
            display: flex;
            gap: 8px;
        }

        /* Products Table */
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

        .view-toggle {
            display: flex;
            background: var(--light);
            border-radius: 5px;
            padding: 5px;
            margin-right: 10px;
        }

        .view-btn {
            padding: 5px 10px;
            border: none;
            background: none;
            cursor: pointer;
            border-radius: 3px;
            color: var(--gray);
        }

        .view-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
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
            max-width: 700px;
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

        /* Image Upload */
        .image-upload {
            border: 2px dashed var(--light-gray);
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-upload:hover {
            border-color: var(--primary);
        }

        .image-upload i {
            font-size: 2rem;
            color: var(--gray);
            margin-bottom: 10px;
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .filters-content {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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
            
            .products-grid {
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
                <i class="fas fa-store fa-2x"></i>
                <h2>Vendor Dashboard</h2>
            </div>
            
            <div class="vendor-info">
                <div class="vendor-name" id="vendor-name">TechGadgets Inc.</div>
                <div class="vendor-email" id="vendor-email">contact@techgadgets.com</div>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li><a href="<?php echo url('/vendor'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="active"><a href="<?php echo url('/vendor/products'); ?>"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="<?php echo url('/vendor/orders'); ?>"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-chart-line"></i> Sales Analytics</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-undo"></i> Returns</a></li>
                    <li><a href="<?php echo url('/vendor/discounts'); ?>"><i class="fas fa-tag"></i> Discounts</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="<?php echo url('/vendor/logout'); ?>" style="color: #e63946;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Products Management</h1>
                <div class="header-actions">
                    <button class="btn btn-outline" id="export-products">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button class="btn btn-primary" id="add-product">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
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
                        <label for="category-filter">Category</label>
                        <select id="category-filter" class="filter-control">
                            <option value="all">All Categories</option>
                            <option value="electronics">Electronics</option>
                            <option value="clothing">Clothing</option>
                            <option value="home">Home & Garden</option>
                            <option value="sports">Sports & Outdoors</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status-filter">Status</label>
                        <select id="status-filter" class="filter-control">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="stock-filter">Stock Level</label>
                        <select id="stock-filter" class="filter-control">
                            <option value="all">All Stock Levels</option>
                            <option value="low">Low Stock (< 10)</option>
                            <option value="medium">Medium Stock (10-50)</option>
                            <option value="high">High Stock (> 50)</option>
                            <option value="out">Out of Stock</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="search-products">Search Products</label>
                        <input type="text" id="search-products" class="filter-control" placeholder="Product name, SKU...">
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
                <span id="selected-count">0 products selected</span>
                <select id="bulk-action" class="filter-control" style="width: auto;">
                    <option value="">Bulk Actions</option>
                    <option value="activate">Activate</option>
                    <option value="deactivate">Deactivate</option>
                    <option value="delete">Delete</option>
                    <option value="add-stock">Add Stock</option>
                </select>
                <button class="btn btn-primary btn-sm" id="apply-bulk-action">Apply</button>
            </div>
            
            <!-- View Toggle and Table Header -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Your Products</h2>
                    <div class="table-actions">
                        <div class="view-toggle">
                            <button class="view-btn active" id="grid-view">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-btn" id="list-view">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                        <button class="btn btn-outline" id="refresh-products">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                
                <!-- Products Grid View -->
                <div class="products-grid" id="products-grid">
                    <!-- Products will be populated by JavaScript -->
                </div>
                
                <!-- Products Table View -->
                <table id="products-table" style="display: none;">
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" id="select-all-table"></th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Sales</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body">
                        <!-- Products will be populated by JavaScript -->
                    </tbody>
                </table>
                
                <div class="pagination">
                    <div class="pagination-info" id="pagination-info">Showing 1-6 of 24 products</div>
                    <div class="pagination-controls">
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <button class="page-btn">4</button>
                        <button class="page-btn">Next</button>
                    </div>
                </div>
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
                            <label for="product-name">Product Name *</label>
                            <input type="text" id="product-name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="product-sku">SKU *</label>
                            <input type="text" id="product-sku" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-category">Category *</label>
                            <select id="product-category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="electronics">Electronics</option>
                                <option value="clothing">Clothing</option>
                                <option value="home">Home & Garden</option>
                                <option value="sports">Sports & Outdoors</option>
                                <option value="books">Books & Media</option>
                                <option value="beauty">Beauty & Personal Care</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product-price">Price ($) *</label>
                            <input type="number" id="product-price" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-stock">Stock Quantity *</label>
                            <input type="number" id="product-stock" class="form-control" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="product-status">Status</label>
                            <select id="product-status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product-description">Description</label>
                        <textarea id="product-description" class="form-control" rows="4" placeholder="Describe your product..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="product-image">Product Image</label>
                        <div class="image-upload" id="image-upload">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload product image</p>
                            <input type="file" id="product-image" accept="image/*" style="display: none;">
                            <img id="image-preview" class="image-preview" alt="Image preview">
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
    
    <!-- Delete Confirmation Modal -->
    <div class="modal" id="delete-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Delete</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delete-product-name"></strong>? This action cannot be undone.</p>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="cancel-delete">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- view product modal -->
     <div class="modal" id="view-product-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="view-product-modal-title">Product Details</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body" id="view-product-modal-body">
                <!-- Product details will be populated by JavaScript -->
            </div>
        </div>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="<?php echo url('/Vendor/js/vendor-products.js'); ?>"></script>
</body>
</html>