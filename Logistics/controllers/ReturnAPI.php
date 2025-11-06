<?php
// returns_api.php - API for Returns Management Dashboard
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
        getReturnsList($db, $status);
    }
    elseif ($method === 'GET' && $action === 'counts') {
        getReturnsCounts($db);
    }
    elseif ($method === 'GET' && $action === 'details') {
        getReturnDetails($db);
    }
    elseif ($method === 'POST' && $action === 'process') {
        processReturn($db);
    }
    elseif ($method === 'POST' && $action === 'bulk-update') {
        bulkUpdateReturns($db);
    }
    elseif ($method === 'GET' && $action === 'export') {
        exportReturns($db, $status);
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
// GET RETURNS LIST
// ============================================
function getReturnsList($db, $status) {
    try {
        $query = "SELECT r.return_id, r.order_id, r.reason, r.status, r.processed_at,
                         o.total_amount, o.created_at as order_date,
                         u.username as customer_name, u.email as customer_email,
                         (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as items_count
                  FROM returns r
                  JOIN orders o ON r.order_id = o.order_id
                  JOIN users u ON o.customer_id = u.user_id";
        
        $params = [];
        
        // Filter by status if not 'all'
        if ($status !== 'all') {
            $query .= " WHERE r.status = ?";
            $params[] = ucfirst($status);
        }
        
        $query .= " ORDER BY r.processed_at DESC";
        
        $returns = $db->select($query, $params);
        
        // Format returns for frontend
        $formattedReturns = array_map(function($return) {
            return [
                'id' => 'RET-' . str_pad($return['return_id'], 4, '0', STR_PAD_LEFT),
                'orderId' => 'ORD-' . str_pad($return['order_id'], 4, '0', STR_PAD_LEFT),
                'customer' => $return['customer_name'],
                'email' => $return['customer_email'],
                'date' => $return['processed_at'],
                'orderDate' => $return['order_date'],
                'reason' => $return['reason'] ?? 'No reason provided',
                'status' => strtolower($return['status']),
                'orderAmount' => (float)$return['total_amount'],
                'itemsCount' => (int)$return['items_count']
            ];
        }, $returns);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $formattedReturns,
            'total' => count($formattedReturns)
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// GET RETURNS COUNTS BY STATUS
// ============================================
function getReturnsCounts($db) {
    try {
        $query = "SELECT status, COUNT(*) as count FROM returns GROUP BY status";
        $result = $db->select($query);
        
        $counts = [
            'all' => 0,
            'pending' => 0,
            'processing' => 0,
            'approved' => 0,
            'rejected' => 0
        ];
        
        foreach ($result as $row) {
            $status = strtolower($row['status']);
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
// GET RETURN DETAILS
// ============================================
function getReturnDetails($db) {
    try {
        $returnId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$returnId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Return ID is required']);
            return;
        }
        
        // Extract numeric ID from format like "RET-1001"
        $numericId = (int)str_replace('RET-', '', $returnId);
        
        // Get return details
        $returnQuery = "SELECT r.*, 
                              o.total_amount, o.created_at as order_date, o.order_status,
                              u.username as customer_name, u.email as customer_email,
                              cd.phone as customer_phone, cd.address as customer_address,
                              handler.username as handler_name
                       FROM returns r
                       JOIN orders o ON r.order_id = o.order_id
                       JOIN users u ON o.customer_id = u.user_id
                       LEFT JOIN customerdetails cd ON u.user_id = cd.user_id
                       LEFT JOIN users handler ON r.handled_by = handler.user_id
                       WHERE r.return_id = ?";
        
        $return = $db->select($returnQuery, [$numericId]);
        
        if (empty($return)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Return not found']);
            return;
        }
        
        $returnData = $return[0];
        
        // Get order items
        $itemsQuery = "SELECT oi.quantity, oi.price_at_purchase, 
                             p.name as product_name, p.product_id
                       FROM order_items oi
                       JOIN products p ON oi.product_id = p.product_id
                       WHERE oi.order_id = ?";
        
        $items = $db->select($itemsQuery, [$returnData['order_id']]);
        
        // Get payment info
        $paymentQuery = "SELECT method, status, amount FROM payments WHERE order_id = ?";
        $payment = $db->select($paymentQuery, [$returnData['order_id']]);
        
        // Format response
        $response = [
            'id' => 'RET-' . str_pad($returnData['return_id'], 4, '0', STR_PAD_LEFT),
            'orderId' => 'ORD-' . str_pad($returnData['order_id'], 4, '0', STR_PAD_LEFT),
            'customer' => [
                'name' => $returnData['customer_name'],
                'email' => $returnData['customer_email'],
                'phone' => $returnData['customer_phone'] ?? '(555) 123-4567',
                'address' => $returnData['customer_address'] ?? 'No address provided'
            ],
            'date' => $returnData['processed_at'],
            'status' => $returnData['status'],
            'reason' => $returnData['reason'] ?? 'No reason provided',
            'orderDate' => $returnData['order_date'],
            'orderStatus' => $returnData['order_status'],
            'orderAmount' => (float)$returnData['total_amount'],
            'handlerName' => $returnData['handler_name'] ?? 'Not assigned',
            'payment' => !empty($payment) ? [
                'method' => $payment[0]['method'],
                'status' => $payment[0]['status'],
                'amount' => (float)$payment[0]['amount']
            ] : null,
            'items' => array_map(function($item) {
                return [
                    'productId' => (int)$item['product_id'],
                    'name' => $item['product_name'],
                    'quantity' => (int)$item['quantity'],
                    'price' => (float)$item['price_at_purchase'],
                    'total' => (float)$item['quantity'] * (float)$item['price_at_purchase']
                ];
            }, $items),
            'refundAmount' => (float)$returnData['total_amount'] // Full refund by default
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
// PROCESS RETURN
// ============================================
function processReturn($db) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['return_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Return ID and status are required']);
            return;
        }
        
        // Extract numeric ID from format like "RET-1001"
        $numericId = (int)str_replace('RET-', '', $data['return_id']);
        
        // Validate status
        $validStatuses = ['Pending', 'Processing', 'Approved', 'Rejected'];
        if (!in_array($data['status'], $validStatuses)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            return;
        }
        
        // Get handler ID
        $handlerId = getHandlerId();
        
        $db->beginTransaction();
        
        // Get return and order info
        $returnInfo = $db->select(
            "SELECT r.order_id, o.total_amount 
             FROM returns r 
             JOIN orders o ON r.order_id = o.order_id 
             WHERE r.return_id = ?", 
            [$numericId]
        );
        
        if (empty($returnInfo)) {
            throw new Exception('Return not found');
        }
        
        $orderId = $returnInfo[0]['order_id'];
        $orderAmount = $returnInfo[0]['total_amount'];
        
        // Update return status
        $updateQuery = "UPDATE returns SET status = ?, handled_by = ? WHERE return_id = ?";
        $db->update($updateQuery, [$data['status'], $handlerId, $numericId]);
        
        // Handle based on status
        if ($data['status'] === 'Approved') {
            // Update order status to indicate return approved
            $db->update(
                "UPDATE orders SET order_status = 'Cancelled' WHERE order_id = ?",
                [$orderId]
            );
            
            // Restore stock for returned items
            $items = $db->select(
                "SELECT product_id, quantity FROM order_items WHERE order_id = ?",
                [$orderId]
            );
            
            foreach ($items as $item) {
                $db->update(
                    "UPDATE products SET stock = stock + ? WHERE product_id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }
            
            // Process refund if provided
            $refundAmount = isset($data['refund_amount']) ? (float)$data['refund_amount'] : (float)$orderAmount;
            
            // Update payment status
            $db->update(
                "UPDATE payments SET status = 'Completed', amount = ? WHERE order_id = ?",
                [$refundAmount, $orderId]
            );
        }
        elseif ($data['status'] === 'Rejected') {
            // Update order status back to delivered
            $db->update(
                "UPDATE orders SET order_status = 'Delivered' WHERE order_id = ?",
                [$orderId]
            );
        }
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => "Return {$data['return_id']} has been {$data['status']}"
        ]);
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// BULK UPDATE RETURNS
// ============================================
function bulkUpdateReturns($db) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['return_ids']) || !isset($data['status']) || empty($data['return_ids'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Return IDs and status are required']);
            return;
        }
        
        // Validate status
        $validStatuses = ['Pending', 'Processing', 'Approved', 'Rejected'];
        if (!in_array($data['status'], $validStatuses)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            return;
        }
        
        // Get handler ID
        $handlerId = getHandlerId();
        
        $db->beginTransaction();
        
        $successCount = 0;
        $failedReturns = [];
        
        foreach ($data['return_ids'] as $returnId) {
            try {
                // Extract numeric ID
                $numericId = (int)str_replace('RET-', '', $returnId);
                
                // Get return and order info
                $returnInfo = $db->select(
                    "SELECT r.order_id, o.total_amount 
                     FROM returns r 
                     JOIN orders o ON r.order_id = o.order_id 
                     WHERE r.return_id = ?", 
                    [$numericId]
                );
                
                if (empty($returnInfo)) {
                    throw new Exception('Return not found');
                }
                
                $orderId = $returnInfo[0]['order_id'];
                
                // Update return status
                $db->update(
                    "UPDATE returns SET status = ?, handled_by = ? WHERE return_id = ?",
                    [$data['status'], $handlerId, $numericId]
                );
                
                // Handle based on status
                if ($data['status'] === 'Approved') {
                    // Update order status
                    $db->update(
                        "UPDATE orders SET order_status = 'Cancelled' WHERE order_id = ?",
                        [$orderId]
                    );
                    
                    // Restore stock
                    $items = $db->select(
                        "SELECT product_id, quantity FROM order_items WHERE order_id = ?",
                        [$orderId]
                    );
                    
                    foreach ($items as $item) {
                        $db->update(
                            "UPDATE products SET stock = stock + ? WHERE product_id = ?",
                            [$item['quantity'], $item['product_id']]
                        );
                    }
                }
                elseif ($data['status'] === 'Rejected') {
                    // Update order status back to delivered
                    $db->update(
                        "UPDATE orders SET order_status = 'Delivered' WHERE order_id = ?",
                        [$orderId]
                    );
                }
                
                $successCount++;
            } catch (Exception $e) {
                $failedReturns[] = $returnId;
            }
        }
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => "Successfully updated {$successCount} returns",
            'updated_count' => $successCount,
            'failed_returns' => $failedReturns
        ]);
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ============================================
// EXPORT RETURNS
// ============================================
function exportReturns($db, $status) {
    try {
        $query = "SELECT r.return_id, r.order_id, r.reason, r.status, r.processed_at,
                         o.total_amount,
                         u.username as customer_name, u.email as customer_email
                  FROM returns r
                  JOIN orders o ON r.order_id = o.order_id
                  JOIN users u ON o.customer_id = u.user_id";
        
        $params = [];
        
        if ($status !== 'all') {
            $query .= " WHERE r.status = ?";
            $params[] = ucfirst($status);
        }
        
        $query .= " ORDER BY r.processed_at DESC";
        
        $returns = $db->select($query, $params);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="returns_export_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['Return ID', 'Order ID', 'Customer', 'Email', 'Date', 'Reason', 'Status', 'Order Amount']);
        
        // CSV data
        foreach ($returns as $return) {
            fputcsv($output, [
                'RET-' . str_pad($return['return_id'], 4, '0', STR_PAD_LEFT),
                'ORD-' . str_pad($return['order_id'], 4, '0', STR_PAD_LEFT),
                $return['customer_name'],
                $return['customer_email'],
                $return['processed_at'],
                $return['reason'] ?? 'N/A',
                $return['status'],
                '$' . number_format($return['total_amount'], 2)
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