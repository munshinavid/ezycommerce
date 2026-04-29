<?php echo "Hello World"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discount Management - E-Commerce Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('/Admin/css/discounts_manage.css'); ?>">
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
                <input type="text" placeholder="Search discounts..." id="searchInput">
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
                <input type="text" id="search" placeholder="Search by name">
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
            <button class="btn btn-primary" id="applyFilters" style="align-self: flex-end;">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>

        <!-- Discounts Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>All Discounts</h3>
                <div>
                    <span id="discountCount">Loading discounts...</span>
                </div>
            </div>
            <div class="table-wrapper">
                <table id="discountsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Discount Name</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Products Applied</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="discountsTableBody">
                        <!-- Discounts will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination" id="pagination">
                <!-- Pagination will be generated here via AJAX -->
            </div>
        </div>
    </div>

    <!-- Add/Edit Discount Modal -->
    <div class="modal" id="discountModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Discount</h3>
                <button class="close-btn">&times;</button>
            </div>
            <form id="discountForm">
                <input type="hidden" id="discountId">
                
                <div class="form-group">
                    <label for="discountName">Discount Name <span style="color: red;">*</span></label>
                    <input type="text" id="discountName" placeholder="e.g., Black Friday Sale, Summer Discount" required>
                    <small style="color: #666;">This is for admin reference only - customers won't see this</small>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="discountType">Discount Type <span style="color: red;">*</span></label>
                        <select id="discountType" required>
                            <option value="">Select Type</option>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="discountValue" id="valueLabel">Value <span style="color: red;">*</span></label>
                        <input type="number" id="discountValue" min="0" step="0.01" placeholder="Enter value" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="startDate">Start Date <span style="color: red;">*</span></label>
                        <input type="datetime-local" id="startDate" required>
                    </div>
                    <div class="form-group">
                        <label for="endDate">End Date <span style="color: red;">*</span></label>
                        <input type="datetime-local" id="endDate" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="applyTo">Apply Discount To <span style="color: red;">*</span></label>
                    <select id="applyTo" required>
                        <option value="all">All Products (Apply to entire store)</option>
                        <option value="selected">Selected Products Only</option>
                        <option value="categories">Product Categories</option>
                    </select>
                    <small style="color: #666;">Choose how this discount will be applied automatically</small>
                </div>
                
                <!-- Product Selector (shows when "Selected Products" is chosen) -->
                <div class="product-selector" id="productSelector" style="display: none;">
                    <label>Select Products</label>
                    <div class="product-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="productSearch" placeholder="Search products...">
                    </div>
                    <div id="productsList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px; margin-top: 10px;">
                        <!-- Products will be loaded here via AJAX -->
                    </div>
                </div>

                <!-- Category Selector (shows when "Categories" is chosen) -->
                <div class="product-selector" id="categorySelector" style="display: none;">
                    <label>Select Categories</label>
                    <div id="categoriesList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px; margin-top: 10px;">
                        <!-- Categories will be loaded here via AJAX -->
                    </div>
                    <small style="color: #666; display: block; margin-top: 5px;">All products in selected categories will get this discount automatically</small>
                </div>
                
                <div class="form-actions" style="margin-top: 20px;">
                    <button type="button" class="btn btn-danger" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Discount</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Products Modal -->
    <div class="modal products-modal" id="viewProductsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="viewProductsTitle">Products with Discount</h3>
                <button class="close-btn">&times;</button>
            </div>
            
            <div class="product-list" id="viewProductsList">
                <!-- Products will be loaded here via AJAX -->
            </div>
            
            <div class="form-actions">
                <button class="btn btn-primary" id="closeViewProductsBtn">Close</button>
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
    <script src="<?php echo url('/Admin/js/discounts_manage.js'); ?>"></script>
</body>
</html>