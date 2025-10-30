<?php
// api/discounts.php - Complete Discount Management API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

require_once '../models/Database.php';

class DiscountManagementAPI {
    private $db;
    private $requestMethod;
    private $requestUri;
    private $uriSegments;

    public function __construct() {
        $this->db = new Database();
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->uriSegments = explode('/', trim($this->requestUri, '/'));
    }

    public function handleRequest() {
        try {
            // Determine the endpoint
            $endpoint = $this->uriSegments[count($this->uriSegments) - 1];
            
            // Check if it's a specific resource request
            if (is_numeric($endpoint)) {
                $resourceId = $endpoint;
                $action = isset($this->uriSegments[count($this->uriSegments) - 2]) ? 
                         $this->uriSegments[count($this->uriSegments) - 2] : null;
                
                // Check for sub-resources like /discounts/{id}/products
                if (isset($this->uriSegments[count($this->uriSegments) - 1]) && 
                    !is_numeric($this->uriSegments[count($this->uriSegments) - 1])) {
                    $subResource = $this->uriSegments[count($this->uriSegments) - 1];
                    if ($subResource === 'products') {
                        $this->getDiscountProducts($action);
                        return;
                    }
                }
                
                $this->handleResourceRequest($resourceId);
                return;
            }

            // Handle collection endpoints
            switch ($endpoint) {
                case 'discounts':
                    $this->handleDiscountsEndpoint();
                    break;
                case 'products':
                    $this->handleProductsEndpoint();
                    break;
                case 'categories':
                    $this->handleCategoriesEndpoint();
                    break;
                default:
                    $this->sendResponse(404, ['message' => 'Endpoint not found']);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, ['message' => $e->getMessage()]);
        }
    }

    // ==================== DISCOUNT ENDPOINTS ====================

    private function handleDiscountsEndpoint() {
        switch ($this->requestMethod) {
            case 'GET':
                $this->getDiscounts();
                break;
            case 'POST':
                $this->createDiscount();
                break;
            default:
                $this->sendResponse(405, ['message' => 'Method not allowed']);
        }
    }

    private function handleResourceRequest($id) {
        switch ($this->requestMethod) {
            case 'GET':
                $this->getDiscount($id);
                break;
            case 'PUT':
                $this->updateDiscount($id);
                break;
            case 'DELETE':
                $this->deleteDiscount($id);
                break;
            default:
                $this->sendResponse(405, ['message' => 'Method not allowed']);
        }
    }

    // GET /api/discounts - Get all discounts with pagination and filters
    private function getDiscounts() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        $type = isset($_GET['type']) ? $_GET['type'] : 'all';

        // Build WHERE clause
        $where = [];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(d.discount_code LIKE ? OR d.discount_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($type !== 'all') {
            $where[] = "d.discount_type = ?";
            $params[] = $type;
        }
        
        // Status filter based on dates
        $now = date('Y-m-d H:i:s');
        if ($status === 'active') {
            $where[] = "(d.start_date <= ? AND d.end_date >= ?)";
            $params[] = $now;
            $params[] = $now;
        } elseif ($status === 'upcoming') {
            $where[] = "d.start_date > ?";
            $params[] = $now;
        } elseif ($status === 'expired') {
            $where[] = "d.end_date < ?";
            $params[] = $now;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count total records
        $countQuery = "SELECT COUNT(*) as total FROM discounts d $whereClause";
        $countResult = $this->db->select($countQuery, $params);
        $total = $countResult[0]['total'];

        // Get discounts with product count
        $query = "
            SELECT 
                d.*,
                (
                    SELECT COUNT(DISTINCT p.product_id) 
                    FROM products p 
                    WHERE p.discount_id = d.discount_id
                ) + (
                    SELECT COUNT(DISTINCT c.category_id) 
                    FROM categories c 
                    WHERE c.discount_id = d.discount_id
                ) as products_count
            FROM discounts d
            $whereClause
            ORDER BY d.discount_id DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $discounts = $this->db->select($query, $params);

        // Format discounts
        $formattedDiscounts = array_map(function($discount) {
            return $this->formatDiscount($discount);
        }, $discounts);

        $this->sendResponse(200, [
            'discounts' => $formattedDiscounts,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'per_page' => $limit,
                'total' => (int)$total
            ]
        ]);
    }

