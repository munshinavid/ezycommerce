<?php
// orders_api.php - API for Orders Management Dashboard
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../models/Database.php';

// Parse the request
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

$db = new Database();

// Get handler ID from session
function getHandlerId() {
    // In real implementation: return $_SESSION['user_id'] ?? null;
    return 5; // Demo handler ID
}

try {
    // Route requests
    if ($method === 'GET' && $action === 'list') {
        getOrdersList($db, $status);
    }
    elseif ($method === 'GET' && $action === 'counts') {
        getOrdersCounts($db);
    }
    elseif ($method === 'GET' && $action === 'details') {
        getOrderDetails($db);
    }
    elseif ($method === 'POST' && $action === 'update-status') {
        updateOrderStatus($db);
    }
    elseif ($method === 'POST' && $action === 'bulk-update') {
        bulkUpdateOrders($db);
    }
    elseif ($method === 'GET' && $action === 'export') {
        exportOrders($db, $status);
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
// GET ORDERS LIST
// ============================================
function getOrdersList($db, $status) {
    try {
        $query = "SELECT o.order_id, o.order_status, o.total_amount, o.created_at,
                         u.username as customer_name, u.email as customer_email,
                         s.tracking_number, s.shipping_status,
                         (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as items_count
                  FROM orders o
                  JOIN users u ON o.customer_id = u.user_id
                  LEFT JOIN shipping s ON o.order_id = s.order_id";
        
        $params = [];
        
        // Filter by status if not 'all'
        if ($status !== 'all') {
            $query .= " WHERE o.order_status = ?";
            $params[] = ucfirst($status);
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        $orders = $db->select($query, $params);
        
        // Format orders for frontend
        $formattedOrders = array_map(function($order) {
            return [
                'id' => 'ORD-' . str_pad($order['order_id'], 4, '0', STR_PAD_LEFT),
                'customer' => $order['customer_name'],
                'email' => $order['customer_email'],
                'date' => $order['created_at'],
                'items' => (int)$order['items_count'],
                'amount' => (float)$order['total_amount'],
                'status' => strtolower($order['order_status']),
                'tracking' => $order['tracking_number'] ?? null,
                'shipping_status' => $order['shipping_status'] ?? null
            ];
        }, $orders);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $formattedOrders,
            'total' => count($formattedOrders)
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// GET ORDERS COUNTS BY STATUS
// ============================================
function getOrdersCounts($db) {
    try {
        $query = "SELECT order_status, COUNT(*) as count FROM orders GROUP BY order_status";
        $result = $db->select($query);
        
        $counts = [
            'all' => 0,
            'pending' => 0,
            'processing' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0
        ];
        
        foreach ($result as $row) {
            $status = strtolower($row['order_status']);
            $counts[$status] = (int)$row['count'];
            $counts['all'] += (int)$row['count'];
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $counts
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// GET ORDER DETAILS
// ============================================
function getOrderDetails($db) {
    try {
        $orderId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$orderId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Order ID is required']);
            return;
        }
        
        // Extract numeric ID from format like "ORD-1001"
        $numericId = (int)str_replace('ORD-', '', $orderId);
        
        // Get order details
        $orderQuery = "SELECT o.*, u.username as customer_name, u.email as customer_email,
                              cd.phone as customer_phone, cd.address as shipping_address,
                              s.tracking_number, s.shipping_status, s.updated_at as shipping_updated,
                              p.method as payment_method, p.status as payment_status
                       FROM orders o
                       JOIN users u ON o.customer_id = u.user_id
                       LEFT JOIN customerdetails cd ON u.user_id = cd.user_id
                       LEFT JOIN shipping s ON o.order_id = s.order_id
                       LEFT JOIN payments p ON o.order_id = p.order_id
                       WHERE o.order_id = ?";
        
        $order = $db->select($orderQuery, [$numericId]);
        
        if (empty($order)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Order not found']);
            return;
        }
        
        $orderData = $order[0];
        
        // Get order items
        $itemsQuery = "SELECT oi.quantity, oi.price_at_purchase, p.name as product_name
                       FROM order_items oi
                       JOIN products p ON oi.product_id = p.product_id
                       WHERE oi.order_id = ?";
        
        $items = $db->select($itemsQuery, [$numericId]);
        
        // Calculate subtotal and shipping
        $subtotal = (float)$orderData['total_amount'] * 0.9;
        $shippingCost = (float)$orderData['total_amount'] * 0.1;
        
        // Format response
        $response = [
            'id' => 'ORD-' . str_pad($orderData['order_id'], 4, '0', STR_PAD_LEFT),
            'customer' => [
                'name' => $orderData['customer_name'],
                'email' => $orderData['customer_email'],
                'phone' => $orderData['customer_phone'] ?? '(555) 123-4567'
            ],
            'date' => $orderData['created_at'],
            'status' => $orderData['order_status'],
            'shipping' => $orderData['shipping_address'] ?? 'No address provided',
            'tracking' => $orderData['tracking_number'] ?? null,
            'shipping_status' => $orderData['shipping_status'] ?? 'Pending',
            'payment' => [
                'method' => $orderData['payment_method'] ?? 'Cash on Delivery',
                'status' => $orderData['payment_status'] ?? 'Pending'
            ],
            'items' => array_map(function($item) {
                return [
                    'name' => $item['product_name'],
                    'quantity' => (int)$item['quantity'],
                    'price' => (float)$item['price_at_purchase'],
                    'total' => (float)$item['quantity'] * (float)$item['price_at_purchase']
                ];
            }, $items),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => (float)$orderData['total_amount']
        ];
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $response
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// UPDATE ORDER STATUS
// ============================================
function updateOrderStatus($db) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['order_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Order ID and status are required']);
            return;
        }
        
        // Extract numeric ID from format like "ORD-1001"
        $numericId = (int)str_replace('ORD-', '', $data['order_id']);
        
        // Validate status
        $validStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
        if (!in_array($data['status'], $validStatuses)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            return;
        }
        
        // Get handler ID
        $handlerId = getHandlerId();
        
        $db->beginTransaction();
        
        // Update order status
        $orderQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $db->update($orderQuery, [$data['status'], $numericId]);
        
        // Map order status to shipping status
        $shippingStatusMap = [
            'Pending' => 'Pending',
            'Processing' => 'Processing',
            'Shipped' => 'Shipped',
            'Delivered' => 'Delivered',
            'Cancelled' => 'Pending'
        ];
        $shippingStatus = $shippingStatusMap[$data['status']];
        
        // Update shipping status
        if (isset($data['tracking_number']) && !empty($data['tracking_number'])) {
            $shippingQuery = "UPDATE shipping 
                            SET shipping_status = ?, tracking_number = ?, handled_by = ? 
                            WHERE order_id = ?";
            $db->update($shippingQuery, [
                $shippingStatus,
                $data['tracking_number'],
                $handlerId,
                $numericId
            ]);
        } else {
            $shippingQuery = "UPDATE shipping 
                            SET shipping_status = ?, handled_by = ? 
                            WHERE order_id = ?";
            $db->update($shippingQuery, [$shippingStatus, $handlerId, $numericId]);
        }
        
        // Update payment status if delivered
        if ($data['status'] === 'Delivered') {
            $paymentQuery = "UPDATE payments SET status = 'Completed' WHERE order_id = ?";
            $db->update($paymentQuery, [$numericId]);
        }
        
        // If cancelled, restore stock
        if ($data['status'] === 'Cancelled') {
            $itemsQuery = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
            $items = $db->select($itemsQuery, [$numericId]);
            
            foreach ($items as $item) {
                $stockQuery = "UPDATE products SET stock = stock + ? WHERE product_id = ?";
                $db->update($stockQuery, [$item['quantity'], $item['product_id']]);
            }
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

// ============================================
// BULK UPDATE ORDERS
// ============================================
function bulkUpdateOrders($db) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['order_ids']) || !isset($data['status']) || empty($data['order_ids'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Order IDs and status are required']);
            return;
        }
        
        // Validate status
        $validStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
        if (!in_array($data['status'], $validStatuses)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            return;
        }
        
        // Get handler ID
        $handlerId = getHandlerId();
        
        $db->beginTransaction();
        
        $successCount = 0;
        $failedOrders = [];
        
        foreach ($data['order_ids'] as $orderId) {
            try {
                // Extract numeric ID
                $numericId = (int)str_replace('ORD-', '', $orderId);
                
                // Update order status
                $orderQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
                $db->update($orderQuery, [$data['status'], $numericId]);
                
                // Map to shipping status
                $shippingStatusMap = [
                    'Pending' => 'Pending',
                    'Processing' => 'Processing',
                    'Shipped' => 'Shipped',
                    'Delivered' => 'Delivered',
                    'Cancelled' => 'Pending'
                ];
                $shippingStatus = $shippingStatusMap[$data['status']];
                
                // Update shipping
                $shippingQuery = "UPDATE shipping 
                                SET shipping_status = ?, handled_by = ? 
                                WHERE order_id = ?";
                $db->update($shippingQuery, [$shippingStatus, $handlerId, $numericId]);
                
                // Update payment if delivered
                if ($data['status'] === 'Delivered') {
                    $paymentQuery = "UPDATE payments SET status = 'Completed' WHERE order_id = ?";
                    $db->update($paymentQuery, [$numericId]);
                }
                
                // Restore stock if cancelled
                if ($data['status'] === 'Cancelled') {
                    $itemsQuery = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
                    $items = $db->select($itemsQuery, [$numericId]);
                    
                    foreach ($items as $item) {
                        $stockQuery = "UPDATE products SET stock = stock + ? WHERE product_id = ?";
                        $db->update($stockQuery, [$item['quantity'], $item['product_id']]);
                    }
                }
                
                $successCount++;
            } catch (Exception $e) {
                $failedOrders[] = $orderId;
            }
        }
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => "Successfully updated {$successCount} orders",
            'updated_count' => $successCount,
            'failed_orders' => $failedOrders
        ]);
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// EXPORT ORDERS
// ============================================
function exportOrders($db, $status) {
    try {
        $query = "SELECT o.order_id, o.order_status, o.total_amount, o.created_at,
                         u.username as customer_name, u.email as customer_email,
                         s.tracking_number,
                         (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as items_count
                  FROM orders o
                  JOIN users u ON o.customer_id = u.user_id
                  LEFT JOIN shipping s ON o.order_id = s.order_id";
        
        $params = [];
        
        if ($status !== 'all') {
            $query .= " WHERE o.order_status = ?";
            $params[] = ucfirst($status);
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        $orders = $db->select($query, $params);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['Order ID', 'Customer', 'Email', 'Date', 'Items', 'Amount', 'Status', 'Tracking']);
        
        // CSV data
        foreach ($orders as $order) {
            fputcsv($output, [
                'ORD-' . str_pad($order['order_id'], 4, '0', STR_PAD_LEFT),
                $order['customer_name'],
                $order['customer_email'],
                $order['created_at'],
                $order['items_count'],
                '$' . number_format($order['total_amount'], 2),
                $order['order_status'],
                $order['tracking_number'] ?? 'N/A'
            ]);
        }
        
        fclose($output);
        exit();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>