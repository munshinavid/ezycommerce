<?php
require_once '../models/ProductModel.php'; // Ensure the path is correct

try {
    // Initialize the ProductModel
    $productModel = new ProductModel();

    // Fetch products (adjust limit and offset if needed)
    $products = $productModel->getPaginatedProducts(10, 0);

    // Check if products are fetched successfully
    if (!empty($products)) {
        echo "<h3>Products fetched successfully:</h3>";
        echo "<pre>";
        print_r($products); // Display the fetched products
        echo "</pre>";
    } else {
        echo "<p>No products found in the database.</p>";
    }
} catch (Exception $e) {
    // Catch any errors and display the message
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
