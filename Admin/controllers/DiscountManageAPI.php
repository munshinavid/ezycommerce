<?php
// api/discounts.php - Simplified Automatic Discount API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Check admin authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin privileges required.']);
    exit();
}

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../../utils/UrlHelper.php';

class DiscountManagementAPI {
    private $db;
    private $requestMethod;
    private $requestUri;
    private $uriSegments;

    public function __construct() {
        $this->db = new Database();
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Use PATH_INFO if set (from front controller routing), otherwise use REQUEST_URI
        if (!empty($_SERVER['PATH_INFO'])) {
            $this->requestUri = $_SERVER['PATH_INFO'];
        } else {
            $this->requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
        
        $this->uriSegments = explode('/', trim($this->requestUri, '/'));
    }

    public function handleRequest() {
        try {
            $endpoint = $this->uriSegments[count($this->uriSegments) - 1];
            
            if (is_numeric($endpoint)) {
                $resourceId = $endpoint;
                $action = isset($this->uriSegments[count($this->uriSegments) - 2]) ? 
                         $this->uriSegments[count($this->uriSegments) - 2] : null;
                
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

        $where = [];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "d.discount_name LIKE ?";
            $params[] = "%$search%";
        }
        
        if ($type !== 'all') {
            $where[] = "d.discount_type = ?";
            $params[] = $type;
        }
        
        $now = date('Y-m-d H:i:s');
        if ($status === 'active') {
            $where[] = "(d.start_date <= ? AND d.end_date >= ? AND d.is_active = 1)";
            $params[] = $now;
            $params[] = $now;
        } elseif ($status === 'upcoming') {
            $where[] = "(d.start_date > ? AND d.is_active = 1)";
            $params[] = $now;
        } elseif ($status === 'expired') {
            $where[] = "(d.end_date < ? OR d.is_active = 0)";
            $params[] = $now;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countQuery = "SELECT COUNT(*) as total FROM discounts d $whereClause";
        $countResult = $this->db->select($countQuery, $params);
        $total = $countResult[0]['total'];

        $query = "
            SELECT 
                d.*,
                (
                    SELECT COUNT(DISTINCT p.product_id) 
                    FROM products p 
                    WHERE p.discount_id = d.discount_id
                ) + (
                    SELECT COUNT(DISTINCT p2.product_id)
                    FROM products p2
                    INNER JOIN categories c ON p2.category_id = c.category_id
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

        $productsQuery = "SELECT product_id FROM products WHERE discount_id = ?";
        $products = $this->db->select($productsQuery, [$id]);
        $productIds = array_map(function($p) { return (int)$p['product_id']; }, $products);

        $categoriesQuery = "SELECT category_id FROM categories WHERE discount_id = ?";
        $categories = $this->db->select($categoriesQuery, [$id]);
        $categoryIds = array_map(function($c) { return (int)$c['category_id']; }, $categories);

        $formattedDiscount = $this->formatDiscount($discount);
        $formattedDiscount['products'] = $productIds;
        $formattedDiscount['categories'] = $categoryIds;

        $this->sendResponse(200, $formattedDiscount);
    }

    // GET /api/discounts/{id}/products - Get products with discount
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

        $required = ['name', 'type', 'value', 'start_date', 'end_date', 'apply_to'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $this->sendResponse(400, ['message' => "Field '$field' is required"]);
                return;
            }
        }

        if (!in_array($data['type'], ['percentage', 'fixed'])) {
            $this->sendResponse(400, ['message' => 'Invalid discount type']);
            return;
        }

        if ($data['type'] === 'percentage' && ($data['value'] < 0 || $data['value'] > 100)) {
            $this->sendResponse(400, ['message' => 'Percentage must be between 0 and 100']);
            return;
        }

        if (!in_array($data['apply_to'], ['all', 'selected', 'categories'])) {
            $this->sendResponse(400, ['message' => 'Invalid apply_to value']);
            return;
        }

        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            $this->sendResponse(400, ['message' => 'Start date must be before end date']);
            return;
        }

        $this->db->beginTransaction();

        try {
            $query = "
                INSERT INTO discounts (
                    discount_name, discount_type, discount_value,
                    start_date, end_date, apply_to, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, 1)
            ";
            
            $discountId = $this->db->insert($query, [
                $data['name'],
                $data['type'],
                $data['value'],
                $data['start_date'],
                $data['end_date'],
                $data['apply_to']
            ]);

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

        $checkQuery = "SELECT discount_id FROM discounts WHERE discount_id = ?";
        $exists = $this->db->select($checkQuery, [$id]);
        
        if (empty($exists)) {
            $this->sendResponse(404, ['message' => 'Discount not found']);
            return;
        }

        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            $this->sendResponse(400, ['message' => 'Start date must be before end date']);
            return;
        }

        $this->db->beginTransaction();

        try {
            $query = "
                UPDATE discounts SET
                    discount_name = ?,
                    discount_type = ?,
                    discount_value = ?,
                    start_date = ?,
                    end_date = ?,
                    apply_to = ?
                WHERE discount_id = ?
            ";
            
            $this->db->update($query, [
                $data['name'],
                $data['type'],
                $data['value'],
                $data['start_date'],
                $data['end_date'],
                $data['apply_to'],
                $id
            ]);

            $this->removeDiscountFromAll($id);

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
        $checkQuery = "SELECT discount_id FROM discounts WHERE discount_id = ?";
        $exists = $this->db->select($checkQuery, [$id]);
        
        if (empty($exists)) {
            $this->sendResponse(404, ['message' => 'Discount not found']);
            return;
        }

        $this->db->beginTransaction();

        try {
            $this->removeDiscountFromAll($id);
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

    private function applyDiscountToProducts($discountId, $productIds) {
        if (empty($productIds)) return;

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $query = "UPDATE products SET discount_id = ? WHERE product_id IN ($placeholders)";
        
        $params = array_merge([$discountId], $productIds);
        $this->db->update($query, $params);
    }

    private function applyDiscountToCategories($discountId, $categoryIds) {
        if (empty($categoryIds)) return;

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $query = "UPDATE categories SET discount_id = ? WHERE category_id IN ($placeholders)";
        
        $params = array_merge([$discountId], $categoryIds);
        $this->db->update($query, $params);
    }

    private function applyDiscountToAll($discountId) {
        $this->db->update("UPDATE products SET discount_id = ?", [$discountId]);
        $this->db->update("UPDATE categories SET discount_id = ?", [$discountId]);
    }

    private function removeDiscountFromAll($discountId) {
        $this->db->update("UPDATE products SET discount_id = NULL WHERE discount_id = ?", [$discountId]);
        $this->db->update("UPDATE categories SET discount_id = NULL WHERE discount_id = ?", [$discountId]);
    }

    private function formatDiscount($discount) {
        return [
            'id' => (int)$discount['discount_id'],
            'name' => $discount['discount_name'] ?? '',
            'type' => $discount['discount_type'],
            'value' => (float)$discount['discount_value'],
            'start_date' => $discount['start_date'],
            'end_date' => $discount['end_date'],
            'apply_to' => $discount['apply_to'] ?? 'all',
            'is_active' => (bool)($discount['is_active'] ?? true),
            'products_count' => (int)($discount['products_count'] ?? 0)
        ];
    }

    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}

$api = new DiscountManagementAPI();
$api->handleRequest();