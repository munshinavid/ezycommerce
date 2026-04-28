<?php
// discounts_api.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/Database.php';

// Start session to get vendor info
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to send JSON response
function sendResponse($success, $data = null, $message = '', $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

// Get current vendor ID from session
function getCurrentVendorId() {
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        sendResponse(false, null, 'Authentication required', 401);
    }

    $role = isset($_SESSION['user']['role']) ? strtolower((string)$_SESSION['user']['role']) : '';
    if ($role !== 'vendor') {
        sendResponse(false, null, 'Vendor access only', 403);
    }

    $db = new Database();
    $vendor = $db->select(
        'SELECT vendor_id FROM vendors WHERE user_id = ?',
        [(int)$_SESSION['user']['id']]
    );

    if (empty($vendor)) {
        sendResponse(false, null, 'Vendor not found', 404);
    }

    return (int)$vendor[0]['vendor_id'];
}

try {
    $db = new Database();
    $method = $_SERVER['REQUEST_METHOD'];
    $request = $_GET['action'] ?? '';

    switch ($method) {
        case 'GET':
            handleGet($db, $request);
            break;
        case 'POST':
            handlePost($db, $request);
            break;
        case 'PUT':
            handlePut($db, $request);
            break;
        case 'DELETE':
            handleDelete($db, $request);
            break;
        default:
            sendResponse(false, null, 'Method not allowed', 405);
    }
} catch (Exception $e) {
    sendResponse(false, null, $e->getMessage(), 500);
}

// ==================== GET REQUESTS ====================
function handleGet($db, $action) {
    $vendorId = getCurrentVendorId();
    
    switch ($action) {
        case 'discounts':
            getVendorDiscounts($db, $vendorId);
            break;
        case 'discount':
            getDiscountById($db, $vendorId);
            break;
        case 'products':
            getVendorProducts($db, $vendorId);
            break;
        case 'categories':
            getCategories($db);
            break;
        case 'discount_products':
            getDiscountProducts($db, $vendorId);
            break;
        case 'vendor_info':
            getVendorInfo($db, $vendorId);
            break;
        default:
            sendResponse(false, null, 'Invalid action', 400);
    }
}

// Get all discounts for a vendor
function getVendorDiscounts($db, $vendorId) {
    // Get all discounts that apply to vendor's products
    $query = "
        SELECT DISTINCT
            d.discount_id,
            d.discount_name,
            d.discount_type,
            d.discount_value,
            d.start_date,
            d.end_date,
            d.apply_to,
            d.is_active,
            d.created_at,
            d.updated_at,
            COUNT(DISTINCT p.product_id) as products_count
        FROM discounts d
        LEFT JOIN products p ON d.discount_id = p.discount_id AND p.vendor_id = ?
        LEFT JOIN categories c ON d.discount_id = c.discount_id
        LEFT JOIN products p2 ON c.category_id = p2.category_id AND p2.vendor_id = ?
        WHERE d.apply_to = 'all' 
           OR (d.apply_to = 'selected' AND p.product_id IS NOT NULL)
           OR (d.apply_to = 'categories' AND p2.product_id IS NOT NULL)
        GROUP BY d.discount_id
        ORDER BY d.created_at DESC
    ";
    
    $discounts = $db->select($query, [$vendorId, $vendorId]);
    sendResponse(true, $discounts);
}

// Get single discount by ID
function getDiscountById($db, $vendorId) {
    if (!isset($_GET['discount_id'])) {
        sendResponse(false, null, 'Discount ID required', 400);
    }
    
    $discountId = $_GET['discount_id'];
    
    // Get discount details
    $discount = $db->select(
        "SELECT * FROM discounts WHERE discount_id = ?",
        [$discountId]
    );
    
    if (empty($discount)) {
        sendResponse(false, null, 'Discount not found', 404);
    }
    
    $discountData = $discount[0];
    
    // Get associated products or categories
    if ($discountData['apply_to'] === 'selected') {
        $products = $db->select(
            "SELECT product_id FROM products WHERE discount_id = ? AND vendor_id = ?",
            [$discountId, $vendorId]
        );
        $discountData['selected_products'] = array_column($products, 'product_id');
    } elseif ($discountData['apply_to'] === 'categories') {
        $categories = $db->select(
            "SELECT category_id FROM categories WHERE discount_id = ?",
            [$discountId]
        );
        $discountData['selected_categories'] = array_column($categories, 'category_id');
    }
    
    sendResponse(true, $discountData);
}

