<?php echo "Hello World"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - E-Commerce Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('/Admin/css/product_management.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('/Admin/css/sidebar.css'); ?>">
</head>
<body>
    <!-- Sidebar -->
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="search" placeholder="Search products...">
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
        </div>

        <!-- Products Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>All Products</h3>
                <div>
                    <span id="productsCount">Showing 1-10 of 0 products</span>
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
                <tbody id="productsTableBody">
                    <!-- Products populated by AJAX -->
                </tbody>
            </table>

            <!-- Pagination (optional, can be dynamic later) -->
            <div class="pagination">
                <button>&laquo;</button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button>&raquo;</button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="addProductModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Product</h3>
                <button class="close-btn">&times;</button>
            </div>
            <form id="addProductForm" enctype="multipart/form-data">
                <input type="hidden" id="productId" name="product_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" id="productName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory">Category</label>
                        <select id="productCategory" name="category_id" required>
                            <option value="">Select Category</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="productDescription">Description</label>
                    <textarea id="productDescription" name="description" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="productPrice">Price ($)</label>
                        <input type="number" id="productPrice" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="productStock">Stock Quantity</label>
                        <input type="number" id="productStock" name="stock" min="0" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="productVendor">Vendor</label>
                        <select id="productVendor" name="vendor_id" required>
                            <option value="">Select Vendor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productDiscount">Discount</label>
                        <select id="productDiscount" name="discount_id">
                            <option value="">No Discount</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Product Image</label>
                    <div class="image-upload" onclick="document.getElementById('productImage').click();">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 24px; margin-bottom: 10px;"></i>
                        <p>Click to upload or drag and drop</p>
                        <p style="font-size: 12px; color: #6c757d;">SVG, PNG, JPG or GIF (max. 5MB)</p>
                        <input type="file" id="productImage" name="image" accept="image/*" style="display:none;">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" id="cancelProductBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitProductBtn">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo url('/Admin/js/product_management.js'); ?>"></script>
</body>
</html>
