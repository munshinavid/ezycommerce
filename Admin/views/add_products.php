<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="add_products.css">
</head>
<body>

    <div class="container">
        <h2>Add New Product</h2>
        <form action="../controllers/add_products.php" method="POST" enctype="multipart/form-data">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="price">Price ($):</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="stock">Stock Quantity:</label>
            <input type="number" id="stock" name="stock" required>

            <label for="image">Product Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <button type="submit">Add Product</button>
        </form>
    </div>

</body>
</html>