// Get vendor's products
function getVendorProducts($db, $vendorId) {
    $products = $db->select(
        "SELECT 
            product_id,
            name,
            price,
            stock,
            image_url,
            category_id,
            discount_id,
            is_active
        FROM products 
        WHERE vendor_id = ? AND is_active = 1
        ORDER BY name",
        [$vendorId]
    );
    
    sendResponse(true, $products);
}

// Get all categories
function getCategories($db) {
    $categories = $db->select(
        "SELECT category_id, category_name FROM categories ORDER BY category_name"
    );
    
    sendResponse(true, $categories);
}

// Get products affected by a discount
function getDiscountProducts($db, $vendorId) {
    if (!isset($_GET['discount_id'])) {
        sendResponse(false, null, 'Discount ID required', 400);
    }
    
    $discountId = $_GET['discount_id'];
    
    // Get discount details
    $discount = $db->select(
        "SELECT apply_to FROM discounts WHERE discount_id = ?",
        [$discountId]
    );
    
    if (empty($discount)) {
        sendResponse(false, null, 'Discount not found', 404);
    }
    
    $applyTo = $discount[0]['apply_to'];
    
    if ($applyTo === 'all') {
        // Get all vendor products
        $products = $db->select(
            "SELECT product_id, name, price, image_url FROM products WHERE vendor_id = ? AND is_active = 1",
            [$vendorId]
        );
    } elseif ($applyTo === 'selected') {
        // Get selected products
        $products = $db->select(
            "SELECT product_id, name, price, image_url 
             FROM products 
             WHERE discount_id = ? AND vendor_id = ? AND is_active = 1",
            [$discountId, $vendorId]
        );
    } else { // categories
        // Get products from categories with this discount
        $products = $db->select(
            "SELECT p.product_id, p.name, p.price, p.image_url 
             FROM products p
             INNER JOIN categories c ON p.category_id = c.category_id
             WHERE c.discount_id = ? AND p.vendor_id = ? AND p.is_active = 1",
            [$discountId, $vendorId]
        );
    }
    
    sendResponse(true, $products);
}

// Get vendor information
function getVendorInfo($db, $vendorId) {
    $vendor = $db->select(
        "SELECT v.vendor_id, v.vendor_name, v.contact_email, u.email, u.username
         FROM vendors v
         INNER JOIN users u ON v.user_id = u.user_id
         WHERE v.vendor_id = ?",
        [$vendorId]
    );
    
    if (empty($vendor)) {
        sendResponse(false, null, 'Vendor not found', 404);
    }
    
    sendResponse(true, $vendor[0]);
}

// ==================== POST REQUESTS ====================
function handlePost($db, $action) {
    $vendorId = getCurrentVendorId();
    
    switch ($action) {
        case 'create_discount':
            createDiscount($db, $vendorId);
            break;
        default:
            sendResponse(false, null, 'Invalid action', 400);
    }
}

