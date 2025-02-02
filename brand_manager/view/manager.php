<?php
session_start();

include_once '../control/Logincontroller.php'; // Ensure this file exists

// Debugging: Check if the class is available
if (!class_exists('LoginController')) {
    die("LoginController class not found!");
}

// Handle logout action
// if (isset($_GET['action']) && $_GET['action'] === 'logout') {
//     // Destroy the session
//     session_destroy();
    
//     // Redirect to the login page
//     header("Location: login.php");
//     exit;
// }

// Session validation to ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Instantiate LoginController if needed
$loginController = new LoginController();

// Manager Page Content
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="../css/addproduct.css">
    <script src="../js/toggleSection.js" defer></script> <!-- External JS -->
</head>
<body>
    <nav>
        <label class="home-link">Manager Dashboard</label>
        <input type="checkbox" id="sidebar-active">
        <label for="sidebar-active" class="menu-button">&#9776;</label>
        <div class="links-container">
            <a href="../control/manager.php?action=view_users">View Users</a>
            <a href="../control/manager.php?action=show_all_products">View All Products</a>
            <a href="#search-product" onclick="toggleSection('search-product');">Search Product</a>
            <a href="#add-product" onclick="toggleSection('add-product');">Add Product</a>
            <a href="#delete-product" onclick="toggleSection('delete-product');">Delete Product</a>
            <a href="../control/manager.php?action=logout">Logout</a>
        </div>
    </nav>

    <h1>Welcome, <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'Manager'; ?>!</h1>

    <!-- Search Product -->
    <div id="search-product" style="display: none;">
        <form method="POST" action="../control/manager.php?action=search_product">
            <h2>Search Product</h2>
            <label>Enter Product Name or ID:</label>
            <input type="text" name="search" required>
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Add Product -->
    <div id="add-product" class="toggle-section" style="display: none;">
        <form method="POST" action="../control/manager.php?action=add_product" enctype="multipart/form-data">
            <input type="text" name="product_id" placeholder="Product ID" required>
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="text" name="price" placeholder="Price" required>
            <input type="number" name="stock" placeholder="Stock" required>
            <input type="file" name="fileToUpload" required>
            <button type="submit" name="add_product">Add Product</button>
        </form>
    </div>

    <!-- Update Product -->
    <div id="update-product" style="display: none;">
        <form method="POST" action="../control/manager.php?action=update_product">
            <h2>Update Product</h2>
            <label>Product ID:</label>
            <input type="number" name="product_id" required><br>
            <label>Product Name:</label>
            <input type="text" name="name" required><br>
            <label>Price:</label>
            <input type="number" name="price" required><br>
            <label>Stock:</label>
            <input type="number" name="stock" required><br>
            <label>Discount ID (optional):</label>
            <input type="number" name="discount_id"><br>
            <label>Start Date (optional):</label>
            <input type="date" name="start_date"><br>
            <label>End Date (optional):</label>
            <input type="date" name="end_date"><br>
            <button type="submit" name="update_product">Update Product</button>
        </form>
    </div>

    <!-- Delete Product -->
    <div id="delete-product" class="toggle-section" style="display: none;">
        <form method="POST" action="../control/manager.php?action=delete_product">
            <h2>Delete Product</h2>
            <label for="product_id">Enter Product ID:</label>
            <input type="number" id="product_id" name="product_id" required>
            <button type="submit" name="delete_product">Delete</button>
        </form>
    </div>
</body>
</html>
