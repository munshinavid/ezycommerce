<?php
// vendor_dashboard_api.php
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
        case 'get_stats':
            if ($method === 'GET') {
                getVendorStats($db, $vendor_id);
            }
            break;
            
        case 'get_recent_orders':
            if ($method === 'GET') {
                getRecentOrders($db, $vendor_id);
            }
            break;
            
        case 'get_low_stock':
            if ($method === 'GET') {
                getLowStockProducts($db, $vendor_id);
            }
            break;
            
        case 'get_products':
            if ($method === 'GET') {
                getProducts($db, $vendor_id);
            }
            break;
            
        case 'get_product':
            if ($method === 'GET') {
                $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
                getProductById($db, $vendor_id, $product_id);
            }
            break;
            
        case 'save_product':
            if ($method === 'POST') {
                saveProduct($db, $vendor_id);
            }
            break;
            
        case 'update_stock':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                updateStock($db, $vendor_id, $data);
            }
            break;
            
        case 'get_vendor_info':
            if ($method === 'GET') {
                getVendorInfo($db, $vendor_id);
            }
            break;
            
        case 'get_categories':
            if ($method === 'GET') {
                getCategories($db);
            }
            break;
            
        default:
            sendResponse(400, ['error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    sendResponse(500, ['error' => $e->getMessage()]);
}

/**
 * Get vendor statistics
 */
function getVendorStats($db, $vendor_id) {
    // Total products
    $productsQuery = "SELECT COUNT(*) as total FROM products WHERE vendor_id = ? AND is_active = 1";
    $totalProducts = $db->select($productsQuery, [$vendor_id]);
    
    // Pending orders (orders with vendor's products in Pending status)
    $pendingQuery = "
        SELECT COUNT(DISTINCT oi.order_id) as total
        FROM order_items oi
        INNER JOIN products p ON oi.product_id = p.product_id
        WHERE p.vendor_id = ? AND oi.vendor_status = 'Pending'
    ";
    $pendingOrders = $db->select($pendingQuery, [$vendor_id]);
    
    // Monthly revenue (current month)
    $revenueQuery = "
        SELECT SUM(oi.price_at_purchase * oi.quantity) as revenue
        FROM order_items oi
        INNER JOIN products p ON oi.product_id = p.product_id
        INNER JOIN orders o ON oi.order_id = o.order_id
        WHERE p.vendor_id = ? 
        AND MONTH(o.created_at) = MONTH(CURRENT_DATE())
        AND YEAR(o.created_at) = YEAR(CURRENT_DATE())
        AND o.order_status != 'Cancelled'
    ";
    $monthlyRevenue = $db->select($revenueQuery, [$vendor_id]);
    
    // Low stock items (stock < 20)
    $lowStockQuery = "
        SELECT COUNT(*) as total 
        FROM products 
        WHERE vendor_id = ? AND stock < 20 AND is_active = 1
    ";
    $lowStockItems = $db->select($lowStockQuery, [$vendor_id]);
    
    sendResponse(200, [
        'totalProducts' => (int)$totalProducts[0]['total'],
        'pendingOrders' => (int)$pendingOrders[0]['total'],
        'monthlyRevenue' => number_format((float)($monthlyRevenue[0]['revenue'] ?? 0), 2, '.', ''),
        'lowStockItems' => (int)$lowStockItems[0]['total']
    ]);
}

/**
 * Get recent orders for vendor
 */
function getRecentOrders($db, $vendor_id) {
    $query = "
        SELECT DISTINCT
            o.order_id,
            o.order_status,
            o.created_at,
            u.username as customer_name,
            SUM(oi.price_at_purchase * oi.quantity) as vendor_amount
        FROM orders o
        INNER JOIN order_items oi ON o.order_id = oi.order_id
        INNER JOIN products p ON oi.product_id = p.product_id
        INNER JOIN users u ON o.customer_id = u.user_id
        WHERE p.vendor_id = ?
        GROUP BY o.order_id, o.order_status, o.created_at, u.username
        ORDER BY o.created_at DESC
        LIMIT 5
    ";
    
    $orders = $db->select($query, [$vendor_id]);
    
    $formattedOrders = [];
    foreach ($orders as $order) {
        $formattedOrders[] = [
            'id' => 'ORD-' . $order['order_id'],
            'customer' => $order['customer_name'],
            'date' => $order['created_at'],
            'amount' => number_format((float)$order['vendor_amount'], 2, '.', ''),
            'status' => $order['order_status']
        ];
    }
    
    sendResponse(200, $formattedOrders);
}

/**
 * Get low stock products
 */
function getLowStockProducts($db, $vendor_id) {
    $query = "
        SELECT 
            product_id,
            name,
            CONCAT('SKU-', product_id) as sku,
            stock
        FROM products
        WHERE vendor_id = ? AND stock < 20 AND is_active = 1
        ORDER BY stock ASC
        LIMIT 10
    ";
    
    $products = $db->select($query, [$vendor_id]);
    
    $formattedProducts = [];
    foreach ($products as $product) {
        $formattedProducts[] = [
            'id' => $product['product_id'],
            'name' => $product['name'],
            'sku' => $product['sku'],
            'stock' => (int)$product['stock']
        ];
    }
    
    sendResponse(200, $formattedProducts);
}

/**
 * Get all vendor products
 */
function getProducts($db, $vendor_id) {
    $query = "
        SELECT 
            p.product_id,
            p.name,
            CONCAT('SKU-', p.product_id) as sku,
            p.price,
            p.stock,
            p.image_url,
            c.category_name as category
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.vendor_id = ? AND p.is_active = 1
        ORDER BY p.product_id DESC
    ";
    
    $products = $db->select($query, [$vendor_id]);
    
    $formattedProducts = [];
    foreach ($products as $product) {
        $formattedProducts[] = [
            'id' => $product['product_id'],
            'name' => $product['name'],
            'sku' => $product['sku'],
            'category' => $product['category'] ?? 'Uncategorized',
            'price' => number_format((float)$product['price'], 2, '.', ''),
            'stock' => (int)$product['stock'],
            'image' => $product['image_url'] ?: 'https://via.placeholder.com/40'
        ];
    }
    
    sendResponse(200, $formattedProducts);
}

/**
 * Get single product by ID (for editing)
 */
function getProductById($db, $vendor_id, $product_id) {
    $query = "
        SELECT 
            p.product_id,
            p.name,
            p.description,
            p.price,
            p.stock,
            p.image_url,
            p.category_id,
            c.category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = ? AND p.vendor_id = ? AND p.is_active = 1
    ";
    
    $result = $db->select($query, [$product_id, $vendor_id]);
    
    if (empty($result)) {
        sendResponse(404, ['error' => 'Product not found']);
        return;
    }
    
    $product = $result[0];
    
    sendResponse(200, [
        'id' => $product['product_id'],
        'name' => $product['name'],
        'description' => $product['description'],
        'price' => number_format((float)$product['price'], 2, '.', ''),
        'stock' => (int)$product['stock'],
        'image_url' => $product['image_url'],
        'category_id' => $product['category_id'],
        'category_name' => $product['category_name']
    ]);
}

/**
 * Save product (add or update)
 */
function saveProduct($db, $vendor_id) {
    // Get form data
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    
    // Validate required fields
    if (empty($name) || $price <= 0 || $stock < 0) {
        sendResponse(400, ['error' => 'Invalid product data']);
        return;
    }
    
    // Handle image upload
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_url = handleImageUpload($_FILES['image']);
        if (!$image_url) {
            sendResponse(400, ['error' => 'Failed to upload image']);
            return;
        }
    }
    
    $db->beginTransaction();
    
    try {
        if ($product_id) {
            // UPDATE existing product
            // First, get current product data
            $currentQuery = "SELECT * FROM products WHERE product_id = ? AND vendor_id = ?";
            $currentProduct = $db->select($currentQuery, [$product_id, $vendor_id]);
            
            if (empty($currentProduct)) {
                throw new Exception('Product not found or access denied');
            }
            
            $current = $currentProduct[0];
            
            // Build update query dynamically - only update changed fields
            $updateFields = [];
            $updateParams = [];
            
            if ($name !== $current['name']) {
                $updateFields[] = "name = ?";
                $updateParams[] = $name;
            }
            
            if ($category_id !== (int)$current['category_id']) {
                $updateFields[] = "category_id = ?";
                $updateParams[] = $category_id;
            }
            
            if ($description !== $current['description']) {
                $updateFields[] = "description = ?";
                $updateParams[] = $description;
            }
            
            if ($price !== (float)$current['price']) {
                $updateFields[] = "price = ?";
                $updateParams[] = $price;
            }
            
            if ($stock !== (int)$current['stock']) {
                $updateFields[] = "stock = ?";
                $updateParams[] = $stock;
            }
            
            // Only update image if new one was uploaded
            if ($image_url) {
                $updateFields[] = "image_url = ?";
                $updateParams[] = $image_url;
                
                // Delete old image file if it exists
                if ($current['image_url'] && file_exists($current['image_url'])) {
                    unlink($current['image_url']);
                }
            }
            
            // If nothing changed, just return success
            if (empty($updateFields)) {
                $db->commit();
                sendResponse(200, [
                    'success' => true,
                    'message' => 'No changes detected',
                    'product_id' => $product_id
                ]);
                return;
            }
            
            // Add WHERE clause parameters
            $updateParams[] = $product_id;
            $updateParams[] = $vendor_id;
            
            $updateQuery = "UPDATE products SET " . implode(', ', $updateFields) . 
                          " WHERE product_id = ? AND vendor_id = ?";
            
            $db->update($updateQuery, $updateParams);
            
            $db->commit();
            
            sendResponse(200, [
                'success' => true,
                'message' => 'Product updated successfully',
                'product_id' => $product_id
            ]);
            
        } else {
            // INSERT new product
            if (!$image_url) {
                throw new Exception('Image is required for new products');
            }
            
            $insertQuery = "
                INSERT INTO products (name, description, price, stock, image_url, category_id, vendor_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";
            
            $new_id = $db->insert($insertQuery, [
                $name, $description, $price, $stock, $image_url, $category_id, $vendor_id
            ]);
            
            $db->commit();
            
            sendResponse(200, [
                'success' => true,
                'message' => 'Product added successfully',
                'product_id' => $new_id
            ]);
        }
        
    } catch (Exception $e) {
        $db->rollback();
        
        // Delete uploaded image if transaction failed
        if ($image_url && file_exists($image_url)) {
            unlink($image_url);
        }
        
        sendResponse(400, ['error' => $e->getMessage()]);
    }
}

/**
 * Update product stock
 */
function updateStock($db, $vendor_id, $data) {
    $product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
    $new_stock = isset($data['new_stock']) ? (int)$data['new_stock'] : 0;
    $reason = isset($data['reason']) ? trim($data['reason']) : '';
    
    if (!$product_id || $new_stock < 0) {
        sendResponse(400, ['error' => 'Invalid data']);
        return;
    }
    
    // Verify product belongs to vendor
    $checkQuery = "SELECT product_id FROM products WHERE product_id = ? AND vendor_id = ?";
    $result = $db->select($checkQuery, [$product_id, $vendor_id]);
    
    if (empty($result)) {
        sendResponse(404, ['error' => 'Product not found or access denied']);
        return;
    }
    
    // Update stock
    $updateQuery = "UPDATE products SET stock = ? WHERE product_id = ? AND vendor_id = ?";
    $db->update($updateQuery, [$new_stock, $product_id, $vendor_id]);
    
    // Log stock change (optional - you might want a stock_history table)
    error_log("Stock updated for product $product_id by vendor $vendor_id: $new_stock (Reason: $reason)");
    
    sendResponse(200, [
        'success' => true,
        'message' => 'Stock updated successfully',
        'new_stock' => $new_stock
    ]);
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
 * Get all categories
 */
function getCategories($db) {
    $query = "SELECT category_id, category_name FROM categories ORDER BY category_name";
    $categories = $db->select($query);
    
    sendResponse(200, $categories);
}

/**
 * Handle image upload
 */
function handleImageUpload($file) {
    // Define upload directory
    $upload_dir = 'uploads/products/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    // Validate file size (2MB max)
    if ($file['size'] > 2 * 1024 * 1024) {
        return false;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('product_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    }
    
    return false;
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