// Create new discount
function createDiscount($db, $vendorId) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['discount_name']) || !isset($data['discount_type']) || 
        !isset($data['discount_value']) || !isset($data['start_date']) || 
        !isset($data['end_date']) || !isset($data['apply_to'])) {
        sendResponse(false, null, 'Missing required fields', 400);
    }
    
    // Validate dates
    $startDate = strtotime($data['start_date']);
    $endDate = strtotime($data['end_date']);
    
    if ($endDate <= $startDate) {
        sendResponse(false, null, 'End date must be after start date', 400);
    }
    
    // Validate discount value
    if ($data['discount_type'] === 'percentage') {
        if ($data['discount_value'] <= 0 || $data['discount_value'] > 100) {
            sendResponse(false, null, 'Percentage must be between 0 and 100', 400);
        }
    } elseif ($data['discount_value'] <= 0) {
        sendResponse(false, null, 'Discount value must be greater than 0', 400);
    }
    
    try {
        $db->beginTransaction();
        
        // Insert discount
        $discountId = $db->insert(
            "INSERT INTO discounts (discount_name, discount_type, discount_value, start_date, end_date, apply_to, is_active) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['discount_name'],
                $data['discount_type'],
                $data['discount_value'],
                $data['start_date'],
                $data['end_date'],
                $data['apply_to'],
                $data['is_active'] ?? true
            ]
        );
        
        // Handle different apply_to scenarios
        if ($data['apply_to'] === 'selected' && !empty($data['selected_products'])) {
            // Verify all products belong to this vendor
            $placeholders = implode(',', array_fill(0, count($data['selected_products']), '?'));
            $params = array_merge([$vendorId], $data['selected_products']);
            
            $vendorProducts = $db->select(
                "SELECT product_id FROM products WHERE vendor_id = ? AND product_id IN ($placeholders)",
                $params
            );
            
            if (count($vendorProducts) !== count($data['selected_products'])) {
                throw new Exception('Some products do not belong to this vendor');
            }
            
            // Update products with discount
            foreach ($data['selected_products'] as $productId) {
                $db->update(
                    "UPDATE products SET discount_id = ? WHERE product_id = ? AND vendor_id = ?",
                    [$discountId, $productId, $vendorId]
                );
            }
        } elseif ($data['apply_to'] === 'categories' && !empty($data['selected_categories'])) {
            // Update categories with discount
            foreach ($data['selected_categories'] as $categoryId) {
                $db->update(
                    "UPDATE categories SET discount_id = ? WHERE category_id = ?",
                    [$discountId, $categoryId]
                );
            }
        } elseif ($data['apply_to'] === 'all') {
            // Apply discount to all vendor products
            $db->update(
                "UPDATE products SET discount_id = ? WHERE vendor_id = ?",
                [$discountId, $vendorId]
            );
        }
        
        $db->commit();
        sendResponse(true, ['discount_id' => $discountId], 'Discount created successfully');
        
    } catch (Exception $e) {
        $db->rollback();
        sendResponse(false, null, 'Error creating discount: ' . $e->getMessage(), 500);
    }
}

// ==================== PUT REQUESTS ====================
function handlePut($db, $action) {
    $vendorId = getCurrentVendorId();
    
    switch ($action) {
        case 'update_discount':
            updateDiscount($db, $vendorId);
            break;
        default:
            sendResponse(false, null, 'Invalid action', 400);
    }
}

