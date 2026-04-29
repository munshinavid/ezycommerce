<?php
// api/products.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../../utils/UrlHelper.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $db = new Database();
    $method = $_SERVER['REQUEST_METHOD'];
    $request = $_SERVER['REQUEST_URI'];
    
    // Parse the request
    $uri_parts = explode('/', trim($request, '/'));
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    // Resolve vendor identity from authenticated user session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        sendResponse(401, ['error' => 'Authentication required']);
        return;
    }

    $role = isset($_SESSION['user']['role']) ? strtolower((string)$_SESSION['user']['role']) : '';
    if ($role !== 'vendor') {
        sendResponse(403, ['error' => 'Vendor access only']);
        return;
    }

    $vendorLookup = $db->select(
        'SELECT vendor_id FROM vendors WHERE user_id = ?',
        [(int)$_SESSION['user']['id']]
    );

    if (empty($vendorLookup)) {
        sendResponse(403, ['error' => 'Vendor profile not found']);
        return;
    }

    $vendor_id = (int)$vendorLookup[0]['vendor_id'];
    
    switch ($method) {
        case 'GET':
            handleGet($db, $action, $vendor_id);
            break;
            
        case 'POST':
            handlePost($db, $action, $vendor_id);
            break;
            
        case 'PUT':
            handlePut($db, $vendor_id);
            break;
            
        case 'DELETE':
            handleDelete($db, $vendor_id);
            break;
            
        default:
            sendResponse(405, ['error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    sendResponse(500, ['error' => $e->getMessage()]);
}

// GET handlers
function handleGet($db, $action, $vendor_id) {
    switch ($action) {
        case 'list':
            getProducts($db, $vendor_id);
            break;
            
        case 'single':
            getProductById($db, $vendor_id);
            break;
            
        case 'stats':
            getProductStats($db, $vendor_id);
            break;
            
        default:
            getProducts($db, $vendor_id);
    }
}

// Get all products for a vendor with optional filters
function getProducts($db, $vendor_id) {
    if (!$vendor_id) {
        sendResponse(400, ['error' => 'Vendor ID is required']);
        return;
    }
    
    // Build query with filters
    $query = "SELECT 
                p.product_id,
                p.name,
                p.description,
                p.price,
                p.stock,
                p.image_url,
                p.is_active,
                c.category_name,
                d.discount_value,
                d.discount_type,
                COALESCE(
                    SUM(oi.quantity), 0
                ) as total_sales
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.category_id
              LEFT JOIN discounts d ON p.discount_id = d.discount_id
              LEFT JOIN order_items oi ON p.product_id = oi.product_id
              WHERE p.vendor_id = ?";
    
    $params = [$vendor_id];
    
    // Apply filters
    if (isset($_GET['category']) && $_GET['category'] !== 'all') {
        $query .= " AND c.category_name = ?";
        $params[] = $_GET['category'];
    }
    
    if (isset($_GET['status']) && $_GET['status'] !== 'all') {
        $is_active = $_GET['status'] === 'active' ? 1 : 0;
        $query .= " AND p.is_active = ?";
        $params[] = $is_active;
    }
    
    if (isset($_GET['stock']) && $_GET['stock'] !== 'all') {
        switch ($_GET['stock']) {
            case 'low':
                $query .= " AND p.stock < 10";
                break;
            case 'medium':
                $query .= " AND p.stock BETWEEN 10 AND 49";
                break;
            case 'high':
                $query .= " AND p.stock >= 50";
                break;
        }
    }
    
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $searchTerm = '%' . $_GET['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $query .= " GROUP BY p.product_id ORDER BY p.product_id DESC";
    
    try {
        $products = $db->select($query, $params);
        
        // Format products for frontend
        $formattedProducts = array_map(function($product) {
            return [
                'id' => $product['product_id'],
                'name' => $product['name'],
                'sku' => 'SKU-' . str_pad($product['product_id'], 5, '0', STR_PAD_LEFT),
                'category' => $product['category_name'] ?? 'Uncategorized',
                'price' => number_format($product['price'], 2, '.', ''),
                'stock' => (int)$product['stock'],
                'status' => $product['is_active'] ? 'active' : 'inactive',
                'description' => $product['description'],
                'image' => UrlHelper::normalizeImageUrl($product['image_url']),
                'sales' => (int)$product['total_sales'],
                'rating' => 4.5 // You can add a ratings table later
            ];
        }, $products);
        
        sendResponse(200, [
            'success' => true,
            'data' => $formattedProducts,
            'count' => count($formattedProducts)
        ]);
        
    } catch (Exception $e) {
        sendResponse(500, ['error' => 'Failed to fetch products: ' . $e->getMessage()]);
    }
}

// Get single product by ID
function getProductById($db, $vendor_id) {
    if (!isset($_GET['id'])) {
        sendResponse(400, ['error' => 'Product ID is required']);
        return;
    }
    
    $product_id = intval($_GET['id']);
    
    $query = "SELECT 
                p.*,
                c.category_name,
                c.category_id,
                d.discount_value,
                d.discount_type
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.category_id
              LEFT JOIN discounts d ON p.discount_id = d.discount_id
              WHERE p.product_id = ? AND p.vendor_id = ?";
    
    try {
        $result = $db->select($query, [$product_id, $vendor_id]);
        
        if (empty($result)) {
            sendResponse(404, ['error' => 'Product not found']);
            return;
        }
        
        $product = $result[0];
        
        sendResponse(200, [
            'success' => true,
            'data' => [
                'id' => $product['product_id'],
                'name' => $product['name'],
                'sku' => 'SKU-' . str_pad($product['product_id'], 5, '0', STR_PAD_LEFT),
                'category' => $product['category_name'] ?? 'Uncategorized',
                'category_id' => $product['category_id'],
                'price' => number_format($product['price'], 2, '.', ''),
                'stock' => (int)$product['stock'],
                'status' => $product['is_active'] ? 'active' : 'inactive',
                'description' => $product['description'],
                'image' => UrlHelper::normalizeImageUrl($product['image_url'])
            ]
        ]);
        
    } catch (Exception $e) {
        sendResponse(500, ['error' => 'Failed to fetch product: ' . $e->getMessage()]);
    }
}

// Get product statistics
function getProductStats($db, $vendor_id) {
    if (!$vendor_id) {
        sendResponse(400, ['error' => 'Vendor ID is required']);
        return;
    }
    
    try {
        // Total products
        $totalQuery = "SELECT COUNT(*) as total FROM products WHERE vendor_id = ?";
        $totalResult = $db->select($totalQuery, [$vendor_id]);
        $total = $totalResult[0]['total'];
        
        // Low stock products
        $lowStockQuery = "SELECT COUNT(*) as low_stock FROM products WHERE vendor_id = ? AND stock < 10";
        $lowStockResult = $db->select($lowStockQuery, [$vendor_id]);
        $lowStock = $lowStockResult[0]['low_stock'];
        
        // Total sales
        $salesQuery = "SELECT COALESCE(SUM(oi.quantity), 0) as total_sales 
                      FROM order_items oi
                      JOIN products p ON oi.product_id = p.product_id
                      WHERE p.vendor_id = ?";
        $salesResult = $db->select($salesQuery, [$vendor_id]);
        $totalSales = $salesResult[0]['total_sales'];
        
        sendResponse(200, [
            'success' => true,
            'data' => [
                'total_products' => (int)$total,
                'low_stock_count' => (int)$lowStock,
                'total_sales' => (int)$totalSales
            ]
        ]);
        
    } catch (Exception $e) {
        sendResponse(500, ['error' => 'Failed to fetch stats: ' . $e->getMessage()]);
    }
}

// POST handlers
function handlePost($db, $action, $vendor_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$vendor_id) {
        sendResponse(400, ['error' => 'Vendor ID is required']);
        return;
    }
    
    switch ($action) {
        case 'create':
            createProduct($db, $data, $vendor_id);
            break;
            
        case 'bulk':
            bulkUpdateProducts($db, $data, $vendor_id);
            break;
            
        default:
            createProduct($db, $data, $vendor_id);
    }
}

// Create new product
function createProduct($db, $data, $vendor_id) {
    // Validate required fields
    if (empty($data['name']) || empty($data['price']) || !isset($data['stock'])) {
        sendResponse(400, ['error' => 'Missing required fields']);
        return;
    }
    
    // Get or create category
    $category_id = null;
    if (!empty($data['category'])) {
        $category_id = getCategoryId($db, $data['category']);
    }
    
    $query = "INSERT INTO products (name, description, price, stock, image_url, category_id, vendor_id, is_active) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $is_active = isset($data['status']) && $data['status'] === 'active' ? 1 : 0;
    $image_url = $data['image_url'] ?? null;
    
    $params = [
        $data['name'],
        $data['description'] ?? '',
        $data['price'],
        $data['stock'],
        $image_url,
        $category_id,
        $vendor_id,
        $is_active
    ];
    
    try {
        $product_id = $db->insert($query, $params);
        
        sendResponse(201, [
            'success' => true,
            'message' => 'Product created successfully',
            'product_id' => $product_id
        ]);
        
    } catch (Exception $e) {
        sendResponse(500, ['error' => 'Failed to create product: ' . $e->getMessage()]);
    }
}

// PUT handler - Update product
function handlePut($db, $vendor_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        sendResponse(400, ['error' => 'Product ID is required']);
        return;
    }
    
    $product_id = intval($data['id']);
    
    // Verify product belongs to vendor
    $checkQuery = "SELECT product_id FROM products WHERE product_id = ? AND vendor_id = ?";
    $checkResult = $db->select($checkQuery, [$product_id, $vendor_id]);
    
    if (empty($checkResult)) {
        sendResponse(404, ['error' => 'Product not found or access denied']);
        return;
    }
    
    // Get or create category
    $category_id = null;
    if (!empty($data['category'])) {
        $category_id = getCategoryId($db, $data['category']);
    }
    
    $query = "UPDATE products 
              SET name = ?, description = ?, price = ?, stock = ?, 
                  image_url = ?, category_id = ?, is_active = ?
              WHERE product_id = ? AND vendor_id = ?";
    
    $is_active = isset($data['status']) && $data['status'] === 'active' ? 1 : 0;
    $image_url = $data['image_url'] ?? null;
    
    $params = [
        $data['name'],
        $data['description'] ?? '',
        $data['price'],
        $data['stock'],
        $image_url,
        $category_id,
        $is_active,
        $product_id,
        $vendor_id
    ];
    
    try {
        $db->update($query, $params);
        
        sendResponse(200, [
            'success' => true,
            'message' => 'Product updated successfully'
        ]);
        
    } catch (Exception $e) {
        sendResponse(500, ['error' => 'Failed to update product: ' . $e->getMessage()]);
    }
}

// DELETE handler - Soft delete by setting is_active = 0
function handleDelete($db, $vendor_id) {
    if (!isset($_GET['id'])) {
        sendResponse(400, ['error' => 'Product ID is required']);
        return;
    }
    
    $product_id = intval($_GET['id']);
    
    // Check if hard delete is requested (optional, for admin use)
    $hardDelete = isset($_GET['hard_delete']) && $_GET['hard_delete'] === 'true';
    
    // Verify product belongs to vendor
    $checkQuery = "SELECT product_id FROM products WHERE product_id = ? AND vendor_id = ?";
    $checkResult = $db->select($checkQuery, [$product_id, $vendor_id]);
    
    if (empty($checkResult)) {
        sendResponse(404, ['error' => 'Product not found or access denied']);
        return;
    }
    
    try {
        if ($hardDelete) {
            // Hard delete - actually remove from database
            // Only use this if product has no order history
            $query = "DELETE FROM products WHERE product_id = ? AND vendor_id = ?";
            $db->delete($query, [$product_id, $vendor_id]);
            $message = 'Product permanently deleted';
        } else {
            // Soft delete - just mark as inactive
            $query = "UPDATE products SET is_active = 0 WHERE product_id = ? AND vendor_id = ?";
            $db->update($query, [$product_id, $vendor_id]);
            $message = 'Product deactivated successfully';
        }
        
        sendResponse(200, [
            'success' => true,
            'message' => $message
        ]);
        
    } catch (Exception $e) {
        sendResponse(500, ['error' => 'Failed to delete product: ' . $e->getMessage()]);
    }
}

// Bulk update products
function bulkUpdateProducts($db, $data, $vendor_id) {
    if (empty($data['product_ids']) || empty($data['action'])) {
        sendResponse(400, ['error' => 'Product IDs and action are required']);
        return;
    }
    
    $product_ids = $data['product_ids'];
    $action = $data['action'];
    
    // Verify all products belong to vendor
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $checkQuery = "SELECT product_id FROM products WHERE product_id IN ($placeholders) AND vendor_id = ?";
    $checkParams = array_merge($product_ids, [$vendor_id]);
    $checkResult = $db->select($checkQuery, $checkParams);
    
    if (count($checkResult) !== count($product_ids)) {
        sendResponse(403, ['error' => 'Some products not found or access denied']);
        return;
    }
    
    try {
        $db->beginTransaction();
        
        switch ($action) {
            case 'activate':
                $query = "UPDATE products SET is_active = 1 WHERE product_id IN ($placeholders) AND vendor_id = ?";
                $db->update($query, $checkParams);
                break;
                
            case 'deactivate':
                $query = "UPDATE products SET is_active = 0 WHERE product_id IN ($placeholders) AND vendor_id = ?";
                $db->update($query, $checkParams);
                break;
                
            case 'delete':
                // Soft delete - set is_active to 0
                $query = "UPDATE products SET is_active = 0 WHERE product_id IN ($placeholders) AND vendor_id = ?";
                $db->update($query, $checkParams);
                break;
                
            case 'hard_delete':
                // Hard delete - actually remove from database (use with caution)
                $query = "DELETE FROM products WHERE product_id IN ($placeholders) AND vendor_id = ?";
                $db->delete($query, $checkParams);
                break;
                
            default:
                $db->rollback();
                sendResponse(400, ['error' => 'Invalid bulk action']);
                return;
        }
        
        $db->commit();
        
        sendResponse(200, [
            'success' => true,
            'message' => 'Bulk action completed successfully',
            'affected_count' => count($product_ids)
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        sendResponse(500, ['error' => 'Failed to perform bulk action: ' . $e->getMessage()]);
    }
}

// Helper function to get or create category
function getCategoryId($db, $category_name) {
    $query = "SELECT category_id FROM categories WHERE category_name = ?";
    $result = $db->select($query, [$category_name]);
    
    if (!empty($result)) {
        return $result[0]['category_id'];
    }
    
    // Create new category
    $insertQuery = "INSERT INTO categories (category_name) VALUES (?)";
    return $db->insert($insertQuery, [$category_name]);
}

// Send JSON response
function sendResponse($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}
?>