    // GET /api/discounts/{id} - Get single discount
    private function getDiscount($id) {
        $query = "SELECT * FROM discounts WHERE discount_id = ?";
        $result = $this->db->select($query, [$id]);

        if (empty($result)) {
            $this->sendResponse(404, ['message' => 'Discount not found']);
            return;
        }

        $discount = $result[0];

        // Get associated products
        $productsQuery = "SELECT product_id FROM products WHERE discount_id = ?";
        $products = $this->db->select($productsQuery, [$id]);
        $productIds = array_map(function($p) { return (int)$p['product_id']; }, $products);

        // Get associated categories
        $categoriesQuery = "SELECT category_id FROM categories WHERE discount_id = ?";
        $categories = $this->db->select($categoriesQuery, [$id]);
        $categoryIds = array_map(function($c) { return (int)$c['category_id']; }, $categories);

        $formattedDiscount = $this->formatDiscount($discount);
        $formattedDiscount['products'] = $productIds;
        $formattedDiscount['categories'] = $categoryIds;

        $this->sendResponse(200, $formattedDiscount);
    }

    // GET /api/discounts/{id}/products - Get products with discount (both direct and via categories)
    private function getDiscountProducts($id) {
        $query = "
            SELECT DISTINCT
                p.product_id,
                p.name,
                p.price,
                p.image_url,
                c.category_name as category,
                CASE 
                    WHEN p.discount_id = ? THEN 'direct'
                    WHEN c.discount_id = ? THEN 'category'
                    ELSE 'none'
                END as discount_source
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE p.discount_id = ? OR c.discount_id = ?
            ORDER BY p.name
        ";
        
        $products = $this->db->select($query, [$id, $id, $id, $id]);

        $formattedProducts = array_map(function($product) {
            return [
                'id' => (int)$product['product_id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'image_url' => $product['image_url'],
                'category' => $product['category'] ?? 'Uncategorized',
                'discount_source' => $product['discount_source']
            ];
        }, $products);

        $this->sendResponse(200, ['products' => $formattedProducts]);
    }

    // POST /api/discounts - Create new discount
    private function createDiscount() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $required = ['code', 'name', 'type', 'value', 'start_date', 'end_date', 'apply_to'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $this->sendResponse(400, ['message' => "Field '$field' is required"]);
                return;
            }
        }

        // Validate discount type
        if (!in_array($data['type'], ['percentage', 'fixed'])) {
            $this->sendResponse(400, ['message' => 'Invalid discount type. Must be percentage or fixed']);
            return;
        }

        // Validate percentage value
        if ($data['type'] === 'percentage' && ($data['value'] < 0 || $data['value'] > 100)) {
            $this->sendResponse(400, ['message' => 'Percentage must be between 0 and 100']);
            return;
        }

        // Validate apply_to
        if (!in_array($data['apply_to'], ['all', 'selected', 'categories'])) {
            $this->sendResponse(400, ['message' => 'Invalid apply_to value']);
            return;
        }