// Update existing discount
function updateDiscount($db, $vendorId) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['discount_id'])) {
        sendResponse(false, null, 'Discount ID required', 400);
    }
    
    $discountId = $data['discount_id'];
    
    // Validate dates if provided
    if (isset($data['start_date']) && isset($data['end_date'])) {
        $startDate = strtotime($data['start_date']);
        $endDate = strtotime($data['end_date']);
        
        if ($endDate <= $startDate) {
            sendResponse(false, null, 'End date must be after start date', 400);
        }
    }
    
    try {
        $db->beginTransaction();
        
        // Check if discount exists
        $existingDiscount = $db->select(
            "SELECT * FROM discounts WHERE discount_id = ?",
            [$discountId]
        );
        
        if (empty($existingDiscount)) {
            throw new Exception('Discount not found');
        }
        
        // Update discount basic info
        $db->update(
            "UPDATE discounts 
             SET discount_name = ?, 
                 discount_type = ?, 
                 discount_value = ?, 
                 start_date = ?, 
                 end_date = ?, 
                 apply_to = ?, 
                 is_active = ?
             WHERE discount_id = ?",
            [
                $data['discount_name'] ?? $existingDiscount[0]['discount_name'],
                $data['discount_type'] ?? $existingDiscount[0]['discount_type'],
                $data['discount_value'] ?? $existingDiscount[0]['discount_value'],
                $data['start_date'] ?? $existingDiscount[0]['start_date'],
                $data['end_date'] ?? $existingDiscount[0]['end_date'],
                $data['apply_to'] ?? $existingDiscount[0]['apply_to'],
                $data['is_active'] ?? $existingDiscount[0]['is_active'],
                $discountId
            ]
        );
        
        // Clear existing associations
        $db->update(
            "UPDATE products SET discount_id = NULL WHERE discount_id = ? AND vendor_id = ?",
            [$discountId, $vendorId]
        );
        
        $db->update(
            "UPDATE categories SET discount_id = NULL WHERE discount_id = ?",
            [$discountId]
        );
        
        // Handle new associations
        $applyTo = $data['apply_to'] ?? $existingDiscount[0]['apply_to'];
        
        if ($applyTo === 'selected' && !empty($data['selected_products'])) {
            // Verify products belong to vendor
            $placeholders = implode(',', array_fill(0, count($data['selected_products']), '?'));
            $params = array_merge([$vendorId], $data['selected_products']);
            
            $vendorProducts = $db->select(
                "SELECT product_id FROM products WHERE vendor_id = ? AND product_id IN ($placeholders)",
                $params
            );
            
            if (count($vendorProducts) !== count($data['selected_products'])) {
                throw new Exception('Some products do not belong to this vendor');
            }
            
            // Apply discount to selected products
            foreach ($data['selected_products'] as $productId) {
                $db->update(
                    "UPDATE products SET discount_id = ? WHERE product_id = ? AND vendor_id = ?",
                    [$discountId, $productId, $vendorId]
                );
            }
        } elseif ($applyTo === 'categories' && !empty($data['selected_categories'])) {
            // Apply discount to categories
            foreach ($data['selected_categories'] as $categoryId) {
                $db->update(
                    "UPDATE categories SET discount_id = ? WHERE category_id = ?",
                    [$discountId, $categoryId]
                );
            }
        } elseif ($applyTo === 'all') {
            // Apply to all vendor products
            $db->update(
                "UPDATE products SET discount_id = ? WHERE vendor_id = ?",
                [$discountId, $vendorId]
            );
        }
        
        $db->commit();
        sendResponse(true, ['discount_id' => $discountId], 'Discount updated successfully');
        
    } catch (Exception $e) {
        $db->rollback();
        sendResponse(false, null, 'Error updating discount: ' . $e->getMessage(), 500);
    }
}

// ==================== DELETE REQUESTS ====================
function handleDelete($db, $action) {
    $vendorId = getCurrentVendorId();
    
    switch ($action) {
        case 'delete_discount':
            deleteDiscount($db, $vendorId);
            break;
        default:
            sendResponse(false, null, 'Invalid action', 400);
    }
}

// Delete discount
function deleteDiscount($db, $vendorId) {
    if (!isset($_GET['discount_id'])) {
        sendResponse(false, null, 'Discount ID required', 400);
    }
    
    $discountId = $_GET['discount_id'];
    
    try {
        $db->beginTransaction();
        
        // Remove discount from vendor's products
        $db->update(
            "UPDATE products SET discount_id = NULL WHERE discount_id = ? AND vendor_id = ?",
            [$discountId, $vendorId]
        );
        
        // Remove discount from categories (if no other vendors use it)
        $db->update(
            "UPDATE categories SET discount_id = NULL WHERE discount_id = ?",
            [$discountId]
        );
        
        // Delete the discount
        $db->delete(
            "DELETE FROM discounts WHERE discount_id = ?",
            [$discountId]
        );
        
        $db->commit();
        sendResponse(true, null, 'Discount deleted successfully');
        
    } catch (Exception $e) {
        $db->rollback();
        sendResponse(false, null, 'Error deleting discount: ' . $e->getMessage(), 500);
    }
}
?>