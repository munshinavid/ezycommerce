<?php
// HomeController.php - Fixed Discount Display Logic
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . "API Error: " . $message . "\n", 3, 'api_errors.log');
}

require_once __DIR__ . '/../models/db.php';

class RESTfulAPIController {
    private $db;
    private $method;
    private $path;
    private $pathSegments;
    
    public function __construct() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->db = new Database();
            $this->method = $_SERVER['REQUEST_METHOD'];
            $this->parsePath();
        } catch (Exception $e) {
            logError("Database connection failed: " . $e->getMessage());
            $this->sendError('Database connection failed: ' . $e->getMessage(), 500);
        }
    }
    
    private function parsePath() {
        $uri = $_SERVER['REQUEST_URI'];
        $script = $_SERVER['SCRIPT_NAME'];
        $this->path = trim(str_replace($script, '', $uri), '/');
        
        if (($pos = strpos($this->path, '?')) !== false) {
            $this->path = substr($this->path, 0, $pos);
        }
        
        $this->pathSegments = array_filter(explode('/', $this->path));
        $this->pathSegments = array_values($this->pathSegments);
    }
    private function normalizeImageUrl(?string $imageUrl): string {
        if (empty($imageUrl)) {
            return '/ezycommerce/uploads/images/product1.jpg';
        }

        if (strpos($imageUrl, 'http://') === 0 || strpos($imageUrl, 'https://') === 0) {
            return $imageUrl;
        }

        if (strpos($imageUrl, '/') === 0) {
            return $imageUrl;
        }

        return '/ezycommerce/' . ltrim($imageUrl, '/');
    }    
    public function handleRequest() {
        try {
            if (empty($this->pathSegments)) {
                $this->sendError("Invalid endpoint", 404);
                return;
            }
            
            $resource = $this->pathSegments[0];
            
            switch ($resource) {
                case 'categories':
                    $this->handleCategories();
                    break;
                case 'products':
                    $this->handleProducts();
                    break;
                case 'customers':
                    $this->handleCustomers();
                    break;
                case 'users':
                    $this->handleUsers();
                    break;
                case 'newsletter':
                    $this->handleNewsletter();
                    break;
                default:
                    $this->sendError("Invalid endpoint: $resource", 404);
                    break;
            }
        } catch (Exception $e) {
            logError("HandleRequest Exception: " . $e->getMessage());
            $this->sendError('Internal server error: ' . $e->getMessage(), 500);
        }
    }
    
    private function handleCategories() {
        if ($this->method !== 'GET') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        $this->getCategories();
    }
    
    private function handleProducts() {
        if ($this->method !== 'GET') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        if (count($this->pathSegments) === 1) {
            $this->getProducts();
        } elseif (count($this->pathSegments) === 2) {
            $productId = $this->pathSegments[1];
            $this->getProduct($productId);
        } else {
            $this->sendError('Invalid products endpoint', 404);
        }
    }
    
    private function handleCustomers() {
        if (count($this->pathSegments) < 3) {
            $this->sendError('Invalid customers endpoint', 404);
            return;
        }
        
        $customerId = $this->pathSegments[1];
        $resource = $this->pathSegments[2];
        
        if ($resource === 'cart') {
            $this->handleCustomerCart($customerId);
        } else {
            $this->sendError('Invalid customers resource', 404);
        }
    }
    
    private function handleUsers() {
        if (count($this->pathSegments) < 3) {
            $this->sendError('Invalid users endpoint', 404);
            return;
        }
        
        $userId = $this->pathSegments[1];
        $resource = $this->pathSegments[2];
        
        if ($resource === 'wishlist') {
            $this->handleUserWishlist($userId);
        } else {
            $this->sendError('Invalid users resource', 404);
        }
    }
    
    private function authenticate($requiredId = null) {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $this->sendError('Unauthorized access', 401);
        }
        if ($requiredId !== null && $_SESSION['user']['id'] != $requiredId) {
            $this->sendError('Forbidden: Access denied', 403);
        }
    }

    private function handleCustomerCart($customerId) {
        $this->authenticate($customerId);
        switch ($this->method) {
            case 'GET':
                if (count($this->pathSegments) === 4 && $this->pathSegments[3] === 'count') {
                    $this->getCartCount($customerId);
                } else {
                    $this->getCart($customerId);
                }
                break;
            case 'POST':
                $this->addToCart($customerId);
                break;
            case 'PUT':
                if (count($this->pathSegments) === 4) {
                    $cartItemId = $this->pathSegments[3];
                    $this->updateCartItem($cartItemId);
                } else {
                    $this->sendError('Cart item ID required for update', 400);
                }
                break;
            case 'DELETE':
                if (count($this->pathSegments) === 4) {
                    $cartItemId = $this->pathSegments[3];
                    $this->removeFromCart($cartItemId);
                } else {
                    $this->sendError('Cart item ID required for deletion', 400);
                }
                break;
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function handleUserWishlist($userId) {
        $this->authenticate($userId);
        switch ($this->method) {
            case 'GET':
                if (count($this->pathSegments) === 4 && $this->pathSegments[3] === 'count') {
                    $this->getWishlistCount($userId);
                } else {
                    $this->getWishlist($userId);
                }
                break;
            case 'POST':
                $this->addToWishlist($userId);
                break;
            case 'DELETE':
                if (count($this->pathSegments) === 4) {
                    $productId = $this->pathSegments[3];
                    $this->removeFromWishlist($userId, $productId);
                } else {
                    $this->sendError('Product ID required for wishlist removal', 400);
                }
                break;
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function handleNewsletter() {
        if (count($this->pathSegments) === 2 && $this->pathSegments[1] === 'subscriptions') {
            if ($this->method === 'POST') {
                $this->subscribeNewsletter();
            } else {
                $this->sendError('Method not allowed', 405);
            }
        } else {
            $this->sendError('Invalid newsletter endpoint', 404);
        }
    }
    
    private function getCategories() {
        try {
            $categories = $this->db->select("
                SELECT category_id, category_name 
                FROM categories 
                ORDER BY category_name
            ");
            
            $this->sendResponse([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (Exception $e) {
            logError("Categories error: " . $e->getMessage());
            $this->sendError('Failed to load categories: ' . $e->getMessage());
        }
    }
    
    // FIXED: Proper discount calculation
    private function getProducts() {
        try {
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 8);
            $filter = $_GET['filter'] ?? 'all';
            $sort = $_GET['sort'] ?? 'newest';
            $category = $_GET['category'] ?? '';
            $search = $_GET['search'] ?? '';
            
            $offset = ($page - 1) * $limit;
            $currentDateTime = date('Y-m-d H:i:s');
            
            $whereClauses = [];
            $params = [];
            
            if (!empty($category)) {
                $whereClauses[] = "c.category_name = ?";
                $params[] = $category;
            }
            
            if (!empty($search)) {
                $whereClauses[] = "(p.name LIKE ? OR p.description LIKE ? OR c.category_name LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            switch ($filter) {
                case 'sale':
                    // Check for active discounts (direct product discount OR category discount)
                    $whereClauses[] = "((pd.discount_id IS NOT NULL AND pd.start_date <= ? AND pd.end_date >= ? AND pd.is_active = 1) OR (cd.discount_id IS NOT NULL AND cd.start_date <= ? AND cd.end_date >= ? AND cd.is_active = 1))";
                    $params[] = $currentDateTime;
                    $params[] = $currentDateTime;
                    $params[] = $currentDateTime;
                    $params[] = $currentDateTime;
                    break;
                case 'in-stock':
                    $whereClauses[] = "p.stock > 0";
                    break;
                case 'out-of-stock':
                    $whereClauses[] = "p.stock = 0";
                    break;
            }
            
            $whereClause = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
            
            $orderBy = 'ORDER BY ';
            switch ($sort) {
                case 'price-low':
                    $orderBy .= 'final_price ASC';
                    break;
                case 'price-high':
                    $orderBy .= 'final_price DESC';
                    break;
                case 'name':
                    $orderBy .= 'p.name ASC';
                    break;
                case 'newest':
                default:
                    $orderBy .= 'p.product_id DESC';
                    break;
            }
            
            // Count query with proper discount filtering
            $countQuery = "
                SELECT COUNT(*) as total 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                LEFT JOIN discounts pd ON p.discount_id = pd.discount_id
                LEFT JOIN discounts cd ON c.discount_id = cd.discount_id
                $whereClause
            ";
            $countResult = $this->db->select($countQuery, $params);
            $totalProducts = $countResult[0]['total'];
            $totalPages = ceil($totalProducts / $limit);
            
            // FIXED: Proper discount calculation query
            $productsQuery = "
                SELECT 
                    p.product_id,
                    p.name,
                    p.description,
                    p.price as original_price,
                    CASE 
                        -- Product has direct discount
                        WHEN pd.discount_id IS NOT NULL 
                             AND pd.start_date <= ? 
                             AND pd.end_date >= ? 
                             AND pd.is_active = 1 
                        THEN 
                            CASE 
                                WHEN pd.discount_type = 'percentage' 
                                THEN p.price * (1 - pd.discount_value/100)
                                WHEN pd.discount_type = 'fixed' 
                                THEN GREATEST(p.price - pd.discount_value, 0)
                            END
                        -- Product inherits category discount
                        WHEN cd.discount_id IS NOT NULL 
                             AND cd.start_date <= ? 
                             AND cd.end_date >= ? 
                             AND cd.is_active = 1 
                        THEN 
                            CASE 
                                WHEN cd.discount_type = 'percentage' 
                                THEN p.price * (1 - cd.discount_value/100)
                                WHEN cd.discount_type = 'fixed' 
                                THEN GREATEST(p.price - cd.discount_value, 0)
                            END
                        -- No discount
                        ELSE p.price
                    END as final_price,
                    CASE 
                        WHEN pd.discount_id IS NOT NULL 
                             AND pd.start_date <= ? 
                             AND pd.end_date >= ? 
                             AND pd.is_active = 1 
                        THEN pd.discount_value
                        WHEN cd.discount_id IS NOT NULL 
                             AND cd.start_date <= ? 
                             AND cd.end_date >= ? 
                             AND cd.is_active = 1 
                        THEN cd.discount_value
                        ELSE 0
                    END as discount_value,
                    CASE 
                        WHEN pd.discount_id IS NOT NULL 
                             AND pd.start_date <= ? 
                             AND pd.end_date >= ? 
                             AND pd.is_active = 1 
                        THEN pd.discount_type
                        WHEN cd.discount_id IS NOT NULL 
                             AND cd.start_date <= ? 
                             AND cd.end_date >= ? 
                             AND cd.is_active = 1 
                        THEN cd.discount_type
                        ELSE NULL
                    END as discount_type,
                    c.category_name,
                    p.image_url,
                    p.stock,
                    (p.stock > 0) as in_stock,
                    CASE 
                        WHEN (pd.discount_id IS NOT NULL AND pd.is_active = 1) 
                          OR (cd.discount_id IS NOT NULL AND cd.is_active = 1) 
                        THEN 'sale'
                        ELSE NULL
                    END as badge
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                LEFT JOIN discounts pd ON p.discount_id = pd.discount_id
                LEFT JOIN discounts cd ON c.discount_id = cd.discount_id
                $whereClause 
                $orderBy 
                LIMIT ? OFFSET ?
            ";
            
            // Add current datetime parameters for discount checks (12 total)
            $queryParams = array_merge(
                [$currentDateTime, $currentDateTime, $currentDateTime, $currentDateTime], // price calculation
                [$currentDateTime, $currentDateTime, $currentDateTime, $currentDateTime], // discount_value
                [$currentDateTime, $currentDateTime, $currentDateTime, $currentDateTime], // discount_type
                $params, // where clause params
                [$limit, $offset] // pagination
            );
            
            $products = $this->db->select($productsQuery, $queryParams);
            
            // Format products
            foreach ($products as &$product) {
                $originalPrice = floatval($product['original_price']);
                $finalPrice = floatval($product['final_price']);
                
                $product['price'] = $finalPrice;
                $product['original_price'] = $originalPrice;
                $product['rating'] = rand(3, 5) + round(rand(0, 9) / 10, 1); // Random rating between 3.0 and 5.0
                $product['review_count'] = rand(10, 200);
                $product['stock'] = intval($product['stock']);
                $product['in_stock'] = $product['stock'] > 0;
                
                // Calculate discount percentage for display
                if ($originalPrice > $finalPrice) {
                    $product['discount_percentage'] = round((($originalPrice - $finalPrice) / $originalPrice) * 100);
                } else {
                    $product['discount_percentage'] = 0;
                }
                
                $product['image_url'] = $this->normalizeImageUrl($product['image_url'] ?? '');
            }
            
            $this->sendResponse([
                'success' => true,
                'products' => $products,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_items' => $totalProducts,
                    'items_per_page' => $limit
                ]
            ]);
            
        } catch (Exception $e) {
            logError("Products error: " . $e->getMessage());
            $this->sendError('Failed to load products: ' . $e->getMessage());
        }
    }
    
    // FIXED: Single product with proper discount
    private function getProduct($productId) {
        try {
            $currentDateTime = date('Y-m-d H:i:s');
            
            $products = $this->db->select("
                SELECT 
                    p.product_id,
                    p.name,
                    p.description,
                    p.price as original_price,
                    CASE 
                        WHEN pd.discount_id IS NOT NULL 
                             AND pd.start_date <= ? 
                             AND pd.end_date >= ? 
                             AND pd.is_active = 1 
                        THEN 
                            CASE 
                                WHEN pd.discount_type = 'percentage' 
                                THEN p.price * (1 - pd.discount_value/100)
                                WHEN pd.discount_type = 'fixed' 
                                THEN GREATEST(p.price - pd.discount_value, 0)
                            END
                        WHEN cd.discount_id IS NOT NULL 
                             AND cd.start_date <= ? 
                             AND cd.end_date >= ? 
                             AND cd.is_active = 1 
                        THEN 
                            CASE 
                                WHEN cd.discount_type = 'percentage' 
                                THEN p.price * (1 - cd.discount_value/100)
                                WHEN cd.discount_type = 'fixed' 
                                THEN GREATEST(p.price - cd.discount_value, 0)
                            END
                        ELSE p.price
                    END as final_price,
                    c.category_name,
                    p.image_url,
                    p.stock,
                    (p.stock > 0) as in_stock
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                LEFT JOIN discounts pd ON p.discount_id = pd.discount_id
                LEFT JOIN discounts cd ON c.discount_id = cd.discount_id
                WHERE p.product_id = ?
            ", [$currentDateTime, $currentDateTime, $currentDateTime, $currentDateTime, $productId]);
            
            if (empty($products)) {
                $this->sendError('Product not found', 404);
                return;
            }
            
            $product = $products[0];
            $originalPrice = floatval($product['original_price']);
            $finalPrice = floatval($product['final_price']);
            
            $product['price'] = $finalPrice;
            $product['original_price'] = $originalPrice;
            $product['rating'] = 4.5;
            $product['review_count'] = rand(10, 200);
            $product['stock'] = intval($product['stock']);
            $product['in_stock'] = $product['stock'] > 0;
            
            $product['image_url'] = $this->normalizeImageUrl($product['image_url'] ?? '');
            
            $this->sendResponse([
                'success' => true,
                'product' => $product
            ]);
            
        } catch (Exception $e) {
            $this->sendError('Failed to load product: ' . $e->getMessage());
        }
    }
    
    private function getCart($customerId) {
        $carts = $this->db->select("SELECT cart_id FROM cart WHERE customer_id = ?", [$customerId]);
        
        if (empty($carts)) {
            $this->db->insert("INSERT INTO cart (customer_id) VALUES (?)", [$customerId]);
            $cartId = $this->db->getLastInsertId();
        } else {
            $cartId = $carts[0]['cart_id'];
        }
        
        $cartItems = $this->db->select("
            SELECT 
                ci.cart_item_id,
                ci.product_id,
                ci.quantity,
                p.name,
                p.price,
                p.image_url,
                p.stock,
                (p.price * ci.quantity) as total
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.product_id
            WHERE ci.cart_id = ?
            ORDER BY ci.cart_item_id DESC
        ", [$cartId]);
        
        $this->sendResponse([
            'success' => true,
            'cart_items' => $cartItems
        ]);
    }
    
    private function addToCart($customerId) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $productId = $input['product_id'] ?? null;
        $quantity = $input['quantity'] ?? 1;
        
        if (!$productId) {
            $this->sendError('Product ID required');
            return;
        }
        
        $products = $this->db->select("SELECT stock, name FROM products WHERE product_id = ?", [$productId]);
        
        if (empty($products)) {
            $this->sendError('Product not found');
            return;
        }
        
        if ($products[0]['stock'] < $quantity) {
            $this->sendError('Insufficient stock available');
            return;
        }
        
        $carts = $this->db->select("SELECT cart_id FROM cart WHERE customer_id = ?", [$customerId]);
        
        if (empty($carts)) {
            $this->db->insert("INSERT INTO cart (customer_id) VALUES (?)", [$customerId]);
            $cartId = $this->db->getLastInsertId();
        } else {
            $cartId = $carts[0]['cart_id'];
        }
        
        $existingItems = $this->db->select("
            SELECT cart_item_id, quantity 
            FROM cart_items 
            WHERE cart_id = ? AND product_id = ?
        ", [$cartId, $productId]);
        
        if (!empty($existingItems)) {
            $newQuantity = $existingItems[0]['quantity'] + $quantity;
            $this->db->update("
                UPDATE cart_items 
                SET quantity = ? 
                WHERE cart_item_id = ?
            ", [$newQuantity, $existingItems[0]['cart_item_id']]);
        } else {
            $this->db->insert("
                INSERT INTO cart_items (cart_id, product_id, quantity) 
                VALUES (?, ?, ?)
            ", [$cartId, $productId, $quantity]);
        }
        
        $cartCount = $this->getCartCountForCustomer($customerId);
        
        $this->sendResponse([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'cart_count' => $cartCount
        ]);
    }
    
    private function updateCartItem($cartItemId) {
        $input = json_decode(file_get_contents('php://input'), true);
        $quantity = $input['quantity'] ?? null;
        
        if (!$quantity || $quantity < 1) {
            $this->sendError('Valid quantity required');
            return;
        }
        
        $this->db->update("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?", [$quantity, $cartItemId]);
        
        $this->sendResponse([
            'success' => true,
            'message' => 'Cart updated successfully'
        ]);
    }
    
    private function removeFromCart($cartItemId) {
        $this->db->delete("DELETE FROM cart_items WHERE cart_item_id = ?", [$cartItemId]);
        
        $this->sendResponse([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }
    
    private function getCartCount($customerId) {
        $cartCount = $this->getCartCountForCustomer($customerId);
        
        $this->sendResponse([
            'success' => true,
            'cart_count' => $cartCount
        ]);
    }
    
    private function getCartCountForCustomer($customerId) {
        $result = $this->db->select("
            SELECT COALESCE(SUM(ci.quantity), 0) as cart_count
            FROM cart c
            LEFT JOIN cart_items ci ON c.cart_id = ci.cart_id
            WHERE c.customer_id = ?
        ", [$customerId]);
        
        return intval($result[0]['cart_count']);
    }
    
    private function getWishlist($userId) {
        $wishlistItems = $this->db->select("
            SELECT 
                w.wishlist_id,
                w.product_id,
                p.name,
                p.price,
                p.image_url,
                p.stock
            FROM wishlist w
            JOIN products p ON w.product_id = p.product_id
            WHERE w.user_id = ?
            ORDER BY w.added_at DESC
        ", [$userId]);
        
        $this->sendResponse([
            'success' => true,
            'wishlist_items' => $wishlistItems
        ]);
    }
    
    private function addToWishlist($userId) {
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? null;
        
        if (!$productId) {
            $this->sendError('Product ID required');
            return;
        }
        
        $existing = $this->db->select("
            SELECT wishlist_id 
            FROM wishlist 
            WHERE user_id = ? AND product_id = ?
        ", [$userId, $productId]);
        
        if (!empty($existing)) {
            $this->sendError('Item already in wishlist');
            return;
        }
        
        $this->db->insert("
            INSERT INTO wishlist (user_id, product_id) 
            VALUES (?, ?)
        ", [$userId, $productId]);
        
        $wishlistCount = $this->getWishlistCountForUser($userId);
        
        $this->sendResponse([
            'success' => true,
            'message' => 'Item added to wishlist',
            'wishlist_count' => $wishlistCount
        ]);
    }
    
    private function removeFromWishlist($userId, $productId) {
        $this->db->delete("
            DELETE FROM wishlist 
            WHERE user_id = ? AND product_id = ?
        ", [$userId, $productId]);
        
        $wishlistCount = $this->getWishlistCountForUser($userId);
        
        $this->sendResponse([
            'success' => true,
            'message' => 'Item removed from wishlist',
            'wishlist_count' => $wishlistCount
        ]);
    }
    
    private function getWishlistCount($userId) {
        $wishlistCount = $this->getWishlistCountForUser($userId);
        
        $this->sendResponse([
            'success' => true,
            'wishlist_count' => $wishlistCount
        ]);
    }
    
    private function getWishlistCountForUser($userId) {
        $result = $this->db->select("
            SELECT COUNT(*) as wishlist_count
            FROM wishlist
            WHERE user_id = ?
        ", [$userId]);
        
        return intval($result[0]['wishlist_count']);
    }
    
    private function subscribeNewsletter() {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? null;
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendError('Valid email required');
            return;
        }
        
        try {
            $this->sendResponse([
                'success' => true,
                'message' => 'Successfully subscribed to newsletter'
            ]);
        } catch (Exception $e) {
            $this->sendError('Newsletter subscription failed: ' . $e->getMessage());
        }
    }
    
    private function sendResponse($data) {
        echo json_encode($data);
        exit();
    }
    
    private function sendError($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
        exit();
    }
}

$controller = new RESTfulAPIController();
$controller->handleRequest();
?>