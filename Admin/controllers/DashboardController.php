<?php
require_once __DIR__ . '/../models/Database.php';

class DashboardController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getStats() {
        $stats = [];

        // Total Orders
        $orders = $this->db->select("SELECT COUNT(*) as total_orders FROM Orders");
        $stats['total_orders'] = $orders[0]['total_orders'];

        // Total Users
        $users = $this->db->select("SELECT COUNT(*) as total_users FROM Users");
        $stats['total_users'] = $users[0]['total_users'];

        // Total Products
        $products = $this->db->select("SELECT COUNT(*) as total_products FROM Products");
        $stats['total_products'] = $products[0]['total_products'];

        // Total Revenue
        $revenue = $this->db->select("SELECT SUM(total_amount) as total_revenue FROM Orders");
        $stats['total_revenue'] = $revenue[0]['total_revenue'] ?? 0;

        return $stats;
    }

    public function getRecentOrders($limit = 5) {
        return $this->db->select("
            SELECT o.order_id, u.username as customer, o.created_at, o.total_amount, o.order_status
            FROM Orders o
            JOIN Users u ON o.customer_id = u.user_id
            ORDER BY o.created_at DESC
            LIMIT ?
        ", [$limit]);
    }

    public function getTopProducts($limit = 5) {
        return $this->db->select("
            SELECT p.product_id, p.name, p.category, p.price, SUM(oi.quantity) as sold, p.stock
            FROM Order_Items oi
            JOIN Products p ON oi.product_id = p.product_id
            GROUP BY p.product_id, p.name, p.category, p.price, p.stock
            ORDER BY sold DESC
            LIMIT ?
        ", [$limit]);
    }
}
