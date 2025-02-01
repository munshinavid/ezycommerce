<?php
class mydb {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "myDB";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Function to get all users
    public function getAllUsers() {
        $sql = "SELECT id, name, gender, email, phone, vehicle_type, nid_number, address, photo, nid FROM delivery_man";
        return $this->conn->query($sql);
    }

    // Function to get limited users (10 records)
    public function getLimitedUsers() {
        $sql = "SELECT id, name, gender, email, phone, vehicle_type, nid_number, address, photo, nid FROM delivery_man LIMIT 10";
        return $this->conn->query($sql);
    }

    // Function to get users by LIKE search
    public function getUsersByLike($search) {
        $search = $this->conn->real_escape_string($search);  // Prevent SQL injection
        $sql = "SELECT id, name, gender, email, phone, vehicle_type, nid_number, address, photo, nid FROM delivery_man WHERE id LIKE '%$search%'";
        return $this->conn->query($sql);
    }

    // Close the connection
    public function close() {
        $this->conn->close();
    }
}
?>
