<?php
class Model {
    private $db;

    // Database connection method
    public function OpenCon() {
        $host = "localhost";        // Database host
        $username = "root";         // Database username
        $password = "";             // Database password
        $dbname = "ecommerce";      // Database name

        // Create connection
        $conn = new mysqli($host, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        return $conn;
    }

    public function __construct() {
        // Initialize the database connection
        $this->db = $this->OpenCon();
    }

    public function getAllUsers($table) {
        $sql = "SELECT * FROM $table";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllProducts($table) {
        $sql = "SELECT * FROM $table";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductByIdOrName($table, $search) {
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE product_id = ? OR name LIKE ?");
        $search_like = "%$search%";
        $stmt->bind_param("ss", $search, $search_like);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addProduct($table, $product_id, $name, $price, $stock, $image_url) {
        $allowedTables = ['products']; // Add other allowed table names if necessary
        if (!in_array($table, $allowedTables)) {
            throw new Exception("Invalid table name.");
        }

        $stmt = $this->db->prepare("INSERT INTO $table (product_id, name, price, stock, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $product_id, $name, $price, $stock, $image_url);
        return $stmt->execute();
    }

    public function updateProduct($table, $product_id, $name, $price, $stock, $discount_id, $start_date, $end_date) {
        $stmt = $this->db->prepare("
            UPDATE $table 
            SET name = ?, price = ?, stock = ?, 
                discount_id = ?, start_date = ?, end_date = ? 
            WHERE product_id = ?
        ");
        $stmt->bind_param("sdissss", $name, $price, $stock, $discount_id, $start_date, $end_date, $product_id);
        return $stmt->execute();
    }

    public function deleteProduct($table, $product_id) {
        $stmt = $this->db->prepare("DELETE FROM $table WHERE product_id = ?");
        $stmt->bind_param("s", $product_id);
        return $stmt->execute();
    }

    public function createUser($firstname, $email, $password) {
        $stmt = $this->db->prepare("INSERT INTO users (firstname, email, password) VALUES (?, ?, ?)");
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sss", $firstname, $email, $hashed_password);
        return $stmt->execute();
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createUser1($firstname, $email, $password) {
        $stmt = $this->db->prepare("INSERT INTO addmanager (firstname, email, password) VALUES (?, ?, ?)");
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sss", $firstname, $email, $hashed_password);
        return $stmt->execute();
    }

    public function getUserByEmail1($email) {
        $stmt = $this->db->prepare("SELECT * FROM addmanager WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>