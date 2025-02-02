<?php
include '../model/db.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $db = new Database();
    if ($db->deleteProduct($product_id)) {
        echo "Product deleted successfully.";
        header("Location: index.php");
    } else {
        echo "Failed to delete product.";
    }
}
?>
