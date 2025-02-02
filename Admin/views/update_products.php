<?php
include '../model/db.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $db = new Database();
    $product = $db->getProductById($product_id);
    echo "CALLING";
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image_url = $_POST['image_url'];
    $discount_id = $_POST['discount_id'];

    if ($db->updateProduct($product_id, $name, $category, $description, $price, $stock, $image_url, $discount_id)) {
        echo "Product updated successfully.";
    } else {
        echo "Failed to update product.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link rel="stylesheet" href="update_products.css">
</head>
<body>

    <h1>Update Product</h1>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= $product['name'] ?>" required><br>
        <label>Category:</label>
        <input type="text" name="category" value="<?= $product['category'] ?>" required><br>
        <label>Description:</label>
        <textarea name="description"><?= $product['description'] ?></textarea><br>
        <label>Price:</label>
        <input type="number" name="price" value="<?= $product['price'] ?>" required><br>
        <label>Stock:</label>
        <input type="number" name="stock" value="<?= $product['stock'] ?>" required><br>
        <label>Image URL:</label>
        <input type="text" name="image_url" value="<?= $product['image_url'] ?>"><br>
        <label>Discount ID:</label>
        <input type="text" name="discount_id" value="<?= $product['discount_id'] ?>"><br><br>
        <button type="submit" name="submit">Update Product</button>
    </form>

</body>
</html>
