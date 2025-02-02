<?php
include '../model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $category = $_POST["category"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];

    // Define upload directory
    $upload_dir = "../../Customer/images/";

    // Ensure the directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Handling Image Upload
    $image_name = basename($_FILES["image"]["name"]);
    $image_tmp = $_FILES["image"]["tmp_name"];
    $image_path = $upload_dir . $image_name;

    if (move_uploaded_file($image_tmp, $image_path)) {
        // Instantiate controller and add product
        $db = new Database;
        if ($db->addProduct($name, $category, $description, $price, $stock, $image_path)) {
            echo "Product added successfully!";
            include '../views/dash.php';
        } else {
            echo "Error adding product!";
        }
    } else {
        echo "Failed to upload image!";
    }
}
?>
