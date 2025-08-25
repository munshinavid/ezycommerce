<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'ecomm';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Execute a SELECT query and return results
    public function select($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die("Prepare failed: " . $this->conn->error);
        }
    
        if (!empty($params)) {
            $this->bindParams($stmt, $params);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Fetch data row by row using fetch_assoc
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    
        $stmt->close();
        return $data;
    }
    

    // Execute an INSERT/UPDATE/DELETE query
    public function execute($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die("Prepare failed: " . $this->conn->error);
        }

        if (!empty($params)) {
            $this->bindParams($stmt, $params);
        }

        $success = $stmt->execute();
        if ($stmt->affected_rows === -1) {
            die("Execution failed: " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    // Bind parameters to the prepared statement
    private function bindParams($stmt, $params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$params);
    }

    // Get the last inserted ID
    public function getLastInsertId() {
        return $this->conn->insert_id;
    }

    // Close the connection
    public function close() {
        $this->conn->close();
    }
}
