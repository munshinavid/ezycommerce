<?php

class Database {
    private $conn;

    // Database connection
    public function __construct($host = "localhost", $user = "root", $password = "", $database = "project_db") {
        $this->conn = new mysqli($host, $user, $password, $database);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Function to add a product
    public function addProduct($name, $category, $description, $price, $stock, $image_url, $discount_id = NULL) {
        $stmt = $this->conn->prepare("INSERT INTO Products (name, category, description, price, stock, image_url, discount_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdisi", $name, $category, $description, $price, $stock, $image_url, $discount_id);
        return $stmt->execute();
    }

    // Function to update product details
    public function updateProduct($product_id, $name, $category, $description, $price, $stock, $image_url, $discount_id = NULL) {
        $stmt = $this->conn->prepare("UPDATE Products SET name=?, category=?, description=?, price=?, stock=?, image_url=?, discount_id=? WHERE product_id=?");
        $stmt->bind_param("sssdisii", $name, $category, $description, $price, $stock, $image_url, $discount_id, $product_id);
        return $stmt->execute();
    }
    // Function to get a single product by ID
    public function getProductById($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM Products WHERE product_id=?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    // Function to get all products
    public function getAllProducts() {
        $query = "SELECT * FROM Products";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Function to delete a product
    public function deleteProduct($product_id) {
        $stmt = $this->conn->prepare("DELETE FROM Products WHERE product_id=?");
        $stmt->bind_param("i", $product_id);
        return $stmt->execute();
    }

    // Function to get all pending user requests
    public function getPendingUsers() {
        $query = "SELECT * FROM Users WHERE role='Customer'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Function to approve a user
    public function approveUser($user_id, $role) {
        $stmt = $this->conn->prepare("UPDATE Users SET role=? WHERE user_id=?");
        $stmt->bind_param("si", $role, $user_id);
        return $stmt->execute();
    }

    // Function to get all orders
    public function getAllOrders() {
        $query = "SELECT Orders.*, Users.username FROM Orders JOIN Users ON Orders.customer_id = Users.user_id";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Close the connection
    public function closeConnection() {
        $this->conn->close();
    }
}



?>
