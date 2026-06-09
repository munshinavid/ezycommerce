<?php
require_once __DIR__ . '/../../utils/ErrorHandler.php';

/**
 * Database class for the Logistics module.
 * Uses mysqli with centralized config from config/db.php.
 */
class Database {
    private $conn;
    private $lastRowCount = 0;

    public function __construct() {
        $this->connect();
    }

    public function __destruct() {
        $this->close();
    }

    private function connect() {
        $cfg = require __DIR__ . '/../../config/db.php';

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->conn = new mysqli(
                $cfg['host'],
                $cfg['username'],
                $cfg['password'],
                $cfg['database'],
                $cfg['port']
            );
            $this->conn->set_charset($cfg['charset'] ?? 'utf8mb4');
        } catch (mysqli_sql_exception $e) {
            throw new Exception("DB Connection Failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Execute a SELECT query and return associative array results.
     */
    public function select($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Error in DB select: " . $this->conn->error);
        }

        if (!empty($params)) {
            $types = $this->detectTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $this->lastRowCount = count($data);
        $stmt->close();

        return $data;
    }

    /**
     * Execute an INSERT/UPDATE/DELETE query.
     */
    public function execute($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Error Processing execute: " . $this->conn->error);
        }

        if (!empty($params)) {
            $types = $this->detectTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $this->lastRowCount = $stmt->affected_rows;
        $stmt->close();

        return true;
    }

    /**
     * Insert wrapper — returns the last inserted ID.
     */
    public function insert($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Error Processing insert: " . $this->conn->error);
        }

        if (!empty($params)) {
            $types = $this->detectTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $id = $stmt->insert_id;
        $this->lastRowCount = $stmt->affected_rows;
        $stmt->close();

        return $id;
    }

    /**
     * Update wrapper.
     */
    public function update($query, $params = []) {
        return $this->execute($query, $params);
    }

    /**
     * Delete wrapper.
     */
    public function delete($query, $params = []) {
        return $this->execute($query, $params);
    }

    /**
     * Get the last inserted ID.
     */
    public function getLastInsertId() {
        return $this->conn->insert_id;
    }

    /**
     * Return the number of rows affected/returned by the last operation.
     */
    public function rowCount() {
        return $this->lastRowCount ?? 0;
    }

    /**
     * Transaction controls.
     */
    public function beginTransaction() {
        $this->conn->begin_transaction();
    }

    public function commit() {
        $this->conn->commit();
    }

    public function rollback() {
        $this->conn->rollback();
    }

    /**
     * Close the connection.
     */
    public function close() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    /**
     * Auto-detect parameter types for mysqli bind_param.
     */
    private function detectTypes(array $params): string {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        return $types;
    }
}