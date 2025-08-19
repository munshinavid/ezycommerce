<?php echo "Hello World"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discount Management - E-Commerce Admin</title>
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

        .discount-type {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .type-percentage { background-color: #d4edda; color: #155724; }
        .type-fixed { background-color: #cce5ff; color: #004085; }

        .discount-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active { background-color: #d4edda; color: #155724; }
        .status-upcoming { background-color: #fff3cd; color: #856404; }
        .status-expired { background-color: #f8d7da; color: #721c24; }

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

        /* Product Selector */
        .product-selector {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
        }

        .product-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding: 8px;
            border-radius: 4px;
        }

        .product-checkbox:hover {
            background-color: #f8f9fa;
        }

        .product-checkbox input {
            margin-right: 10px;
        }

        /* Products Modal */
        .products-modal .modal-content {
            width: 800px;
        }

        .product-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 15px;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .product-category {
            color: #6c757d;
            font-size: 14px;
        }

        .product-price {
            font-weight: 600;
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
            <div class="menu-item">
                <i class="fas fa-box"></i>
                <span>Products Management</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders Management</span>
            </div>
            <div class="menu-item active">
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
                <input type="text" placeholder="Search discounts...">
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
            <h1 class="page-title">Discounts Management</h1>
            <button class="btn btn-primary" id="addDiscountBtn">
                <i class="fas fa-plus"></i> Add New Discount
            </button>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <label for="search">Search Discounts</label>
                <input type="text" id="search" placeholder="Search by type or value">
            </div>
            <div class="filter-group">
                <label for="statusFilter">Filter by Status</label>
                <select id="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="typeFilter">Filter by Type</label>
                <select id="typeFilter">
                    <option value="all">All Types</option>
                    <option value="percentage">Percentage</option>
                    <option value="fixed">Fixed Amount</option>
                </select>
            </div>
            <button class="btn btn-primary" style="align-self: flex-end;">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>

        <!-- Discounts Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>All Discounts</h3>
                <div>
                    <span>Showing 1-5 of 5 discounts</span>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Discount ID</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Products Applied</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#D1001</td>
                        <td><span class="discount-type type-percentage">Percentage</span></td>
                        <td>15%</td>
                        <td>Jun 1, 2023</td>
                        <td>Jun 30, 2023</td>
                        <td>12 products</td>
                        <td><span class="discount-status status-active">Active</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-products-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#D1002</td>
                        <td><span class="discount-type type-fixed">Fixed</span></td>
                        <td>$20.00</td>
                        <td>Jul 1, 2023</td>
                        <td>Jul 15, 2023</td>
                        <td>8 products</td>
                        <td><span class="discount-status status-upcoming">Upcoming</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-products-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#D1003</td>
                        <td><span class="discount-type type-percentage">Percentage</span></td>
                        <td>25%</td>
                        <td>May 15, 2023</td>
                        <td>May 31, 2023</td>
                        <td>5 products</td>
                        <td><span class="discount-status status-expired">Expired</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-products-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#D1004</td>
                        <td><span class="discount-type type-percentage">Percentage</span></td>
                        <td>10%</td>
                        <td>Jun 10, 2023</td>
                        <td>Jun 20, 2023</td>
                        <td>All products</td>
                        <td><span class="discount-status status-expired">Expired</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-products-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#D1005</td>
                        <td><span class="discount-type type-fixed">Fixed</span></td>
                        <td>$50.00</td>
                        <td>Jun 25, 2023</td>
                        <td>Jul 10, 2023</td>
                        <td>3 products</td>
                        <td><span class="discount-status status-active">Active</span></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm view-products-btn"><i class="fas fa-eye"></i> View</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <button>&laquo;</button>
                <button class="active">1</button>
                <button>&raquo;</button>
            </div>
        </div>
    </div>

    <!-- Add Discount Modal -->
    <div class="modal" id="addDiscountModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Discount</h3>
                <button class="close-btn">&times;</button>
            </div>
            <form id="addDiscountForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="discountType">Discount Type</label>
                        <select id="discountType" required>
                            <option value="">Select Type</option>
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="discountValue" id="valueLabel">Value</label>
                        <input type="number" id="discountValue" min="0" step="0.01" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="startDate">Start Date</label>
                        <input type="date" id="startDate" required>
                    </div>
                    <div class="form-group">
                        <label for="endDate">End Date</label>
                        <input type="date" id="endDate" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Apply to Products</label>
                    <select id="applyTo">
                        <option value="all">All Products</option>
                        <option value="selected">Selected Products</option>
                    </select>
                    
                    <div class="product-selector" id="productSelector" style="display: none;">
                        <div class="product-checkbox">
                            <input type="checkbox" id="product1">
                            <label for="product1">Wireless Headphones</label>
                        </div>
                        <div class="product-checkbox">
                            <input type="checkbox" id="product2">
                            <label for="product2">Running Shoes</label>
                        </div>
                        <div class="product-checkbox">
                            <input type="checkbox" id="product3">
                            <label for="product3">Smart Watch</label>
                        </div>
                        <div class="product-checkbox">
                            <input type="checkbox" id="product4">
                            <label for="product4">Cotton T-Shirt</label>
                        </div>
                        <div class="product-checkbox">
                            <input type="checkbox" id="product5">
                            <label for="product5">Coffee Maker</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-danger">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Discount</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Products Modal -->
    <div class="modal products-modal" id="viewProductsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Products with Discount #D1001</h3>
                <button class="close-btn">&times;</button>
            </div>
            
            <div class="product-list">
                <div class="product-item">
                    <img src="https://via.placeholder.com/50" alt="Wireless Headphones">
                    <div class="product-info">
                        <div class="product-name">Wireless Headphones</div>
                        <div class="product-category">Electronics</div>
                    </div>
                    <div class="product-price">$129.99</div>
                </div>
                
                <div class="product-item">
                    <img src="https://via.placeholder.com/50" alt="Running Shoes">
                    <div class="product-info">
                        <div class="product-name">Running Shoes</div>
                        <div class="product-category">Sports</div>
                    </div>
                    <div class="product-price">$89.99</div>
                </div>
                
                <div class="product-item">
                    <img src="https://via.placeholder.com/50" alt="Smart Watch">
                    <div class="product-info">
                        <div class="product-name">Smart Watch</div>
                        <div class="product-category">Electronics</div>
                    </div>
                    <div class="product-price">$199.99</div>
                </div>
                
                <div class="product-item">
                    <img src="https://via.placeholder.com/50" alt="Cotton T-Shirt">
                    <div class="product-info">
                        <div class="product-name">Cotton T-Shirt</div>
                        <div class="product-category">Clothing</div>
                    </div>
                    <div class="product-price">$24.99</div>
                </div>
                
                <div class="product-item">
                    <img src="https://via.placeholder.com/50" alt="Coffee Maker">
                    <div class="product-info">
                        <div class="product-name">Coffee Maker</div>
                        <div class="product-category">Home & Kitchen</div>
                    </div>
                    <div class="product-price">$79.99</div>
                </div>
            </div>
            
            <div class="form-actions">
                <button class="btn btn-primary">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const addDiscountBtn = document.getElementById('addDiscountBtn');
        const addDiscountModal = document.getElementById('addDiscountModal');
        const viewProductsModal = document.getElementById('viewProductsModal');
        const viewProductsBtns = document.querySelectorAll('.view-products-btn');
        const closeBtns = document.querySelectorAll('.close-btn');
        const cancelBtn = document.querySelector('.btn-danger');

        addDiscountBtn.addEventListener('click', () => {
            addDiscountModal.style.display = 'flex';
        });

        viewProductsBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                viewProductsModal.style.display = 'flex';
            });
        });

        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                addDiscountModal.style.display = 'none';
                viewProductsModal.style.display = 'none';
            });
        });

        cancelBtn.addEventListener('click', () => {
            addDiscountModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === addDiscountModal || e.target === viewProductsModal) {
                addDiscountModal.style.display = 'none';
                viewProductsModal.style.display = 'none';
            }
        });

        // Form submission
        document.getElementById('addDiscountForm').addEventListener('submit', (e) => {
            e.preventDefault();
            // In a real application, you would handle form submission to the server here
            alert('Discount added successfully!');
            addDiscountModal.style.display = 'none';
        });

        // Dynamic form behavior
        const discountType = document.getElementById('discountType');
        const valueLabel = document.getElementById('valueLabel');
        const discountValue = document.getElementById('discountValue');

        discountType.addEventListener('change', () => {
            if (discountType.value === 'percentage') {
                valueLabel.textContent = 'Percentage';
                discountValue.placeholder = 'Enter percentage';
                discountValue.max = 100;
            } else if (discountType.value === 'fixed') {
                valueLabel.textContent = 'Amount';
                discountValue.placeholder = 'Enter amount';
                discountValue.removeAttribute('max');
            }
        });

        // Product selector toggle
        const applyTo = document.getElementById('applyTo');
        const productSelector = document.getElementById('productSelector');

        applyTo.addEventListener('change', () => {
            if (applyTo.value === 'selected') {
                productSelector.style.display = 'block';
            } else {
                productSelector.style.display = 'none';
            }
        });

        // Filter functionality
        document.getElementById('search').addEventListener('input', (e) => {
            const searchValue = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const type = row.children[1].textContent.toLowerCase();
                const value = row.children[2].textContent.toLowerCase();
                
                if (type.includes(searchValue) || value.includes(searchValue)) {
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
                const status = row.children[6].textContent.toLowerCase();
                
                if (statusValue === 'all' || status.includes(statusValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.getElementById('typeFilter').addEventListener('change', (e) => {
            const typeValue = e.target.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const type = row.children[1].textContent.toLowerCase();
                
                if (typeValue === 'all' || type.includes(typeValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>