<?php
// vendor_orders_api.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../models/Database.php';

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Mock vendor_id - In production, get this from session
$vendor_id = isset($_GET['vendor_id']) ? (int)$_GET['vendor_id'] : 1;

try {
    $db = new Database();
    
    switch ($action) {
        case 'get_orders':
            if ($method === 'GET') {
                getVendorOrders($db, $vendor_id);
            }
            break;
            
        case 'update_status':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                updateOrderStatus($db, $vendor_id, $data);
            }
            break;
            
        case 'get_vendor_info':
            if ($method === 'GET') {
                getVendorInfo($db, $vendor_id);
            }
            break;
            
        default:
            sendResponse(400, ['error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    sendResponse(500, ['error' => $e->getMessage()]);
}

/**
 * Get all orders containing vendor's products
 */
function getVendorOrders($db, $vendor_id) {
    // Get all orders that contain at least one product from this vendor
    $query = "
        SELECT DISTINCT
            o.order_id,
            o.order_status,
            o.total_amount,
            o.created_at as order_date,
            u.username as customer_name,
            u.email as customer_email,
            cd.full_name,
            cd.address,
            cd.phone
        FROM orders o
        INNER JOIN order_items oi ON o.order_id = oi.order_id
        INNER JOIN products p ON oi.product_id = p.product_id
        INNER JOIN users u ON o.customer_id = u.user_id
        LEFT JOIN customerdetails cd ON u.user_id = cd.user_id
        WHERE p.vendor_id = ?
        ORDER BY o.created_at DESC
    ";
    
    $orders = $db->select($query, [$vendor_id]);
    
    // For each order, get only the vendor's products
    foreach ($orders as &$order) {
        $order['id'] = 'ORD-' . $order['order_id'];
        $order['date'] = $order['order_date'];
        
        // Map order status to frontend format
        $order['status'] = mapOrderStatus($order['order_status']);
        
        // Get vendor's products in this order
        $productsQuery = "
            SELECT 
                p.product_id,
                p.name,
                p.image_url as image,
                oi.quantity,
                oi.price_at_purchase as price,
                oi.vendor_status,
                CONCAT('SKU-', p.product_id) as sku
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ? AND p.vendor_id = ?
        ";
        
        $products = $db->select($productsQuery, [$order['order_id'], $vendor_id]);
        
        // Calculate vendor total
        $vendorTotal = 0;
        foreach ($products as &$product) {
            $product['image'] = $product['image'] ?: 'https://via.placeholder.com/100';
            $vendorTotal += $product['price'] * $product['quantity'];
        }
        
        $order['vendor_products'] = $products;
        $order['vendor_total'] = $vendorTotal;
        
        // Check if vendor can still update status
        $order['can_update_status'] = canVendorUpdateStatus($products);
        
        // Customer info
        $order['customer'] = [
            'name' => $order['full_name'] ?: $order['customer_name'],
            'email' => $order['customer_email'],
            'address' => $order['address'] ?? '',
            'phone' => $order['phone'] ?? ''
        ];
        
        // Clean up
        unset($order['order_id'], $order['order_status'], $order['total_amount'], 
             $order['order_date'], $order['customer_name'], $order['customer_email'],
             $order['full_name'], $order['address'], $order['phone']);
    }
    
    sendResponse(200, ['orders' => $orders]);
}

/**
 * Update order status for vendor's products
 */
function updateOrderStatus($db, $vendor_id, $data) {
    $order_id = isset($data['order_id']) ? str_replace('ORD-', '', $data['order_id']) : null;
    $new_status = isset($data['status']) ? $data['status'] : null;
    $cancel_reason = isset($data['cancel_reason']) ? $data['cancel_reason'] : '';
    
    if (!$order_id || !$new_status) {
        sendResponse(400, ['error' => 'Missing required fields']);
        return;
    }
    
    // Map frontend status to database enum
    $vendorStatus = mapToVendorStatus($new_status);
    
    if (!$vendorStatus) {
        sendResponse(400, ['error' => 'Invalid status']);
        return;
    }
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Check if vendor's products in this order are already marked as Ready to Ship
        $checkQuery = "
            SELECT oi.vendor_status
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ? AND p.vendor_id = ?
            LIMIT 1
        ";
        
        $currentStatus = $db->select($checkQuery, [$order_id, $vendor_id]);
        
        if (empty($currentStatus)) {
            throw new Exception('Order not found or does not belong to this vendor');
        }
        
        // Check if already marked as Ready to Ship
        if ($currentStatus[0]['vendor_status'] === 'ReadyToShip') {
            throw new Exception('Cannot update status. Order items are already marked as Ready to Ship and have been handed over to logistics.');
        }
        
        // Update vendor_status for all order items belonging to this vendor
        $updateQuery = "
            UPDATE order_items oi
            INNER JOIN products p ON oi.product_id = p.product_id
            SET oi.vendor_status = ?
            WHERE oi.order_id = ? AND p.vendor_id = ?
        ";
        
        $db->update($updateQuery, [$vendorStatus, $order_id, $vendor_id]);
        
        // If marked as Ready to Ship, create/update shipping record
        if ($vendorStatus === 'ReadyToShip') {
            // Check if all vendors' items in this order are Ready to Ship
            $allItemsQuery = "
                SELECT COUNT(*) as total,
                       SUM(CASE WHEN vendor_status = 'ReadyToShip' THEN 1 ELSE 0 END) as ready
                FROM order_items
                WHERE order_id = ?
            ";
            
            $itemsStatus = $db->select($allItemsQuery, [$order_id]);
            
            // If all items are ready, update order status to Processing
            if ($itemsStatus[0]['total'] == $itemsStatus[0]['ready']) {
                $updateOrderQuery = "UPDATE orders SET order_status = 'Processing' WHERE order_id = ?";
                $db->update($updateOrderQuery, [$order_id]);
                
                // Create or update shipping record
                $shippingCheckQuery = "SELECT shipping_id FROM shipping WHERE order_id = ?";
                $shippingExists = $db->select($shippingCheckQuery, [$order_id]);
                
                if (empty($shippingExists)) {
                    $insertShippingQuery = "
                        INSERT INTO shipping (order_id, shipping_status, tracking_number)
                        VALUES (?, 'Processing', ?)
                    ";
                    $tracking = 'TRK-' . $order_id . '-' . time();
                    $db->insert($insertShippingQuery, [$order_id, $tracking]);
                }
            }
        }
        
        // If cancelled, log the reason (you might want a separate table for this)
        if ($vendorStatus === 'Cancelled' && $cancel_reason) {
            // You could add a cancellation_reasons table or add reason to order_items
            // For now, we'll just log it
            error_log("Order $order_id cancelled by vendor $vendor_id. Reason: $cancel_reason");
        }
        
        $db->commit();
        
        sendResponse(200, [
            'success' => true,
            'message' => 'Order status updated successfully',
            'new_status' => $new_status
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        sendResponse(400, ['error' => $e->getMessage()]);
    }
}

/**
 * Get vendor information
 */
function getVendorInfo($db, $vendor_id) {
    $query = "
        SELECT 
            v.vendor_id,
            v.vendor_name as name,
            v.contact_email as email,
            u.email as login_email
        FROM vendors v
        INNER JOIN users u ON v.user_id = u.user_id
        WHERE v.vendor_id = ?
    ";
    
    $vendor = $db->select($query, [$vendor_id]);
    
    if (empty($vendor)) {
        sendResponse(404, ['error' => 'Vendor not found']);
        return;
    }
    
    sendResponse(200, $vendor[0]);
}

/**
 * Map database order status to frontend format
 */
function mapOrderStatus($dbStatus) {
    $statusMap = [
        'Pending' => 'pending',
        'Processing' => 'ready-to-ship',
        'Shipped' => 'shipped',
        'Delivered' => 'delivered',
        'Cancelled' => 'cancelled'
    ];
    
    return $statusMap[$dbStatus] ?? 'pending';
}

/**
 * Map frontend status to database vendor_status enum
 */
function mapToVendorStatus($frontendStatus) {
    $statusMap = [
        'pending' => 'Pending',
        'ready-to-ship' => 'ReadyToShip',
        'cancelled' => 'Cancelled'
    ];
    
    return $statusMap[$frontendStatus] ?? null;
}

/**
 * Check if vendor can update status based on current vendor_status
 */
function canVendorUpdateStatus($products) {
    foreach ($products as $product) {
        if ($product['vendor_status'] === 'ReadyToShip') {
            return false;
        }
    }
    return true;
}

/**
 * Send JSON response
 */
function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}
?>