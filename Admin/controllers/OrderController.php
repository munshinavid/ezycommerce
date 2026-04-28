<?php
// OrderController.php - Complete RESTful API Backend for Order Management

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../models/Database.php';

class OrderController {
    private $db;
    
    public function __construct() {
        try {
            $this->db = new Database();
        } catch (Exception $e) {
            $this->sendError('Database connection failed: ' . $e->getMessage(), 500);
        }
    }
    
    // Handle all requests
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Check for specific order ID in query parameter
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        // Check for export request
        $isExport = isset($_GET['export']) && $_GET['export'] === 'csv';
        
        try {
            switch ($method) {
                case 'GET':
                    if ($isExport) {
                        $this->exportOrders();
                    } elseif ($orderId) {
                        $this->getOrderById($orderId);
                    } else {
                        $this->getAllOrders();
                    }
                    break;
                    
                case 'POST':
                    $this->createOrder();
                    break;
                    
                case 'PUT':
                    if ($orderId) {
                        $this->updateOrder($orderId);
                    } else {
                        $this->sendError('Order ID required', 400);
                    }
                    break;
                    
                case 'DELETE':
                    if ($orderId) {
                        $this->deleteOrder($orderId);
                    } else {
                        $this->sendError('Order ID required', 400);
                    }
                    break;
                    
                default:
                    $this->sendError('Method not allowed', 405);
            }
        } catch (Exception $e) {
            $this->sendError('Server error: ' . $e->getMessage(), 500);
        }
    }
    
    // GET /api/orders - Get all orders with filters and pagination
    private function getAllOrders() {
        try {
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $status = isset($_GET['status']) ? $_GET['status'] : 'all';
            $date = isset($_GET['date']) ? $_GET['date'] : 'all';
            
            $offset = ($page - 1) * $limit;
            
            // Build WHERE conditions
            $whereConditions = [];
            $params = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.username LIKE ? OR u.email LIKE ? OR o.order_id LIKE ?)";
                $searchParam = "%$search%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            if ($status !== 'all') {
                $whereConditions[] = "o.order_status = ?";
                $params[] = ucfirst(strtolower($status));
            }
            
            if ($date !== 'all') {
                switch ($date) {
                    case 'today':
                        $whereConditions[] = "DATE(o.created_at) = CURDATE()";
                        break;
                    case 'week':
                        $whereConditions[] = "o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                        break;
                    case 'month':
                        $whereConditions[] = "o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                        break;
                }
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM orders o 
                           INNER JOIN users u ON o.customer_id = u.user_id 
                           $whereClause";
            $countResult = $this->db->select($countQuery, $params);
            $total = isset($countResult[0]['total']) ? (int)$countResult[0]['total'] : 0;
            
            // Get orders
            $query = "SELECT 
                        o.order_id as id,
                        u.username as customer_name,
                        u.email as customer_email,
                        o.order_status as status,
                        o.total_amount,
                        o.created_at,
                        COALESCE(p.status, 'pending') as payment_status
                      FROM orders o
                      INNER JOIN users u ON o.customer_id = u.user_id
                      LEFT JOIN payments p ON o.order_id = p.order_id
                      $whereClause
                      ORDER BY o.created_at DESC
                      LIMIT ? OFFSET ?";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $orders = $this->db->select($query, $params);
            
            // Format orders
            if ($orders && is_array($orders)) {
                foreach ($orders as &$order) {
                    $order['id'] = (int)$order['id'];
                    $order['status'] = strtolower($order['status']);
                    $order['payment_status'] = strtolower($order['payment_status']);
                    $order['total_amount'] = (float)$order['total_amount'];
                }
            } else {
                $orders = [];
            }
            
            $this->sendResponse([
                'orders' => $orders,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => $total > 0 ? (int)ceil($total / $limit) : 1
                ]
            ]);
        } catch (Exception $e) {
            $this->sendError('Failed to fetch orders: ' . $e->getMessage(), 500);
        }
    }
    
    // GET /api/orders/{id} - Get single order details
    private function getOrderById($id) {
        try {
            // Get order details
            $query = "SELECT 
                        o.order_id as id,
                        o.customer_id,
                        u.username as customer_name,
                        u.email as customer_email,
                        COALESCE(cd.phone, 'N/A') as customer_phone,
                        COALESCE(cd.address, 'N/A') as shipping_address,
                        o.order_status as status,
                        o.total_amount,
                        o.created_at,
                        COALESCE(p.method, 'Cash on Delivery') as payment_method,
                        COALESCE(p.status, 'pending') as payment_status
                      FROM orders o
                      INNER JOIN users u ON o.customer_id = u.user_id
                      LEFT JOIN customerdetails cd ON u.user_id = cd.user_id
                      LEFT JOIN payments p ON o.order_id = p.order_id
                      WHERE o.order_id = ?";
            
            $orderResult = $this->db->select($query, [$id]);
            
            if (empty($orderResult) || !isset($orderResult[0])) {
                $this->sendError('Order not found', 404);
                return;
            }
            
            $order = $orderResult[0];
            
            // Get order items
            $itemsQuery = "SELECT 
                            oi.order_item_id,
                            p.name,
                            p.image_url,
                            oi.quantity,
                            oi.price_at_purchase as price
                           FROM order_items oi
                           INNER JOIN products p ON oi.product_id = p.product_id
                           WHERE oi.order_id = ?";
            
            $items = $this->db->select($itemsQuery, [$id]);
            
            // Calculate breakdown
            $subtotal = 0.00;
            if ($items && is_array($items)) {
                foreach ($items as &$item) {
                    $item['price'] = (float)$item['price'];
                    $item['quantity'] = (int)$item['quantity'];
                    $subtotal += $item['price'] * $item['quantity'];
                }
            } else {
                $items = [];
            }
            
            // Calculate shipping, tax, discount
            $shipping_cost = 10.00;
            $tax_rate = 0.08;
            $tax_amount = $subtotal * $tax_rate;
            $discount = 0.00;
            
            // Verify total matches
            $calculated_total = $subtotal + $shipping_cost + $tax_amount - $discount;
            
            // Format order
            $order['id'] = (int)$order['id'];
            $order['customer_id'] = (int)$order['customer_id'];
            $order['status'] = strtolower($order['status']);
            $order['payment_status'] = strtolower($order['payment_status']);
            $order['total_amount'] = (float)$order['total_amount'];
            $order['items'] = $items;
            $order['subtotal'] = round($subtotal, 2);
            $order['shipping_cost'] = round($shipping_cost, 2);
            $order['tax_amount'] = round($tax_amount, 2);
            $order['discount'] = round($discount, 2);
            
            $this->sendResponse($order);
        } catch (Exception $e) {
            $this->sendError('Failed to fetch order details: ' . $e->getMessage(), 500);
        }
    }
    
    // POST /api/orders - Create new order
    private function createOrder() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['customer_id']) || !isset($data['items']) || empty($data['items'])) {
                $this->sendError('Invalid order data. customer_id and items are required', 400);
                return;
            }
            
            $this->db->beginTransaction();
            
            // Calculate total
            $total = 0;
            foreach ($data['items'] as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['price'])) {
                    throw new Exception('Invalid item data');
                }
                $total += $item['price'] * $item['quantity'];
            }
            
            // Add shipping and tax
            $shipping = 10.00;
            $tax = $total * 0.08;
            $total = $total + $shipping + $tax;
            
            // Insert order
            $orderQuery = "INSERT INTO orders (customer_id, order_status, total_amount, created_at) 
                          VALUES (?, 'Pending', ?, NOW())";
            $orderId = $this->db->insert($orderQuery, [$data['customer_id'], $total]);
            
            if (!$orderId) {
                throw new Exception('Failed to create order');
            }
            
            // Insert order items
            foreach ($data['items'] as $item) {
                $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                             VALUES (?, ?, ?, ?)";
                $this->db->insert($itemQuery, [
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ]);
                
                // Update product stock
                $stockQuery = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
                $this->db->update($stockQuery, [$item['quantity'], $item['product_id']]);
            }
            
            // Insert payment record
            $paymentQuery = "INSERT INTO payments (order_id, amount, method, status, created_at) 
                            VALUES (?, ?, ?, 'Pending', NOW())";
            $this->db->insert($paymentQuery, [
                $orderId,
                $total,
                isset($data['payment_method']) ? $data['payment_method'] : 'Cash on Delivery'
            ]);
            
            $this->db->commit();
            
            $this->sendResponse([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $orderId
            ], 201);
            
        } catch (Exception $e) {
            $this->db->rollback();
            $this->sendError('Failed to create order: ' . $e->getMessage(), 500);
        }
    }
    
    // PUT /api/orders/{id} - Update order
    private function updateOrder($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Check if order exists
            $checkQuery = "SELECT order_id FROM orders WHERE order_id = ?";
            $exists = $this->db->select($checkQuery, [$id]);
            
            if (empty($exists)) {
                $this->sendError('Order not found', 404);
                return;
            }
            
            $updates = [];
            $params = [];
            
            if (isset($data['status'])) {
                $updates[] = "order_status = ?";
                $params[] = ucfirst(strtolower($data['status']));
            }
            
            if (empty($updates)) {
                $this->sendError('No valid fields to update', 400);
                return;
            }
            
            $params[] = $id;
            
            $query = "UPDATE orders SET " . implode(", ", $updates) . " WHERE order_id = ?";
            $success = $this->db->update($query, $params);
            
            if ($success !== false) {
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Order updated successfully'
                ]);
            } else {
                $this->sendError('Failed to update order', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Failed to update order: ' . $e->getMessage(), 500);
        }
    }
    
    // DELETE /api/orders/{id} - Delete order
    private function deleteOrder($id) {
        try {
            // Check if order exists
            $checkQuery = "SELECT order_id FROM orders WHERE order_id = ?";
            $exists = $this->db->select($checkQuery, [$id]);
            
            if (empty($exists)) {
                $this->sendError('Order not found', 404);
                return;
            }
            
            $this->db->beginTransaction();
            
            // Delete order items first
            $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
            $this->db->delete($deleteItemsQuery, [$id]);
            
            // Delete payments
            $deletePaymentsQuery = "DELETE FROM payments WHERE order_id = ?";
            $this->db->delete($deletePaymentsQuery, [$id]);
            
            // Delete order
            $deleteOrderQuery = "DELETE FROM orders WHERE order_id = ?";
            $success = $this->db->delete($deleteOrderQuery, [$id]);
            
            $this->db->commit();
            
            if ($success !== false) {
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Order deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete order');
            }
        } catch (Exception $e) {
            $this->db->rollback();
            $this->sendError('Failed to delete order: ' . $e->getMessage(), 500);
        }
    }
    
    // GET /api/orders/export - Export orders to CSV
    private function exportOrders() {
        try {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $status = isset($_GET['status']) ? $_GET['status'] : 'all';
            $date = isset($_GET['date']) ? $_GET['date'] : 'all';
            
            // Build WHERE conditions
            $whereConditions = [];
            $params = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.username LIKE ? OR u.email LIKE ? OR o.order_id LIKE ?)";
                $searchParam = "%$search%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            if ($status !== 'all') {
                $whereConditions[] = "o.order_status = ?";
                $params[] = ucfirst(strtolower($status));
            }
            
            if ($date !== 'all') {
                switch ($date) {
                    case 'today':
                        $whereConditions[] = "DATE(o.created_at) = CURDATE()";
                        break;
                    case 'week':
                        $whereConditions[] = "o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                        break;
                    case 'month':
                        $whereConditions[] = "o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                        break;
                }
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
            
            $query = "SELECT 
                        o.order_id,
                        u.username as customer_name,
                        u.email as customer_email,
                        o.order_status,
                        o.total_amount,
                        o.created_at,
                        COALESCE(p.status, 'pending') as payment_status
                      FROM orders o
                      INNER JOIN users u ON o.customer_id = u.user_id
                      LEFT JOIN payments p ON o.order_id = p.order_id
                      $whereClause
                      ORDER BY o.created_at DESC
                      LIMIT 1000";
            
            $orders = $this->db->select($query, $params);
            
            // Set CSV headers
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="orders_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // Write CSV header
            fputcsv($output, ['Order ID', 'Customer Name', 'Customer Email', 'Status', 'Total Amount', 'Payment Status', 'Order Date']);
            
            // Write data rows
            if ($orders && is_array($orders)) {
                foreach ($orders as $order) {
                    fputcsv($output, [
                        $order['order_id'],
                        $order['customer_name'],
                        $order['customer_email'],
                        $order['order_status'],
                        '$' . number_format($order['total_amount'], 2),
                        $order['payment_status'],
                        date('Y-m-d H:i:s', strtotime($order['created_at']))
                    ]);
                }
            }
            
            fclose($output);
            exit();
        } catch (Exception $e) {
            $this->sendError('Failed to export orders: ' . $e->getMessage(), 500);
        }
    }
    
    // Send success response
    private function sendResponse($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit();
    }
    
    // Send error response
    private function sendError($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
        exit();
    }
}

// Initialize and handle request
try {
    $controller = new OrderController();
    $controller->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Fatal error: ' . $e->getMessage()
    ]);
}