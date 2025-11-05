<?php
// api.php - Complete RESTful API for Order Management Dashboard
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../models/Database.php';

// Parse the request
$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : null;

$db = new Database();

try {
    // Route requests
    if ($request === 'dashboard' && $method === 'GET') {
        getDashboardData($db);
    } 
    elseif ($request === 'orders') {
        handleOrders($db, $method, $action, $id);
    }
    elseif ($request === 'returns') {
        handleReturns($db, $method, $action, $id);
    }
    elseif ($request === 'shipping') {
        handleShipping($db, $method, $action, $id);
    }
    else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Endpoint not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// ============================================
// DASHBOARD ENDPOINTS
// ============================================
function getDashboardData($db) {
    try {
        // Get order counts by status
        $countsQuery = "SELECT order_status, COUNT(*) as count FROM orders GROUP BY order_status";
        $countsResult = $db->select($countsQuery);
        
        $counts = [
            'pending' => 0,
            'processing' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0
        ];
        
        foreach ($countsResult as $row) {
            $status = strtolower($row['order_status']);
            $counts[$status] = (int)$row['count'];
        }
        
        // Get recent orders
        $ordersQuery = "SELECT o.order_id, o.order_status, o.total_amount, o.created_at, 
                               u.username as customer_name
                        FROM orders o
                        JOIN users u ON o.customer_id = u.user_id
                        ORDER BY o.created_at DESC
                        LIMIT 10";
        $recentOrders = $db->select($ordersQuery);
        
        // Format orders
        $formattedOrders = array_map(function($row) {
            return [
                'order_id' => $row['order_id'],
                'customer_name' => $row['customer_name'],
                'created_at' => $row['created_at'],
                'status' => $row['order_status'],
                'total_amount' => number_format($row['total_amount'], 2)
            ];
        }, $recentOrders);
        
        // Get return requests
        $returnsQuery = "SELECT r.return_id, r.order_id, r.reason, r.status, r.processed_at
                        FROM returns r
                        WHERE r.status IN ('Pending', 'Processing')
                        ORDER BY r.processed_at DESC";
        $returns = $db->select($returnsQuery);
        
        // Get shipping data
        $shippingQuery = "SELECT s.order_id, s.shipping_status, s.tracking_number, s.updated_at,
                                 u.username as customer_name
                          FROM shipping s
                          JOIN orders o ON s.order_id = o.order_id
                          JOIN users u ON o.customer_id = u.user_id
                          ORDER BY s.updated_at DESC
                          LIMIT 10";
        $shipping = $db->select($shippingQuery);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => [
                'counts' => $counts,
                'recentOrders' => $formattedOrders,
                'returns' => $returns,
                'shipping' => $shipping
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// ORDER ENDPOINTS
// ============================================
function handleOrders($db, $method, $action, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                getOrder($db, $id);
            } else {
                getAllOrders($db);
            }
            break;
            
        case 'POST':
            if ($action === 'create') {
                createOrder($db);
            } elseif ($action === 'update-status') {
                updateOrderStatus($db);
            }
            break;
            
        case 'PUT':
            if ($id) {
                updateOrder($db, $id);
            }
            break;
            
        case 'DELETE':
            if ($id) {
                deleteOrder($db, $id);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
}

function getAllOrders($db) {
    try {
        $query = "SELECT o.order_id, o.order_status, o.total_amount, o.created_at,
                         u.username as customer_name, u.email as customer_email
                  FROM orders o
                  JOIN users u ON o.customer_id = u.user_id
                  ORDER BY o.created_at DESC";
        $orders = $db->select($query);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $orders]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function getOrder($db, $id) {
    try {
        $query = "SELECT o.*, u.username as customer_name, u.email as customer_email
                  FROM orders o
                  JOIN users u ON o.customer_id = u.user_id
                  WHERE o.order_id = ?";
        $order = $db->select($query, [$id]);
        
        if (empty($order)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Order not found']);
            return;
        }
        
        // Get order items
        $itemsQuery = "SELECT oi.*, p.name as product_name
                      FROM order_items oi
                      JOIN products p ON oi.product_id = p.product_id
                      WHERE oi.order_id = ?";
        $items = $db->select($itemsQuery, [$id]);
        
        $order[0]['items'] = $items;
        
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $order[0]]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function createOrder($db) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['customer_id']) || !isset($data['items']) || empty($data['items'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            return;
        }
        
        $db->beginTransaction();
        
        // Calculate total
        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Create order
        $orderQuery = "INSERT INTO orders (customer_id, order_status, total_amount) 
                      VALUES (?, 'Pending', ?)";
        $orderId = $db->insert($orderQuery, [$data['customer_id'], $total]);
        
        // Add order items
        foreach ($data['items'] as $item) {
            $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase)
                         VALUES (?, ?, ?, ?)";
            $db->insert($itemQuery, [
                $orderId, 
                $item['product_id'], 
                $item['quantity'], 
                $item['price']
            ]);
            
            // Update product stock
            $stockQuery = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
            $db->update($stockQuery, [$item['quantity'], $item['product_id']]);
        }
        
        // Create shipping record
        $shippingQuery = "INSERT INTO shipping (order_id, shipping_status) VALUES (?, 'Pending')";
        $db->insert($shippingQuery, [$orderId]);
        
        // Create payment record
        $paymentQuery = "INSERT INTO payments (order_id, amount, method, status) 
                        VALUES (?, ?, ?, 'Pending')";
        $db->insert($paymentQuery, [
            $orderId, 
            $total, 
            $data['payment_method'] ?? 'Cash on Delivery'
        ]);
        
        $db->commit();
        
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'Order created successfully',
            'order_id' => $orderId
        ]);
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function updateOrderStatus($db) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['order_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            return;
        }
        
        $db->beginTransaction();
        
        // Update order status
        $orderQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $db->update($orderQuery, [$data['status'], $data['order_id']]);
        
        // Update shipping status and tracking number if provided
        if (isset($data['tracking_number']) && !empty($data['tracking_number'])) {
            $shippingStatus = $data['status'] === 'Shipped' ? 'Shipped' : 'Processing';
            $shippingQuery = "UPDATE shipping SET shipping_status = ?, tracking_number = ? 
                            WHERE order_id = ?";
            $db->update($shippingQuery, [
                $shippingStatus, 
                $data['tracking_number'], 
                $data['order_id']
            ]);
        } else {
            // Just update shipping status based on order status
            $shippingStatusMap = [
                'Pending' => 'Pending',
                'Processing' => 'Processing',
                'Shipped' => 'Shipped',
                'Delivered' => 'Delivered',
                'Cancelled' => 'Pending'
            ];
            $shippingStatus = $shippingStatusMap[$data['status']] ?? 'Pending';
            $shippingQuery = "UPDATE shipping SET shipping_status = ? WHERE order_id = ?";
            $db->update($shippingQuery, [$shippingStatus, $data['order_id']]);
        }
        
        // Update payment status if order is delivered
        if ($data['status'] === 'Delivered') {
            $paymentQuery = "UPDATE payments SET status = 'Completed' WHERE order_id = ?";
            $db->update($paymentQuery, [$data['order_id']]);
        }
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Order status updated successfully'
        ]);
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function updateOrder($db, $id) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $query = "UPDATE orders SET order_status = ?, total_amount = ? WHERE order_id = ?";
        $db->update($query, [
            $data['order_status'] ?? 'Pending',
            $data['total_amount'] ?? 0,
            $id
        ]);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function deleteOrder($db, $id) {
    try {
        $db->beginTransaction();
        
        // Delete order (cascade will handle related records)
        $query = "DELETE FROM orders WHERE order_id = ?";
        $db->delete($query, [$id]);
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// RETURN ENDPOINTS
// ============================================
function handleReturns($db, $method, $action, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                getReturn($db, $id);
            } else {
                getAllReturns($db);
            }
            break;
            
        case 'POST':
            if ($action === 'create') {
                createReturn($db);
            } elseif ($action === 'process') {
                processReturn($db);
            }
            break;
            
        case 'PUT':
            if ($id) {
                updateReturn($db, $id);
            }
            break;
            
        case 'DELETE':
            if ($id) {
                deleteReturn($db, $id);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
}

function getAllReturns($db) {
    try {
        $query = "SELECT r.*, o.order_status, u.username as customer_name
                  FROM returns r
                  JOIN orders o ON r.order_id = o.order_id
                  JOIN users u ON o.customer_id = u.user_id
                  ORDER BY r.processed_at DESC";
        $returns = $db->select($query);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $returns]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function getReturn($db, $id) {
    try {
        $query = "SELECT r.*, o.order_status, o.total_amount, u.username as customer_name
                  FROM returns r
                  JOIN orders o ON r.order_id = o.order_id
                  JOIN users u ON o.customer_id = u.user_id
                  WHERE r.return_id = ?";
        $return = $db->select($query, [$id]);
        
        if (empty($return)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Return request not found']);
            return;
        }
        
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $return[0]]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function createReturn($db) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['order_id']) || !isset($data['reason'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            return;
        }
        
        // Check if order exists
        $orderQuery = "SELECT order_id FROM orders WHERE order_id = ?";
        $order = $db->select($orderQuery, [$data['order_id']]);
        
        if (empty($order)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Order not found']);
            return;
        }
        
        $query = "INSERT INTO returns (order_id, reason, status) VALUES (?, ?, 'Pending')";
        $returnId = $db->insert($query, [$data['order_id'], $data['reason']]);
        
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'Return request created successfully',
            'return_id' => $returnId
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function processReturn($db) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['return_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            return;
        }
        
        $validStatuses = ['Pending', 'Processing', 'Approved', 'Rejected'];
        if (!in_array($data['status'], $validStatuses)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            return;
        }
        
        $db->beginTransaction();
        
        // Update return status
        $query = "UPDATE returns SET status = ?, handled_by = ? WHERE return_id = ?";
        $db->update($query, [
            $data['status'], 
            $data['handled_by'] ?? null, 
            $data['return_id']
        ]);
        
        // If approved, restore stock and update order status
        if ($data['status'] === 'Approved') {
            // Get order details
            $returnQuery = "SELECT order_id FROM returns WHERE return_id = ?";
            $returnData = $db->select($returnQuery, [$data['return_id']]);
            $orderId = $returnData[0]['order_id'];
            
            // Restore stock
            $itemsQuery = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
            $items = $db->select($itemsQuery, [$orderId]);
            
            foreach ($items as $item) {
                $stockQuery = "UPDATE products SET stock = stock + ? WHERE product_id = ?";
                $db->update($stockQuery, [$item['quantity'], $item['product_id']]);
            }
            
            // Update order status
            $orderQuery = "UPDATE orders SET order_status = 'Cancelled' WHERE order_id = ?";
            $db->update($orderQuery, [$orderId]);
        }
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Return request processed successfully'
        ]);
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function updateReturn($db, $id) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $query = "UPDATE returns SET status = ?, reason = ? WHERE return_id = ?";
        $db->update($query, [
            $data['status'] ?? 'Pending',
            $data['reason'] ?? '',
            $id
        ]);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Return updated successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function deleteReturn($db, $id) {
    try {
        $query = "DELETE FROM returns WHERE return_id = ?";
        $db->delete($query, [$id]);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Return deleted successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// SHIPPING ENDPOINTS
// ============================================
function handleShipping($db, $method, $action, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                getShipping($db, $id);
            } else {
                getAllShipping($db);
            }
            break;
            
        case 'POST':
            if ($action === 'update') {
                updateShippingStatus($db);
            }
            break;
            
        case 'PUT':
            if ($id) {
                updateShippingRecord($db, $id);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
}

function getAllShipping($db) {
    try {
        $query = "SELECT s.*, o.total_amount, u.username as customer_name
                  FROM shipping s
                  JOIN orders o ON s.order_id = o.order_id
                  JOIN users u ON o.customer_id = u.user_id
                  ORDER BY s.updated_at DESC";
        $shipping = $db->select($query);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $shipping]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function getShipping($db, $orderId) {
    try {
        $query = "SELECT s.*, o.order_status, o.total_amount, u.username as customer_name
                  FROM shipping s
                  JOIN orders o ON s.order_id = o.order_id
                  JOIN users u ON o.customer_id = u.user_id
                  WHERE s.order_id = ?";
        $shipping = $db->select($query, [$orderId]);
        
        if (empty($shipping)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Shipping record not found']);
            return;
        }
        
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $shipping[0]]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function updateShippingStatus($db) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['order_id']) || !isset($data['shipping_status'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            return;
        }
        
        $validStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered'];
        if (!in_array($data['shipping_status'], $validStatuses)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid shipping status']);
            return;
        }
        
        $db->beginTransaction();
        
        // Update shipping record
        if (isset($data['tracking_number']) && !empty($data['tracking_number'])) {
            $query = "UPDATE shipping SET shipping_status = ?, tracking_number = ?, handled_by = ? 
                     WHERE order_id = ?";
            $db->update($query, [
                $data['shipping_status'],
                $data['tracking_number'],
                $data['handled_by'] ?? null,
                $data['order_id']
            ]);
        } else {
            $query = "UPDATE shipping SET shipping_status = ?, handled_by = ? WHERE order_id = ?";
            $db->update($query, [
                $data['shipping_status'],
                $data['handled_by'] ?? null,
                $data['order_id']
            ]);
        }
        
        // Update order status based on shipping status
        $orderStatusMap = [
            'Pending' => 'Pending',
            'Processing' => 'Processing',
            'Shipped' => 'Shipped',
            'Delivered' => 'Delivered'
        ];
        $orderStatus = $orderStatusMap[$data['shipping_status']];
        $orderQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $db->update($orderQuery, [$orderStatus, $data['order_id']]);
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Shipping status updated successfully'
        ]);
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function updateShippingRecord($db, $orderId) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $query = "UPDATE shipping SET shipping_status = ?, tracking_number = ? 
                 WHERE order_id = ?";
        $db->update($query, [
            $data['shipping_status'] ?? 'Pending',
            $data['tracking_number'] ?? null,
            $orderId
        ]);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Shipping record updated successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>