        // Validate dates
        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            $this->sendResponse(400, ['message' => 'Start date must be before end date']);
            return;
        }

        $this->db->beginTransaction();

        try {
            // Check if discount code already exists
            $checkQuery = "SELECT discount_id FROM discounts WHERE discount_code = ?";
            $existing = $this->db->select($checkQuery, [$data['code']]);
            if (!empty($existing)) {
                $this->sendResponse(400, ['message' => 'Discount code already exists']);
                return;
            }

            // Insert discount
            $query = "
                INSERT INTO discounts (
                    discount_code, discount_name, discount_type, discount_value,
                    start_date, end_date, max_uses, min_order_amount, apply_to
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
            
            $discountId = $this->db->insert($query, [
                $data['code'],
                $data['name'],
                $data['type'],
                $data['value'],
                $data['start_date'],
                $data['end_date'],
                $data['max_uses'] ?? 0,
                $data['min_order_amount'] ?? 0,
                $data['apply_to']
            ]);

            // Apply discount based on apply_to setting
            if ($data['apply_to'] === 'selected' && !empty($data['products'])) {
                $this->applyDiscountToProducts($discountId, $data['products']);
            } elseif ($data['apply_to'] === 'categories' && !empty($data['categories'])) {
                $this->applyDiscountToCategories($discountId, $data['categories']);
            } elseif ($data['apply_to'] === 'all') {
                $this->applyDiscountToAll($discountId);
            }

            $this->db->commit();
            $this->sendResponse(201, [
                'message' => 'Discount created successfully',
                'discount_id' => $discountId
            ]);
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // PUT /api/discounts/{id} - Update discount
    private function updateDiscount($id) {
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if discount exists
        $checkQuery = "SELECT discount_id, discount_code FROM discounts WHERE discount_id = ?";
        $exists = $this->db->select($checkQuery, [$id]);
        
        if (empty($exists)) {
            $this->sendResponse(404, ['message' => 'Discount not found']);
            return;
        }

        // Check if new code conflicts with another discount
        if ($data['code'] !== $exists[0]['discount_code']) {
            $codeCheck = "SELECT discount_id FROM discounts WHERE discount_code = ? AND discount_id != ?";
            $codeExists = $this->db->select($codeCheck, [$data['code'], $id]);
            if (!empty($codeExists)) {
                $this->sendResponse(400, ['message' => 'Discount code already exists']);
                return;
            }
        }

        // Validate dates
        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            $this->sendResponse(400, ['message' => 'Start date must be before end date']);
            return;
        }

        $this->db->beginTransaction();

        try {
            // Update discount
            $query = "
                UPDATE discounts SET
                    discount_code = ?,
                    discount_name = ?,
                    discount_type = ?,
                    discount_value = ?,
                    start_date = ?,
                    end_date = ?,
                    max_uses = ?,
                    min_order_amount = ?,
                    apply_to = ?
                WHERE discount_id = ?
            ";
            
            $this->db->update($query, [
                $data['code'],
                $data['name'],
                $data['type'],
                $data['value'],
                $data['start_date'],
                $data['end_date'],
                $data['max_uses'] ?? 0,
                $data['min_order_amount'] ?? 0,
                $data['apply_to'],
                $id
            ]);

            // Remove existing discount from products and categories
            $this->removeDiscountFromAll($id);

            // Apply discount based on new apply_to setting
            if ($data['apply_to'] === 'selected' && !empty($data['products'])) {
                $this->applyDiscountToProducts($id, $data['products']);
            } elseif ($data['apply_to'] === 'categories' && !empty($data['categories'])) {
                $this->applyDiscountToCategories($id, $data['categories']);
            } elseif ($data['apply_to'] === 'all') {
                $this->applyDiscountToAll($id);
            }

            $this->db->commit();
            $this->sendResponse(200, ['message' => 'Discount updated successfully']);
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // DELETE /api/discounts/{id} - Delete discount
    private function deleteDiscount($id) {
        // Check if discount exists
        $checkQuery = "SELECT discount_id FROM discounts WHERE discount_id = ?";
        $exists = $this->db->select($checkQuery, [$id]);
        
        if (empty($exists)) {
            $this->sendResponse(404, ['message' => 'Discount not found']);
            return;
        }

        $this->db->beginTransaction();

        try {
            // Remove discount from products and categories (foreign key SET NULL will handle this, but being explicit)
            $this->removeDiscountFromAll($id);

            // Delete discount
            $deleteQuery = "DELETE FROM discounts WHERE discount_id = ?";
            $this->db->delete($deleteQuery, [$id]);

            $this->db->commit();
            $this->sendResponse(200, ['message' => 'Discount deleted successfully']);
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // ==================== PRODUCTS ENDPOINTS ====================

    private function handleProductsEndpoint() {
        if ($this->requestMethod !== 'GET') {
            $this->sendResponse(405, ['message' => 'Method not allowed']);
            return;
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;

        $where = '';
        $params = [];

        if (!empty($search)) {
            $where = 'WHERE p.name LIKE ? OR p.description LIKE ?';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query = "
            SELECT 
                p.product_id,
                p.name,
                p.price,
                p.stock,
                p.image_url,
                c.category_name,
                c.category_id
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            $where
            ORDER BY p.name
            LIMIT ?
        ";

        $params[] = $limit;
        $products = $this->db->select($query, $params);

        $formattedProducts = array_map(function($product) {
            return [
                'id' => (int)$product['product_id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'stock' => (int)$product['stock'],
                'image_url' => $product['image_url'],
                'category' => $product['category_name'] ?? 'Uncategorized',
                'category_id' => $product['category_id'] ? (int)$product['category_id'] : null
            ];
        }, $products);

        $this->sendResponse(200, ['products' => $formattedProducts]);
    }

    // ==================== CATEGORIES ENDPOINTS ====================

    private function handleCategoriesEndpoint() {
        if ($this->requestMethod !== 'GET') {
            $this->sendResponse(405, ['message' => 'Method not allowed']);
            return;
        }

        $query = "
            SELECT 
                category_id,
                category_name,
                (SELECT COUNT(*) FROM products WHERE category_id = categories.category_id) as products_count
            FROM categories
            ORDER BY category_name
        ";

        $categories = $this->db->select($query);

        $formattedCategories = array_map(function($category) {
            return [
                'id' => (int)$category['category_id'],
                'name' => $category['category_name'],
                'products_count' => (int)$category['products_count']
            ];
        }, $categories);

        $this->sendResponse(200, ['categories' => $formattedCategories]);
    }

    // ==================== HELPER METHODS ====================

    // Apply discount to specific products
    private function applyDiscountToProducts($discountId, $productIds) {
        if (empty($productIds)) return;

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $query = "UPDATE products SET discount_id = ? WHERE product_id IN ($placeholders)";
        
        $params = array_merge([$discountId], $productIds);
        $this->db->update($query, $params);
    }

    // Apply discount to specific categories
    private function applyDiscountToCategories($discountId, $categoryIds) {
        if (empty($categoryIds)) return;

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $query = "UPDATE categories SET discount_id = ? WHERE category_id IN ($placeholders)";
        
        $params = array_merge([$discountId], $categoryIds);
        $this->db->update($query, $params);
    }

    // Apply discount to all products and categories
    private function applyDiscountToAll($discountId) {
        $this->db->update("UPDATE products SET discount_id = ?", [$discountId]);
        $this->db->update("UPDATE categories SET discount_id = ?", [$discountId]);
    }

    // Remove discount from all products and categories
    private function removeDiscountFromAll($discountId) {
        $this->db->update("UPDATE products SET discount_id = NULL WHERE discount_id = ?", [$discountId]);
        $this->db->update("UPDATE categories SET discount_id = NULL WHERE discount_id = ?", [$discountId]);
    }

    // Format discount for response
    private function formatDiscount($discount) {
        return [
            'id' => (int)$discount['discount_id'],
            'code' => $discount['discount_code'] ?? '',
            'name' => $discount['discount_name'] ?? '',
            'type' => $discount['discount_type'],
            'value' => (float)$discount['discount_value'],
            'start_date' => $discount['start_date'],
            'end_date' => $discount['end_date'],
            'max_uses' => (int)($discount['max_uses'] ?? 0),
            'min_order_amount' => (float)($discount['min_order_amount'] ?? 0),
            'apply_to' => $discount['apply_to'] ?? 'all',
            'products_count' => (int)($discount['products_count'] ?? 0)
        ];
    }

    // Send JSON response
    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}

// Initialize and handle the request
$api = new DiscountManagementAPI();
$api->handleRequest();