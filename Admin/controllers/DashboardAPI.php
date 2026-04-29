<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../../utils/UrlHelper.php';

class DashboardAPI {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get Dashboard Statistics
    public function getStats() {
        try {
            $totalOrders = $this->db->select("SELECT COUNT(*) as count FROM orders");
            $totalUsers = $this->db->select("SELECT COUNT(*) as count FROM users WHERE role_id != 1");
            $totalProducts = $this->db->select("SELECT COUNT(*) as count FROM products");
            $totalRevenue = $this->db->select("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE order_status != 'Cancelled'");

            return [
                'success' => true,
                'data' => [
                    'total_orders' => $totalOrders[0]['count'] ?? 0,
                    'total_users' => $totalUsers[0]['count'] ?? 0,
                    'total_products' => $totalProducts[0]['count'] ?? 0,
                    'total_revenue' => $totalRevenue[0]['total'] ?? 0
                ]
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get Recent Orders
    public function getRecentOrders($limit = 10) {
        try {
            $query = "SELECT o.order_id, u.username as customer, o.created_at, 
                      o.total_amount, o.order_status 
                      FROM orders o 
                      JOIN users u ON o.customer_id = u.user_id 
                      ORDER BY o.created_at DESC 
                      LIMIT ?";
            $data = $this->db->select($query, [$limit]);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get Top Products
    public function getTopProducts($limit = 10) {
        try {
            $query = "SELECT p.product_id, p.name, c.category_name as category, p.price, 
                      COALESCE(SUM(oi.quantity), 0) as sold, p.stock 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.category_id 
                      LEFT JOIN order_items oi ON p.product_id = oi.product_id 
                      GROUP BY p.product_id 
                      ORDER BY sold DESC 
                      LIMIT ?";
            $data = $this->db->select($query, [$limit]);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get Sales Data for Chart
    public function getSalesData($days = 7) {
        try {
            $query = "SELECT DATE(created_at) as date, 
                      COUNT(*) as orders, 
                      COALESCE(SUM(total_amount), 0) as revenue 
                      FROM orders 
                      WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                      AND order_status != 'Cancelled'
                      GROUP BY DATE(created_at) 
                      ORDER BY date ASC";
            $data = $this->db->select($query, [$days]);
            
            // Fill missing dates with zero values
            $result = $this->fillMissingDates($data, $days);
            
            return ['success' => true, 'data' => $result];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Fill Missing Dates in Sales Data
    private function fillMissingDates($data, $days) {
        $result = [];
        $dataMap = [];
        
        // Create a map of existing dates
        foreach ($data as $row) {
            $dataMap[$row['date']] = $row;
        }
        
        // Fill all dates in the range
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            if (isset($dataMap[$date])) {
                $result[] = $dataMap[$date];
            } else {
                $result[] = [
                    'date' => $date,
                    'orders' => 0,
                    'revenue' => 0
                ];
            }
        }
        
        return $result;
    }

    // Get Revenue by Category
    public function getRevenueByCategory() {
        try {
            $query = "SELECT c.category_name, 
                      COALESCE(SUM(oi.quantity * oi.price_at_purchase), 0) as revenue 
                      FROM categories c 
                      LEFT JOIN products p ON c.category_id = p.category_id 
                      LEFT JOIN order_items oi ON p.product_id = oi.product_id 
                      LEFT JOIN orders o ON oi.order_id = o.order_id 
                      WHERE o.order_status != 'Cancelled' OR o.order_status IS NULL
                      GROUP BY c.category_id 
                      HAVING revenue > 0
                      ORDER BY revenue DESC 
                      LIMIT 10";
            $data = $this->db->select($query);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get Order Status Distribution
    public function getOrderStatusDistribution() {
        try {
            $query = "SELECT order_status, COUNT(*) as count 
                      FROM orders 
                      GROUP BY order_status";
            $data = $this->db->select($query);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Update Order Status
    public function updateOrderStatus($orderId, $status) {
        try {
            $validStatuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
            if (!in_array($status, $validStatuses)) {
                return ['success' => false, 'message' => 'Invalid status'];
            }

            $query = "UPDATE orders SET order_status = ? WHERE order_id = ?";
            $this->db->update($query, [$status, $orderId]);
            
            // Update shipping status if applicable
            if ($status === 'Shipped') {
                $shippingQuery = "UPDATE shipping SET shipping_status = 'Shipped' WHERE order_id = ?";
                $this->db->update($shippingQuery, [$orderId]);
            } elseif ($status === 'Delivered') {
                $shippingQuery = "UPDATE shipping SET shipping_status = 'Delivered' WHERE order_id = ?";
                $this->db->update($shippingQuery, [$orderId]);
            }
            
            return ['success' => true, 'message' => 'Order status updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get Order Details
    public function getOrderDetails($orderId) {
        try {
            $orderQuery = "SELECT o.*, u.username, u.email 
                          FROM orders o 
                          JOIN users u ON o.customer_id = u.user_id 
                          WHERE o.order_id = ?";
            $order = $this->db->select($orderQuery, [$orderId]);
            
            if (empty($order)) {
                return ['success' => false, 'message' => 'Order not found'];
            }
            
            $itemsQuery = "SELECT oi.*, p.name, p.image_url 
                          FROM order_items oi 
                          JOIN products p ON oi.product_id = p.product_id 
                          WHERE oi.order_id = ?";
            $items = $this->db->select($itemsQuery, [$orderId]);
            
            return [
                'success' => true,
                'data' => [
                    'order' => $order[0],
                    'items' => $items
                ]
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Delete Product
    public function deleteProduct($productId) {
        try {
            $query = "DELETE FROM products WHERE product_id = ?";
            $this->db->delete($query, [$productId]);
            return ['success' => true, 'message' => 'Product deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Update Product Stock
    public function updateProductStock($productId, $stock) {
        try {
            $query = "UPDATE products SET stock = ? WHERE product_id = ?";
            $this->db->update($query, [$stock, $productId]);
            return ['success' => true, 'message' => 'Stock updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get Low Stock Products
    public function getLowStockProducts($threshold = 10) {
        try {
            $query = "SELECT p.product_id, p.name, p.stock, c.category_name 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.category_id 
                      WHERE p.stock <= ? 
                      ORDER BY p.stock ASC";
            $data = $this->db->select($query, [$threshold]);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get Recent Activity
    public function getRecentActivity($limit = 10) {
        try {
            $query = "SELECT 'order' as type, o.order_id as id, 
                      CONCAT('New order from ', u.username) as description, 
                      o.created_at as timestamp 
                      FROM orders o 
                      JOIN users u ON o.customer_id = u.user_id 
                      ORDER BY o.created_at DESC 
                      LIMIT ?";
            $data = $this->db->select($query, [$limit]);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

// Handle API Requests
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin privileges required.']);
    exit();
}

$api = new DashboardAPI();
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        switch ($action) {
            case 'update_order_status':
                $result = $api->updateOrderStatus($input['order_id'], $input['status']);
                break;
            case 'update_product_stock':
                $result = $api->updateProductStock($input['product_id'], $input['stock']);
                break;
            case 'delete_product':
                $result = $api->deleteProduct($input['product_id']);
                break;
            default:
                $result = ['success' => false, 'message' => 'Invalid action'];
        }
    } else {
        switch ($action) {
            case 'stats':
                $result = $api->getStats();
                break;
            case 'recent_orders':
                $limit = $_GET['limit'] ?? 10;
                $result = $api->getRecentOrders($limit);
                break;
            case 'top_products':
                $limit = $_GET['limit'] ?? 10;
                $result = $api->getTopProducts($limit);
                break;
            case 'sales_data':
                $days = $_GET['days'] ?? 7;
                $result = $api->getSalesData($days);
                break;
            case 'revenue_by_category':
                $result = $api->getRevenueByCategory();
                break;
            case 'order_status':
                $result = $api->getOrderStatusDistribution();
                break;
            case 'order_details':
                $orderId = $_GET['order_id'] ?? 0;
                $result = $api->getOrderDetails($orderId);
                break;
            case 'low_stock':
                $threshold = $_GET['threshold'] ?? 10;
                $result = $api->getLowStockProducts($threshold);
                break;
            case 'recent_activity':
                $limit = $_GET['limit'] ?? 10;
                $result = $api->getRecentActivity($limit);
                break;
            default:
                $result = ['success' => false, 'message' => 'Invalid action'];
        }
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>