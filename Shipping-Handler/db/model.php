<?php
class mydb {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "myDB";
    protected $conn;

    public function __construct() {
        $this->conn = new mysqli(
            $this->servername, 
            $this->username, 
            $this->password, 
            $this->dbname
        );
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // User-related methods
    public function getUserByEmail1($email) {
        $stmt = $this->conn->prepare("SELECT id, name, email, password FROM delivery_man WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    // Order-related methods
    public function getOrdersByDeliveryMan($deliveryManId) {
        $stmt = $this->conn->prepare(
            "SELECT order_id, customer_name, delivery_address, order_details, status, created_at 
             FROM orders 
             WHERE delivery_man_id = ?"
        );
        $stmt->bind_param("i", $deliveryManId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateOrderStatus($orderId, $status, $deliveryManId) {
        $valid_statuses = ['pending', 'confirmed', 'delivered'];
        if (!in_array($status, $valid_statuses)) {
            throw new Exception("Invalid status value");
        }

        $stmt = $this->conn->prepare(
            "UPDATE orders 
             SET status = ? 
             WHERE order_id = ? 
             AND delivery_man_id = ?"
        );
        $stmt->bind_param("sii", $status, $orderId, $deliveryManId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function close() {
        $this->conn->close();
    }
}

class Order {
    private $db;
    
    public function __construct() {
        $this->db = new mydb();
    }

    public function getOrdersByDeliveryMan($deliveryManId) {
        return $this->db->getOrdersByDeliveryMan($deliveryManId);
    }

    public function updateOrderStatus($orderId, $status, $deliveryManId) {
        return $this->db->updateOrderStatus($orderId, $status, $deliveryManId);
    }
}
?>