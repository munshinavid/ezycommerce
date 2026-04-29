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
        $orders = $this->db->select("SELECT COUNT(*) as total_orders FROM orders");
        $stats['total_orders'] = $orders[0]['total_orders'];

        // Total Users
        $users = $this->db->select("SELECT COUNT(*) as total_users FROM users");
        $stats['total_users'] = $users[0]['total_users'];

        // Total Products
        $products = $this->db->select("SELECT COUNT(*) as total_products FROM products");
        $stats['total_products'] = $products[0]['total_products'];

        // Total Revenue
        $revenue = $this->db->select("SELECT SUM(total_amount) as total_revenue FROM orders");
        $stats['total_revenue'] = $revenue[0]['total_revenue'] ?? 0;

        return $stats;
    }

    public function getRecentOrders($limit = 5) {
        return $this->db->select("
            SELECT o.order_id, u.username as customer, o.created_at, o.total_amount, o.order_status
            FROM orders o
            JOIN users u ON o.customer_id = u.user_id
            ORDER BY o.created_at DESC
            LIMIT ?
        ", [$limit]);
    }

    public function getTopProducts($limit = 5) {
    return $this->db->select("
        SELECT 
            p.product_id, 
            p.name, 
            c.category_name AS category, 
            p.price, 
            SUM(oi.quantity) AS sold, 
            p.stock
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        GROUP BY p.product_id, p.name, c.category_name, p.price, p.stock
        ORDER BY sold DESC
        LIMIT ?
    ", [$limit]);
}

}
