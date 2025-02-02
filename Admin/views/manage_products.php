<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="manage_products.css">
</head>
<body>

    <header>
        <h1>Manage Products</h1>
    </header>

    <main>
        <div class="product-list">
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../model/db.php';
                    $db = new Database();
                    $products = $db->getAllProducts(); // Fetch products from the database
                    foreach ($products as $product) {
                        echo "
                            <tr>
                                <td>{$product['product_id']}</td>
                                <td>{$product['name']}</td>
                                <td>{$product['category']}</td>
                                <td>\${$product['price']}</td>
                                <td>{$product['stock']}</td>
                                <td>
                                    <a href='/ezycommerce/Admin/views/update_products.php?id={$product['product_id']}'>Update</a> |
                                    <a href='/ezycommerce/Admin/controllers/delete_products.php?id={$product['product_id']}'>Delete</a>
                                </td>
                            </tr>
                        ";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
