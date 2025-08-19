
<?php echo "Hello World"; ?>
<!DOCTYPE html>
<!-- ...existing code... -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - E-Commerce Admin</title>
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

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }

        .stock-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .in-stock { background-color: #d4edda; color: #155724; }
        .low-stock { background-color: #fff3cd; color: #856404; }
        .out-of-stock { background-color: #f8d7da; color: #721c24; }

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
            width: 600px;
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

        /* Image Upload */
        .image-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
            cursor: pointer;
        }

        .image-upload:hover {
            border-color: var(--primary);
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
            <div class="menu-item active">
                <i class="fas fa-box"></i>
                <span>Products Management</span>
            </div>
            <div class="menu-item">
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
                <input type="text" placeholder="Search products...">
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
            <h1 class="page-title">Products Management</h1>
            <button class="btn btn-primary" id="addProductBtn">
                <i class="fas fa-plus"></i> Add New Product
            </button>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <label for="search">Search Products</label>
                <input type="text" id="search" placeholder="Search by name or description">
            </div>
            <div class="filter-group">
                <label for="categoryFilter">Filter by Category</label>
                <select id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="electronics">Electronics</option>
                    <option value="clothing">Clothing</option>
                    <option value="home">Home & Kitchen</option>
                    <option value="books">Books</option>
                    <option value="sports">Sports & Outdoors</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="stockFilter">Filter by Stock</label>
                <select id="stockFilter">
                    <option value="all">All Stock Status</option>
                    <option value="in-stock">In Stock</option>
                    <option value="low-stock">Low Stock</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>
            </div>
            <button class="btn btn-primary" style="align-self: flex-end;">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>

        <!-- Products Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>All Products</h3>
                <div>
                    <span>Showing 1-10 of 127 products</span>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Vendor</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#P1001</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Wireless Headphones">
                                <div>Wireless Headphones</div>
                            </div>
                        </td>
                        <td>Electronics</td>
                        <td>$129.99</td>
                        <td>56</td>
                        <td>TechGadgets Inc.</td>
                        <td><span class="stock-status in-stock">In Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#P1002</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Running Shoes">
                                <div>Running Shoes</div>
                            </div>
                        </td>
                        <td>Sports</td>
                        <td>$89.99</td>
                        <td>12</td>
                        <td>SportZone</td>
                        <td><span class="stock-status low-stock">Low Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#P1003</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Smart Watch">
                                <div>Smart Watch</div>
                            </div>
                        </td>
                        <td>Electronics</td>
                        <td>$199.99</td>
                        <td>0</td>
                        <td>TechGadgets Inc.</td>
                        <td><span class="stock-status out-of-stock">Out of Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#P1004</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Cotton T-Shirt">
                                <div>Cotton T-Shirt</div>
                            </div>
                        </td>
                        <td>Clothing</td>
                        <td>$24.99</td>
                        <td>98</td>
                        <td>FashionHub</td>
                        <td><span class="stock-status in-stock">In Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#P1005</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Coffee Maker">
                                <div>Coffee Maker</div>
                            </div>
                        </td>
                        <td>Home & Kitchen</td>
                        <td>$79.99</td>
                        <td>23</td>
                        <td>HomeEssentials</td>
                        <td><span class="stock-status in-stock">In Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#P1006</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Bluetooth Speaker">
                                <div>Bluetooth Speaker</div>
                            </div>
                        </td>
                        <td>Electronics</td>
                        <td>$59.99</td>
                        <td>7</td>
                        <td>AudioTech</td>
                        <td><span class="stock-status low-stock">Low Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#P1007</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Yoga Mat">
                                <div>Yoga Mat</div>
                            </div>
                        </td>
                        <td>Sports</td>
                        <td>$29.99</td>
                        <td>45</td>
                        <td>SportZone</td>
                        <td><span class="stock-status in-stock">In Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#P1008</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Desk Lamp">
                                <div>Desk Lamp</div>
                            </div>
                        </td>
                        <td>Home & Kitchen</td>
                        <td>$39.99</td>
                        <td>0</td>
                        <td>HomeEssentials</td>
                        <td><span class="stock-status out-of-stock">Out of Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#P1009</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Backpack">
                                <div>Backpack</div>
                            </div>
                        </td>
                        <td>Accessories</td>
                        <td>$49.99</td>
                        <td>31</td>
                        <td>TravelGear</td>
                        <td><span class="stock-status in-stock">In Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#P1010</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://via.placeholder.com/50" class="product-image" alt="Wireless Earbuds">
                                <div>Wireless Earbuds</div>
                            </div>
                        </td>
                        <td>Electronics</td>
                        <td>$89.99</td>
                        <td>17</td>
                        <td>AudioTech</td>
                        <td><span class="stock-status in-stock">In Stock</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
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

    <!-- Add Product Modal -->
    <div class="modal" id="addProductModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Product</h3>
                <button class="close-btn">&times;</button>
            </div>
            <form id="addProductForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" id="productName" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory">Category</label>
                        <select id="productCategory" required>
                            <option value="">Select Category</option>
                            <option value="electronics">Electronics</option>
                            <option value="clothing">Clothing</option>
                            <option value="home">Home & Kitchen</option>
                            <option value="sports">Sports & Outdoors</option>
                            <option value="books">Books</option>
                            <option value="accessories">Accessories</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="productDescription">Description</label>
                    <textarea id="productDescription" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="productPrice">Price ($)</label>
                        <input type="number" id="productPrice" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="productStock">Stock Quantity</label>
                        <input type="number" id="productStock" min="0" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="productVendor">Vendor</label>
                        <select id="productVendor" required>
                            <option value="">Select Vendor</option>
                            <option value="1">TechGadgets Inc.</option>
                            <option value="2">FashionHub</option>
                            <option value="3">HomeEssentials</option>
                            <option value="4">SportZone</option>
                            <option value="5">AudioTech</option>
                            <option value="6">TravelGear</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productDiscount">Discount</label>
                        <select id="productDiscount">
                            <option value="">No Discount</option>
                            <option value="1">Summer Sale (15% off)</option>
                            <option value="2">Clearance (25% off)</option>
                            <option value="3">New Customer (10% off)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Product Image</label>
                    <div class="image-upload">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 24px; margin-bottom: 10px;"></i>
                        <p>Click to upload or drag and drop</p>
                        <p style="font-size: 12px; color: #6c757d;">SVG, PNG, JPG or GIF (max. 5MB)</p>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-danger">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        const addProductBtn = document.getElementById('addProductBtn');
        const addProductModal = document.getElementById('addProductModal');
        const closeBtn = document.querySelector('.close-btn');
        const cancelBtn = document.querySelector('.btn-danger');

        addProductBtn.addEventListener('click', () => {
            addProductModal.style.display = 'flex';
        });

        closeBtn.addEventListener('click', () => {
            addProductModal.style.display = 'none';
        });

        cancelBtn.addEventListener('click', () => {
            addProductModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === addProductModal) {
                addProductModal.style.display = 'none';
            }
        });

        // Form submission
        document.getElementById('addProductForm').addEventListener('submit', (e) => {
            e.preventDefault();
            // In a real application, you would handle form submission to the server here
            alert('Product added successfully!');
            addProductModal.style.display = 'none';
        });

        // Filter functionality
        document.getElementById('search').addEventListener('input', (e) => {
            const searchValue = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const productName = row.children[1].textContent.toLowerCase();
                
                if (productName.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            const categoryValue = e.target.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const category = row.children[2].textContent.toLowerCase();
                
                if (categoryValue === 'all' || category.includes(categoryValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.getElementById('stockFilter').addEventListener('change', (e) => {
            const stockValue = e.target.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const status = row.children[6].textContent.toLowerCase().replace(' ', '-');
                
                if (stockValue === 'all' || status.includes(stockValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>