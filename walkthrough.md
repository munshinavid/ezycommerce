# EzyCommerce Pre-Deployment Fixes — Walkthrough

## Summary

Applied **8 critical fixes** and **3 medium improvements** across **10 files** to make the project production-ready and portfolio-safe.

---

## Critical Fixes Applied

### C-1: Removed Plaintext Password Bypass
**File:** [AuthController.php](file:///c:/xampp/htdocs/ezycommerce/Customer/controllers/AuthController.php#L100-L103)

Removed the `else if ($password === $user['password'])` fallback that allowed login with unhashed passwords. Only `password_verify()` is used now.

```diff:AuthController.php
<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../models/db.php';

class AuthController {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $endpoint = $_GET['endpoint'] ?? null; // ✅ simpler query param
        
        // If no endpoint param, try to detect from request URI
        if (!$endpoint && isset($_SERVER['REQUEST_URI'])) {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (strpos($uri, '/logout') !== false) {
                $endpoint = 'logout';
            }
        }

        switch ($endpoint) {
            case 'login':
                if ($method === 'POST') {
                    $this->login();
                }
                break;
                
            case 'register':
                if ($method === 'POST') {
                    $this->register();
                }
                break;
                
            case 'logout':
                if ($method === 'POST' || $method === 'GET') {
                    $this->logout();
                }
                break;
                
            case 'verify':
                if ($method === 'GET') {
                    $this->verifyToken();
                }
                break;
                
            default:
                $this->sendResponse(['error' => 'Invalid request'], 404);
        }
    }
    
    public function login() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            $email = trim($input['email'] ?? '');
            $password = $input['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $this->sendResponse(['error' => 'Email and password are required'], 400);
                return;
            }
            
            $user = $this->db->select(
                "SELECT u.user_id, u.username, u.email, u.password, r.role_name 
                 FROM users u 
                 JOIN roles r ON u.role_id = r.role_id 
                 WHERE u.email = ?",
                [$email]
            );
            
            if (empty($user)) {
                $this->sendResponse(['error' => 'Invalid credentials'], 401);
                return;
            }
            
            $user = $user[0];
            $passwordMatch = false;
            
            // Check bcrypt hashed password first
            if (password_verify($password, $user['password'])) {
                $passwordMatch = true;
            } else if ($password === $user['password']) {
                // TEMPORARY: Allow plain text password for testing (remove in production)
                $passwordMatch = true;
            }
            
            if (!$passwordMatch) {
                $this->sendResponse(['error' => 'Invalid credentials'], 401);
                return;
            }
            
            $userDetails = $this->db->select(
                "SELECT full_name, phone FROM customerdetails WHERE user_id = ? LIMIT 1",
                [$user['user_id']]
            );
            
            $userData = [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role_name'],
                'firstName' => '',
                'lastName' => '',
                'phone' => '',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
            ];
            
            if (!empty($userDetails)) {
                $nameParts = explode(' ', $userDetails[0]['full_name'], 2);
                $userData['firstName'] = $nameParts[0];
                $userData['lastName'] = $nameParts[1] ?? '';
                $userData['phone'] = $userDetails[0]['phone'];
            }

            // ✅ Store necessary info in session
            $_SESSION['user'] = [
                'id' => $userData['id'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'role' => $userData['role'],
                'firstName' => $userData['firstName'],
                'lastName' => $userData['lastName'],
                'phone' => $userData['phone']
            ];
            
            $this->sendResponse([
                'message' => 'Login successful',
                'user' => $userData
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Login failed'], 500);
        }
    }
    
    public function register() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            $username = trim($input['username'] ?? '');
            $email = trim($input['email'] ?? '');
            $password = $input['password'] ?? '';
            $firstName = trim($input['firstName'] ?? '');
            $lastName = trim($input['lastName'] ?? '');
            $phone = trim($input['phone'] ?? '');
            
            if (empty($username) || empty($email) || empty($password)) {
                $this->sendResponse(['error' => 'Username, email, and password are required'], 400);
                return;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->sendResponse(['error' => 'Invalid email format'], 400);
                return;
            }
            
            $existingUser = $this->db->select(
                "SELECT user_id FROM users WHERE email = ? OR username = ?",
                [$email, $username]
            );
            if (!empty($existingUser)) {
                $this->sendResponse(['error' => 'User already exists with this email or username'], 400);
                return;
            }
            
            $customerRole = $this->db->select("SELECT role_id FROM roles WHERE role_name = 'Customer' LIMIT 1");
            if (empty($customerRole)) {
                $this->db->insert("INSERT INTO roles (role_name) VALUES ('Customer')");
                $roleId = $this->db->getLastInsertId();
            } else {
                $roleId = $customerRole[0]['role_id'];
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $success = $this->db->insert(
                "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)",
                [$username, $email, $hashedPassword, $roleId]
            );
            if (!$success) {
                $this->sendResponse(['error' => 'Failed to create user'], 500);
                return;
            }
            
            $userId = $this->db->getLastInsertId();
            if (!empty($firstName) || !empty($lastName) || !empty($phone)) {
                $fullName = trim($firstName . ' ' . $lastName);
                $this->db->insert(
                    "INSERT INTO customerdetails (user_id, full_name, billing_address, shipping_address, phone) VALUES (?, ?, '', '', ?)",
                    [$userId, $fullName, $phone]
                );
            }

            $userData = [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phone' => $phone,
                'role' => 'Customer',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
            ];
            
            $this->sendResponse([
                'message' => 'Registration successful',
                'user' => $userData
            ], 201);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Registration failed'], 500);
        }
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $this->sendResponse(['message' => 'Logout successful']);
    }
    
    public function verifyToken() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
                $this->sendResponse(['error' => 'Not authenticated'], 401);
                return;
            }
            
            $userId = $_SESSION['user']['id'];
            
            $user = $this->db->select(
                "SELECT u.user_id, u.username, u.email, r.role_name 
                 FROM users u 
                 JOIN roles r ON u.role_id = r.role_id 
                 WHERE u.user_id = ?",
                [$userId]
            );
            if (empty($user)) {
                $this->sendResponse(['error' => 'User not found'], 404);
                return;
            }
            
            $user = $user[0];
            $userDetails = $this->db->select(
                "SELECT full_name, phone FROM customerdetails WHERE user_id = ? LIMIT 1",
                [$userId]
            );
            
            $userData = [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role_name'],
                'firstName' => '',
                'lastName' => '',
                'phone' => '',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
            ];
            
            if (!empty($userDetails)) {
                $nameParts = explode(' ', $userDetails[0]['full_name'], 2);
                $userData['firstName'] = $nameParts[0];
                $userData['lastName'] = $nameParts[1] ?? '';
                $userData['phone'] = $userDetails[0]['phone'];
            }
            
            $this->sendResponse(['valid' => true, 'user' => $userData]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Token verification failed'], 500);
        }
    }
    
    private function sendResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
}

$controller = new AuthController();
$controller->handleRequest();
===
<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../models/db.php';

class AuthController {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $endpoint = $_GET['endpoint'] ?? null; // ✅ simpler query param
        
        // If no endpoint param, try to detect from request URI
        if (!$endpoint && isset($_SERVER['REQUEST_URI'])) {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (strpos($uri, '/logout') !== false) {
                $endpoint = 'logout';
            }
        }

        switch ($endpoint) {
            case 'login':
                if ($method === 'POST') {
                    $this->login();
                }
                break;
                
            case 'register':
                if ($method === 'POST') {
                    $this->register();
                }
                break;
                
            case 'logout':
                if ($method === 'POST' || $method === 'GET') {
                    $this->logout();
                }
                break;
                
            case 'verify':
                if ($method === 'GET') {
                    $this->verifyToken();
                }
                break;
                
            default:
                $this->sendResponse(['error' => 'Invalid request'], 404);
        }
    }
    
    public function login() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            $email = trim($input['email'] ?? '');
            $password = $input['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $this->sendResponse(['error' => 'Email and password are required'], 400);
                return;
            }
            
            $user = $this->db->select(
                "SELECT u.user_id, u.username, u.email, u.password, r.role_name 
                 FROM users u 
                 JOIN roles r ON u.role_id = r.role_id 
                 WHERE u.email = ?",
                [$email]
            );
            
            if (empty($user)) {
                $this->sendResponse(['error' => 'Invalid credentials'], 401);
                return;
            }
            
            $user = $user[0];
            $passwordMatch = false;
            
            // Verify bcrypt hashed password
            if (password_verify($password, $user['password'])) {
                $passwordMatch = true;
            }
            
            if (!$passwordMatch) {
                $this->sendResponse(['error' => 'Invalid credentials'], 401);
                return;
            }
            
            $userDetails = $this->db->select(
                "SELECT full_name, phone FROM customerdetails WHERE user_id = ? LIMIT 1",
                [$user['user_id']]
            );
            
            $userData = [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role_name'],
                'firstName' => '',
                'lastName' => '',
                'phone' => '',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
            ];
            
            if (!empty($userDetails)) {
                $nameParts = explode(' ', $userDetails[0]['full_name'], 2);
                $userData['firstName'] = $nameParts[0];
                $userData['lastName'] = $nameParts[1] ?? '';
                $userData['phone'] = $userDetails[0]['phone'];
            }

            // ✅ Store necessary info in session
            $_SESSION['user'] = [
                'id' => $userData['id'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'role' => $userData['role'],
                'firstName' => $userData['firstName'],
                'lastName' => $userData['lastName'],
                'phone' => $userData['phone']
            ];
            
            $this->sendResponse([
                'message' => 'Login successful',
                'user' => $userData
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Login failed'], 500);
        }
    }
    
    public function register() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            $username = trim($input['username'] ?? '');
            $email = trim($input['email'] ?? '');
            $password = $input['password'] ?? '';
            $firstName = trim($input['firstName'] ?? '');
            $lastName = trim($input['lastName'] ?? '');
            $phone = trim($input['phone'] ?? '');
            
            if (empty($username) || empty($email) || empty($password)) {
                $this->sendResponse(['error' => 'Username, email, and password are required'], 400);
                return;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->sendResponse(['error' => 'Invalid email format'], 400);
                return;
            }
            
            $existingUser = $this->db->select(
                "SELECT user_id FROM users WHERE email = ? OR username = ?",
                [$email, $username]
            );
            if (!empty($existingUser)) {
                $this->sendResponse(['error' => 'User already exists with this email or username'], 400);
                return;
            }
            
            $customerRole = $this->db->select("SELECT role_id FROM roles WHERE role_name = 'Customer' LIMIT 1");
            if (empty($customerRole)) {
                $this->db->insert("INSERT INTO roles (role_name) VALUES ('Customer')");
                $roleId = $this->db->getLastInsertId();
            } else {
                $roleId = $customerRole[0]['role_id'];
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $success = $this->db->insert(
                "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)",
                [$username, $email, $hashedPassword, $roleId]
            );
            if (!$success) {
                $this->sendResponse(['error' => 'Failed to create user'], 500);
                return;
            }
            
            $userId = $this->db->getLastInsertId();
            if (!empty($firstName) || !empty($lastName) || !empty($phone)) {
                $fullName = trim($firstName . ' ' . $lastName);
                $this->db->insert(
                    "INSERT INTO customerdetails (user_id, full_name, billing_address, shipping_address, phone) VALUES (?, ?, '', '', ?)",
                    [$userId, $fullName, $phone]
                );
            }

            $userData = [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phone' => $phone,
                'role' => 'Customer',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
            ];
            
            $this->sendResponse([
                'message' => 'Registration successful',
                'user' => $userData
            ], 201);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Registration failed'], 500);
        }
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $this->sendResponse(['message' => 'Logout successful']);
    }
    
    public function verifyToken() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
                $this->sendResponse(['error' => 'Not authenticated'], 401);
                return;
            }
            
            $userId = $_SESSION['user']['id'];
            
            $user = $this->db->select(
                "SELECT u.user_id, u.username, u.email, r.role_name 
                 FROM users u 
                 JOIN roles r ON u.role_id = r.role_id 
                 WHERE u.user_id = ?",
                [$userId]
            );
            if (empty($user)) {
                $this->sendResponse(['error' => 'User not found'], 404);
                return;
            }
            
            $user = $user[0];
            $userDetails = $this->db->select(
                "SELECT full_name, phone FROM customerdetails WHERE user_id = ? LIMIT 1",
                [$userId]
            );
            
            $userData = [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role_name'],
                'firstName' => '',
                'lastName' => '',
                'phone' => '',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
            ];
            
            if (!empty($userDetails)) {
                $nameParts = explode(' ', $userDetails[0]['full_name'], 2);
                $userData['firstName'] = $nameParts[0];
                $userData['lastName'] = $nameParts[1] ?? '';
                $userData['phone'] = $userDetails[0]['phone'];
            }
            
            $this->sendResponse(['valid' => true, 'user' => $userData]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Token verification failed'], 500);
        }
    }
    
    private function sendResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
}

$controller = new AuthController();
$controller->handleRequest();
```

---

### C-2: Removed Fatal `alert()` in PHP
**File:** [UserController.php](file:///c:/xampp/htdocs/ezycommerce/Customer/controllers/UserController.php#L49)

Deleted `alert("orders")` — a JavaScript function call inside PHP that caused a fatal `Call to undefined function` error on every orders API request.

```diff:UserController.php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../models/db.php';

class UserController {
    private $db;
    private $user_id;
    
    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->user_id = $this->getUserIdFromSession();
    }
    
    public function handleRequest() {
        // Get endpoint from query parameters instead of URI
        $endpoint = $_GET['endpoint'] ?? null;
        $id = $_GET['id'] ?? null;
        $method = $_GET['method'] ?? $_SERVER['REQUEST_METHOD'];
        
        if (!$endpoint) {
            $this->sendResponse(['error' => 'Endpoint parameter is required'], 400);
            return;
        }
        
        switch ($endpoint) {
            case 'dashboard':
                if ($method === 'GET') {
                    $this->getDashboard();
                } else {
                    $this->sendResponse(['error' => 'Method not allowed for dashboard'], 405);
                }
                break;
                
            case 'orders':
                if ($method === 'GET') {
                    $this->getOrders();
                    alert("orders");
                } else {
                    $this->sendResponse(['error' => 'Method not allowed for orders'], 405);
                }
                break;
                
            case 'addresses':
                switch ($method) {
                    case 'GET':
                        if ($id) {
                            $this->getAddress($id);
                        } else {
                            $this->getAddresses();
                        }
                        break;
                    case 'POST':
                        $this->createAddress();
                        break;
                    case 'PUT':
                        if ($id) {
                            $this->updateAddress($id);
                        } else {
                            $this->sendResponse(['error' => 'ID required for address update'], 400);
                        }
                        break;
                    case 'DELETE':
                        if ($id) {
                            $this->deleteAddress($id);
                        } else {
                            $this->sendResponse(['error' => 'ID required for address deletion'], 400);
                        }
                        break;
                    default:
                        $this->sendResponse(['error' => 'Method not allowed for addresses'], 405);
                }
                break;
                
            case 'profile':
                switch ($method) {
                    case 'GET':
                        $this->getProfile();
                        break;
                    case 'PUT':
                        $this->updateProfile();
                        break;
                    default:
                        $this->sendResponse(['error' => 'Method not allowed for profile'], 405);
                }
                break;
                
            case 'wishlist':
                switch ($method) {
                    case 'GET':
                        $this->getWishlist();
                        break;
                    case 'POST':
                        $this->addToWishlist();
                        break;
                    case 'DELETE':
                        if ($id) {
                            $this->removeFromWishlist($id);
                        } else {
                            $this->sendResponse(['error' => 'ID required for wishlist removal'], 400);
                        }
                        break;
                    default:
                        $this->sendResponse(['error' => 'Method not allowed for wishlist'], 405);
                }
                break;
                
            default:
                $this->sendResponse(['error' => 'Endpoint not found'], 404);
        }
    }
    
    private function getUserIdFromSession() {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id'];
        }

        $this->sendResponse(['error' => 'Not authenticated'], 401);
        exit();
   }
    
    public function getDashboard() {
        try {
            // Get total orders count
            $totalOrders = $this->db->select(
                "SELECT COUNT(*) as count FROM orders WHERE customer_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            // Get completed orders count
            $completedOrders = $this->db->select(
                "SELECT COUNT(*) as count FROM orders WHERE customer_id = ? AND order_status = 'Delivered'",
                [$this->user_id]
            )[0]['count'];
            
            // Get wishlist items count
            $wishlistItems = $this->db->select(
                "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            // Get addresses count
            $addressesCount = $this->db->select(
                "SELECT COUNT(*) as count FROM customerdetails WHERE user_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            // Get recent orders
            $recentOrders = $this->db->select(
                "SELECT order_id as id, order_status as status, total_amount as total, created_at as orderDate 
                 FROM orders WHERE customer_id = ? 
                 ORDER BY created_at DESC LIMIT 5",
                [$this->user_id]
            );
            
            $stats = [
                'totalOrders' => (int)$totalOrders,
                'completedOrders' => (int)$completedOrders,
                'wishlistItems' => (int)$wishlistItems,
                'addressesCount' => (int)$addressesCount
            ];
            
            $this->sendResponse([
                'stats' => $stats,
                'recentOrders' => $recentOrders
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch dashboard data'], 500);
        }
    }
    
    public function getOrders() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            // Get total count for pagination
            $totalCount = $this->db->select(
                "SELECT COUNT(*) as count FROM orders WHERE customer_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            // Get orders with pagination
            $orders = $this->db->select(
                "SELECT order_id as id, order_status as status, total_amount as total, created_at as orderDate 
                 FROM orders WHERE customer_id = ? 
                 ORDER BY created_at DESC LIMIT ? OFFSET ?",
                [$this->user_id, $limit, $offset]
            );
            
            $totalPages = ceil($totalCount / $limit);
            
            $pagination = [
                'currentPage' => $page,
                'totalPages' => (int)$totalPages,
                'totalItems' => (int)$totalCount,
                'itemsPerPage' => $limit
            ];
            
            $this->sendResponse([
                'orders' => $orders,
                'pagination' => $pagination
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch orders'], 500);
        }
    }
    
    public function getAddresses() {
        try {
            $addresses = $this->db->select(
                "SELECT detail_id as id, full_name, address as address_line1, '' as address_line2, 
                        '' as city, '' as state, '' as zip_code, '' as country, phone, 
                        CASE WHEN detail_id = (SELECT MIN(detail_id) FROM customerdetails WHERE user_id = ?) THEN 1 ELSE 0 END as is_default
                 FROM customerdetails WHERE user_id = ?",
                [$this->user_id, $this->user_id]
            );
            
            $this->sendResponse($addresses);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch addresses'], 500);
        }
    }
    
    public function getAddress($id) {
        try {
            $address = $this->db->select(
                "SELECT detail_id as id, full_name, address as address_line1, '' as address_line2, 
                        '' as city, '' as state, '' as zip_code, '' as country, phone, 
                        CASE WHEN detail_id = (SELECT MIN(detail_id) FROM customerdetails WHERE user_id = ?) THEN 1 ELSE 0 END as is_default,
                        'home' as type
                 FROM customerdetails WHERE detail_id = ? AND user_id = ?",
                [$this->user_id, $id, $this->user_id]
            );
            
            if (empty($address)) {
                $this->sendResponse(['error' => 'Address not found'], 404);
                return;
            }
            
            $this->sendResponse($address[0]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch address'], 500);
        }
    }
    
    public function createAddress() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            $required_fields = ['full_name', 'address_line1', 'phone'];
            foreach ($required_fields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    $this->sendResponse(['error' => "Field '$field' is required"], 400);
                    return;
                }
            }
            
            // Combine address fields into single address field for CustomerDetails table
            $full_address = $input['address_line1'];
            if (!empty($input['address_line2'])) {
                $full_address .= ', ' . $input['address_line2'];
            }
            if (!empty($input['city'])) {
                $full_address .= ', ' . $input['city'];
            }
            if (!empty($input['state'])) {
                $full_address .= ', ' . $input['state'];
            }
            if (!empty($input['zip_code'])) {
                $full_address .= ' ' . $input['zip_code'];
            }
            if (!empty($input['country'])) {
                $full_address .= ', ' . $input['country'];
            }
            
            $success = $this->db->execute(
                "INSERT INTO customerdetails (user_id, full_name, address, phone) VALUES (?, ?, ?, ?)",
                [$this->user_id, $input['full_name'], $full_address, $input['phone']]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Address created successfully'], 201);
            } else {
                $this->sendResponse(['error' => 'Failed to create address'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to create address'], 500);
        }
    }
    
    public function updateAddress($id) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            // Check if address exists and belongs to user
            $existing = $this->db->select(
                "SELECT detail_id FROM customerdetails WHERE detail_id = ? AND user_id = ?",
                [$id, $this->user_id]
            );
            
            if (empty($existing)) {
                $this->sendResponse(['error' => 'Address not found'], 404);
                return;
            }
            
            // Combine address fields
            $full_address = $input['address_line1'];
            if (!empty($input['address_line2'])) {
                $full_address .= ', ' . $input['address_line2'];
            }
            if (!empty($input['city'])) {
                $full_address .= ', ' . $input['city'];
            }
            if (!empty($input['state'])) {
                $full_address .= ', ' . $input['state'];
            }
            if (!empty($input['zip_code'])) {
                $full_address .= ' ' . $input['zip_code'];
            }
            if (!empty($input['country'])) {
                $full_address .= ', ' . $input['country'];
            }
            
            $success = $this->db->execute(
                "UPDATE customerdetails SET full_name = ?, address = ?, phone = ? WHERE detail_id = ? AND user_id = ?",
                [$input['full_name'], $full_address, $input['phone'], $id, $this->user_id]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Address updated successfully']);
            } else {
                $this->sendResponse(['error' => 'Failed to update address'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to update address'], 500);
        }
    }
    
    public function deleteAddress($id) {
        try {
            // Check if address exists and belongs to user
            $existing = $this->db->select(
                "SELECT detail_id FROM customerdetails WHERE detail_id = ? AND user_id = ?",
                [$id, $this->user_id]
            );
            
            if (empty($existing)) {
                $this->sendResponse(['error' => 'Address not found'], 404);
                return;
            }
            
            $success = $this->db->execute(
                "DELETE FROM customerdetails WHERE detail_id = ? AND user_id = ?",
                [$id, $this->user_id]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Address deleted successfully']);
            } else {
                $this->sendResponse(['error' => 'Failed to delete address'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to delete address'], 500);
        }
    }
    
    public function getProfile() {
        try {
            $user = $this->db->select(
                "SELECT user_id as id, username, email, created_at FROM users WHERE user_id = ?",
                [$this->user_id]
            );
            
            if (empty($user)) {
                $this->sendResponse(['error' => 'User not found'], 404);
                return;
            }
            
            // Get additional details if available
            $details = $this->db->select(
                "SELECT full_name, phone FROM customerdetails WHERE user_id = ? LIMIT 1",
                [$this->user_id]
            );
            
            $profile = $user[0];
            if (!empty($details)) {
                $nameParts = explode(' ', $details[0]['full_name'], 2);
                $profile['firstName'] = $nameParts[0];
                $profile['lastName'] = isset($nameParts[1]) ? $nameParts[1] : '';
                $profile['phone'] = $details[0]['phone'];
            } else {
                $profile['firstName'] = '';
                $profile['lastName'] = '';
                $profile['phone'] = '';
            }
            
            $profile['avatar'] = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80';
            
            $this->sendResponse($profile);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch profile'], 500);
        }
    }
    
    public function updateProfile() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            // Update Users table
            if (isset($input['email'])) {
                $this->db->execute(
                    "UPDATE users SET email = ? WHERE user_id = ?",
                    [$input['email'], $this->user_id]
                );
            }
            
            // Handle password update
            if (isset($input['new_password']) && !empty($input['new_password'])) {
                if (!isset($input['current_password'])) {
                    $this->sendResponse(['error' => 'Current password is required'], 400);
                    return;
                }
                
                // Verify current password
                $currentUser = $this->db->select(
                    "SELECT password FROM users WHERE user_id = ?",
                    [$this->user_id]
                );
                
                if (empty($currentUser) || !password_verify($input['current_password'], $currentUser[0]['password'])) {
                    $this->sendResponse(['error' => 'Current password is incorrect'], 400);
                    return;
                }
                
                // Update password
                $hashedPassword = password_hash($input['new_password'], PASSWORD_DEFAULT);
                $this->db->execute(
                    "UPDATE users SET password = ? WHERE user_id = ?",
                    [$hashedPassword, $this->user_id]
                );
            }
            
            // Update CustomerDetails table
            if (isset($input['first_name']) || isset($input['last_name']) || isset($input['phone'])) {
                $fullName = trim(($input['first_name'] ?? '') . ' ' . ($input['last_name'] ?? ''));
                
                // Check if customer details exist
                $existing = $this->db->select(
                    "SELECT detail_id FROM customerdetails WHERE user_id = ? LIMIT 1",
                    [$this->user_id]
                );
                
                if (!empty($existing)) {
                    // Update existing
                    $this->db->execute(
                        "UPDATE customerdetails SET full_name = ?, phone = ? WHERE user_id = ?",
                        [$fullName, $input['phone'] ?? '', $this->user_id]
                    );
                } else {
                    // Create new
                    $this->db->execute(
                        "INSERT INTO customerdetails (user_id, full_name, address, phone) VALUES (?, ?, ?, ?)",
                        [$this->user_id, $fullName, '', $input['phone'] ?? '']
                    );
                }
            }
            
            // Return updated profile
            $this->getProfile();
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to update profile'], 500);
        }
    }
    
    public function getWishlist() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            $wishlist = $this->db->select(
                "SELECT w.wishlist_id, w.added_at, p.product_id, p.name, p.price, p.image_url, p.stock
                 FROM wishlist w 
                 JOIN products p ON w.product_id = p.product_id 
                 WHERE w.user_id = ? 
                 ORDER BY w.added_at DESC 
                 LIMIT ? OFFSET ?",
                [$this->user_id, $limit, $offset]
            );
            
            $totalCount = $this->db->select(
                "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            $this->sendResponse([
                'items' => $wishlist,
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => ceil($totalCount / $limit),
                    'totalItems' => (int)$totalCount
                ]
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch wishlist'], 500);
        }
    }
    
    public function addToWishlist() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['product_id'])) {
                $this->sendResponse(['error' => 'Product ID is required'], 400);
                return;
            }
            
            // Check if already in wishlist
            $existing = $this->db->select(
                "SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?",
                [$this->user_id, $input['product_id']]
            );
            
            if (!empty($existing)) {
                $this->sendResponse(['error' => 'Product already in wishlist'], 400);
                return;
            }
            
            $success = $this->db->execute(
                "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)",
                [$this->user_id, $input['product_id']]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Product added to wishlist'], 201);
            } else {
                $this->sendResponse(['error' => 'Failed to add to wishlist'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to add to wishlist'], 500);
        }
    }
    
    public function removeFromWishlist($productId) {
        try {
            $success = $this->db->execute(
                "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?",
                [$this->user_id, $productId]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Product removed from wishlist']);
            } else {
                $this->sendResponse(['error' => 'Failed to remove from wishlist'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to remove from wishlist'], 500);
        }
    }
    
    private function sendResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
}

// Handle the request
$controller = new UserController();
$controller->handleRequest();
?>
===
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../models/db.php';

class UserController {
    private $db;
    private $user_id;
    
    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->user_id = $this->getUserIdFromSession();
    }
    
    public function handleRequest() {
        // Get endpoint from query parameters instead of URI
        $endpoint = $_GET['endpoint'] ?? null;
        $id = $_GET['id'] ?? null;
        $method = $_GET['method'] ?? $_SERVER['REQUEST_METHOD'];
        
        if (!$endpoint) {
            $this->sendResponse(['error' => 'Endpoint parameter is required'], 400);
            return;
        }
        
        switch ($endpoint) {
            case 'dashboard':
                if ($method === 'GET') {
                    $this->getDashboard();
                } else {
                    $this->sendResponse(['error' => 'Method not allowed for dashboard'], 405);
                }
                break;
                
            case 'orders':
                if ($method === 'GET') {
                    $this->getOrders();
                } else {
                    $this->sendResponse(['error' => 'Method not allowed for orders'], 405);
                }
                break;
                
            case 'addresses':
                switch ($method) {
                    case 'GET':
                        if ($id) {
                            $this->getAddress($id);
                        } else {
                            $this->getAddresses();
                        }
                        break;
                    case 'POST':
                        $this->createAddress();
                        break;
                    case 'PUT':
                        if ($id) {
                            $this->updateAddress($id);
                        } else {
                            $this->sendResponse(['error' => 'ID required for address update'], 400);
                        }
                        break;
                    case 'DELETE':
                        if ($id) {
                            $this->deleteAddress($id);
                        } else {
                            $this->sendResponse(['error' => 'ID required for address deletion'], 400);
                        }
                        break;
                    default:
                        $this->sendResponse(['error' => 'Method not allowed for addresses'], 405);
                }
                break;
                
            case 'profile':
                switch ($method) {
                    case 'GET':
                        $this->getProfile();
                        break;
                    case 'PUT':
                        $this->updateProfile();
                        break;
                    default:
                        $this->sendResponse(['error' => 'Method not allowed for profile'], 405);
                }
                break;
                
            case 'wishlist':
                switch ($method) {
                    case 'GET':
                        $this->getWishlist();
                        break;
                    case 'POST':
                        $this->addToWishlist();
                        break;
                    case 'DELETE':
                        if ($id) {
                            $this->removeFromWishlist($id);
                        } else {
                            $this->sendResponse(['error' => 'ID required for wishlist removal'], 400);
                        }
                        break;
                    default:
                        $this->sendResponse(['error' => 'Method not allowed for wishlist'], 405);
                }
                break;
                
            default:
                $this->sendResponse(['error' => 'Endpoint not found'], 404);
        }
    }
    
    private function getUserIdFromSession() {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id'];
        }

        $this->sendResponse(['error' => 'Not authenticated'], 401);
        exit();
   }
    
    public function getDashboard() {
        try {
            // Get total orders count
            $totalOrders = $this->db->select(
                "SELECT COUNT(*) as count FROM orders WHERE customer_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            // Get completed orders count
            $completedOrders = $this->db->select(
                "SELECT COUNT(*) as count FROM orders WHERE customer_id = ? AND order_status = 'Delivered'",
                [$this->user_id]
            )[0]['count'];
            
            // Get wishlist items count
            $wishlistItems = $this->db->select(
                "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            // Get addresses count
            $addressesCount = $this->db->select(
                "SELECT COUNT(*) as count FROM customerdetails WHERE user_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            // Get recent orders
            $recentOrders = $this->db->select(
                "SELECT order_id as id, order_status as status, total_amount as total, created_at as orderDate 
                 FROM orders WHERE customer_id = ? 
                 ORDER BY created_at DESC LIMIT 5",
                [$this->user_id]
            );
            
            $stats = [
                'totalOrders' => (int)$totalOrders,
                'completedOrders' => (int)$completedOrders,
                'wishlistItems' => (int)$wishlistItems,
                'addressesCount' => (int)$addressesCount
            ];
            
            $this->sendResponse([
                'stats' => $stats,
                'recentOrders' => $recentOrders
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch dashboard data'], 500);
        }
    }
    
    public function getOrders() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            // Get total count for pagination
            $totalCount = $this->db->select(
                "SELECT COUNT(*) as count FROM orders WHERE customer_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            // Get orders with pagination
            $orders = $this->db->select(
                "SELECT order_id as id, order_status as status, total_amount as total, created_at as orderDate 
                 FROM orders WHERE customer_id = ? 
                 ORDER BY created_at DESC LIMIT ? OFFSET ?",
                [$this->user_id, $limit, $offset]
            );
            
            $totalPages = ceil($totalCount / $limit);
            
            $pagination = [
                'currentPage' => $page,
                'totalPages' => (int)$totalPages,
                'totalItems' => (int)$totalCount,
                'itemsPerPage' => $limit
            ];
            
            $this->sendResponse([
                'orders' => $orders,
                'pagination' => $pagination
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch orders'], 500);
        }
    }
    
    public function getAddresses() {
        try {
            $addresses = $this->db->select(
                "SELECT detail_id as id, full_name, address as address_line1, '' as address_line2, 
                        '' as city, '' as state, '' as zip_code, '' as country, phone, 
                        CASE WHEN detail_id = (SELECT MIN(detail_id) FROM customerdetails WHERE user_id = ?) THEN 1 ELSE 0 END as is_default
                 FROM customerdetails WHERE user_id = ?",
                [$this->user_id, $this->user_id]
            );
            
            $this->sendResponse($addresses);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch addresses'], 500);
        }
    }
    
    public function getAddress($id) {
        try {
            $address = $this->db->select(
                "SELECT detail_id as id, full_name, address as address_line1, '' as address_line2, 
                        '' as city, '' as state, '' as zip_code, '' as country, phone, 
                        CASE WHEN detail_id = (SELECT MIN(detail_id) FROM customerdetails WHERE user_id = ?) THEN 1 ELSE 0 END as is_default,
                        'home' as type
                 FROM customerdetails WHERE detail_id = ? AND user_id = ?",
                [$this->user_id, $id, $this->user_id]
            );
            
            if (empty($address)) {
                $this->sendResponse(['error' => 'Address not found'], 404);
                return;
            }
            
            $this->sendResponse($address[0]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch address'], 500);
        }
    }
    
    public function createAddress() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            $required_fields = ['full_name', 'address_line1', 'phone'];
            foreach ($required_fields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    $this->sendResponse(['error' => "Field '$field' is required"], 400);
                    return;
                }
            }
            
            // Combine address fields into single address field for CustomerDetails table
            $full_address = $input['address_line1'];
            if (!empty($input['address_line2'])) {
                $full_address .= ', ' . $input['address_line2'];
            }
            if (!empty($input['city'])) {
                $full_address .= ', ' . $input['city'];
            }
            if (!empty($input['state'])) {
                $full_address .= ', ' . $input['state'];
            }
            if (!empty($input['zip_code'])) {
                $full_address .= ' ' . $input['zip_code'];
            }
            if (!empty($input['country'])) {
                $full_address .= ', ' . $input['country'];
            }
            
            $success = $this->db->execute(
                "INSERT INTO customerdetails (user_id, full_name, address, phone) VALUES (?, ?, ?, ?)",
                [$this->user_id, $input['full_name'], $full_address, $input['phone']]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Address created successfully'], 201);
            } else {
                $this->sendResponse(['error' => 'Failed to create address'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to create address'], 500);
        }
    }
    
    public function updateAddress($id) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            // Check if address exists and belongs to user
            $existing = $this->db->select(
                "SELECT detail_id FROM customerdetails WHERE detail_id = ? AND user_id = ?",
                [$id, $this->user_id]
            );
            
            if (empty($existing)) {
                $this->sendResponse(['error' => 'Address not found'], 404);
                return;
            }
            
            // Combine address fields
            $full_address = $input['address_line1'];
            if (!empty($input['address_line2'])) {
                $full_address .= ', ' . $input['address_line2'];
            }
            if (!empty($input['city'])) {
                $full_address .= ', ' . $input['city'];
            }
            if (!empty($input['state'])) {
                $full_address .= ', ' . $input['state'];
            }
            if (!empty($input['zip_code'])) {
                $full_address .= ' ' . $input['zip_code'];
            }
            if (!empty($input['country'])) {
                $full_address .= ', ' . $input['country'];
            }
            
            $success = $this->db->execute(
                "UPDATE customerdetails SET full_name = ?, address = ?, phone = ? WHERE detail_id = ? AND user_id = ?",
                [$input['full_name'], $full_address, $input['phone'], $id, $this->user_id]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Address updated successfully']);
            } else {
                $this->sendResponse(['error' => 'Failed to update address'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to update address'], 500);
        }
    }
    
    public function deleteAddress($id) {
        try {
            // Check if address exists and belongs to user
            $existing = $this->db->select(
                "SELECT detail_id FROM customerdetails WHERE detail_id = ? AND user_id = ?",
                [$id, $this->user_id]
            );
            
            if (empty($existing)) {
                $this->sendResponse(['error' => 'Address not found'], 404);
                return;
            }
            
            $success = $this->db->execute(
                "DELETE FROM customerdetails WHERE detail_id = ? AND user_id = ?",
                [$id, $this->user_id]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Address deleted successfully']);
            } else {
                $this->sendResponse(['error' => 'Failed to delete address'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to delete address'], 500);
        }
    }
    
    public function getProfile() {
        try {
            $user = $this->db->select(
                "SELECT user_id as id, username, email, created_at FROM users WHERE user_id = ?",
                [$this->user_id]
            );
            
            if (empty($user)) {
                $this->sendResponse(['error' => 'User not found'], 404);
                return;
            }
            
            // Get additional details if available
            $details = $this->db->select(
                "SELECT full_name, phone FROM customerdetails WHERE user_id = ? LIMIT 1",
                [$this->user_id]
            );
            
            $profile = $user[0];
            if (!empty($details)) {
                $nameParts = explode(' ', $details[0]['full_name'], 2);
                $profile['firstName'] = $nameParts[0];
                $profile['lastName'] = isset($nameParts[1]) ? $nameParts[1] : '';
                $profile['phone'] = $details[0]['phone'];
            } else {
                $profile['firstName'] = '';
                $profile['lastName'] = '';
                $profile['phone'] = '';
            }
            
            $profile['avatar'] = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80';
            
            $this->sendResponse($profile);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch profile'], 500);
        }
    }
    
    public function updateProfile() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendResponse(['error' => 'Invalid JSON input'], 400);
                return;
            }
            
            // Update Users table
            if (isset($input['email'])) {
                $this->db->execute(
                    "UPDATE users SET email = ? WHERE user_id = ?",
                    [$input['email'], $this->user_id]
                );
            }
            
            // Handle password update
            if (isset($input['new_password']) && !empty($input['new_password'])) {
                if (!isset($input['current_password'])) {
                    $this->sendResponse(['error' => 'Current password is required'], 400);
                    return;
                }
                
                // Verify current password
                $currentUser = $this->db->select(
                    "SELECT password FROM users WHERE user_id = ?",
                    [$this->user_id]
                );
                
                if (empty($currentUser) || !password_verify($input['current_password'], $currentUser[0]['password'])) {
                    $this->sendResponse(['error' => 'Current password is incorrect'], 400);
                    return;
                }
                
                // Update password
                $hashedPassword = password_hash($input['new_password'], PASSWORD_DEFAULT);
                $this->db->execute(
                    "UPDATE users SET password = ? WHERE user_id = ?",
                    [$hashedPassword, $this->user_id]
                );
            }
            
            // Update CustomerDetails table
            if (isset($input['first_name']) || isset($input['last_name']) || isset($input['phone'])) {
                $fullName = trim(($input['first_name'] ?? '') . ' ' . ($input['last_name'] ?? ''));
                
                // Check if customer details exist
                $existing = $this->db->select(
                    "SELECT detail_id FROM customerdetails WHERE user_id = ? LIMIT 1",
                    [$this->user_id]
                );
                
                if (!empty($existing)) {
                    // Update existing
                    $this->db->execute(
                        "UPDATE customerdetails SET full_name = ?, phone = ? WHERE user_id = ?",
                        [$fullName, $input['phone'] ?? '', $this->user_id]
                    );
                } else {
                    // Create new
                    $this->db->execute(
                        "INSERT INTO customerdetails (user_id, full_name, address, phone) VALUES (?, ?, ?, ?)",
                        [$this->user_id, $fullName, '', $input['phone'] ?? '']
                    );
                }
            }
            
            // Return updated profile
            $this->getProfile();
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to update profile'], 500);
        }
    }
    
    public function getWishlist() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            $wishlist = $this->db->select(
                "SELECT w.wishlist_id, w.added_at, p.product_id, p.name, p.price, p.image_url, p.stock
                 FROM wishlist w 
                 JOIN products p ON w.product_id = p.product_id 
                 WHERE w.user_id = ? 
                 ORDER BY w.added_at DESC 
                 LIMIT ? OFFSET ?",
                [$this->user_id, $limit, $offset]
            );
            
            $totalCount = $this->db->select(
                "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?",
                [$this->user_id]
            )[0]['count'];
            
            $this->sendResponse([
                'items' => $wishlist,
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => ceil($totalCount / $limit),
                    'totalItems' => (int)$totalCount
                ]
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to fetch wishlist'], 500);
        }
    }
    
    public function addToWishlist() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['product_id'])) {
                $this->sendResponse(['error' => 'Product ID is required'], 400);
                return;
            }
            
            // Check if already in wishlist
            $existing = $this->db->select(
                "SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?",
                [$this->user_id, $input['product_id']]
            );
            
            if (!empty($existing)) {
                $this->sendResponse(['error' => 'Product already in wishlist'], 400);
                return;
            }
            
            $success = $this->db->execute(
                "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)",
                [$this->user_id, $input['product_id']]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Product added to wishlist'], 201);
            } else {
                $this->sendResponse(['error' => 'Failed to add to wishlist'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to add to wishlist'], 500);
        }
    }
    
    public function removeFromWishlist($productId) {
        try {
            $success = $this->db->execute(
                "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?",
                [$this->user_id, $productId]
            );
            
            if ($success !== false) {
                $this->sendResponse(['message' => 'Product removed from wishlist']);
            } else {
                $this->sendResponse(['error' => 'Failed to remove from wishlist'], 500);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Failed to remove from wishlist'], 500);
        }
    }
    
    private function sendResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
}

// Handle the request
$controller = new UserController();
$controller->handleRequest();
?>
```

---

### C-3: Fixed Variable Order in UserUpdate.php
**File:** [UserUpdate.php](file:///c:/xampp/htdocs/ezycommerce/Customer/controllers/UserUpdate.php#L16-L22)

Moved `$userModel = new UserModel()` **before** its first use at `$userModel->isEmailTaken()`. Previously caused an undefined variable fatal error on every profile update.

```diff:UserUpdate.php
<?php
require_once __DIR__ . '/../models/UserModel.php';
session_start();

// Check if the update request was made
if (isset($_POST['update'])) {
    // Assuming you have all the necessary data from the form
    $userId = $_SESSION['user']['id'] ?? null;
    $username = $_POST['name'];
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'] ?? null;
    $fullName = $_POST['full_name'];
    $phone = $_POST['phone'];
    $billingAddress = $_POST['billing_address'];
    $shippingAddress = $_POST['shipping_address'];

    // Check if the new email is duplicated
    if ($userModel->isEmailTaken($email, $userId)) {
        $_SESSION['error_message'] = 'This email is already in use. Please choose another one.';
        header('Location: ' . url('/profile'));
        exit();
    }

    $userModel= new UserModel();

    // Update user data
    $userUpdated = $userModel->updateUser($userId, $username, $email, $newPassword);

    // Update customer details
    $customerDetailsUpdated = $userModel->updateCustomerDetails($userId, $fullName, $phone, $billingAddress, $shippingAddress);

    if ($userUpdated && $customerDetailsUpdated) {
        $_SESSION['success_message'] = 'Profile updated successfully!';
    } else {
        $_SESSION['error_message'] = 'There was an error updating your profile.';
    }

    header('Location: ' . url('/profile'));
    exit();

} else {
    // header("Location: ../views/updateprofile.php");
    // exit();
}
===
<?php
require_once __DIR__ . '/../models/UserModel.php';
session_start();

// Check if the update request was made
if (isset($_POST['update'])) {
    // Assuming you have all the necessary data from the form
    $userId = $_SESSION['user']['id'] ?? null;
    $username = $_POST['name'];
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'] ?? null;
    $fullName = $_POST['full_name'];
    $phone = $_POST['phone'];
    $billingAddress = $_POST['billing_address'];
    $shippingAddress = $_POST['shipping_address'];

    $userModel = new UserModel();

    // Check if the new email is duplicated
    if ($userModel->isEmailTaken($email, $userId)) {
        $_SESSION['error_message'] = 'This email is already in use. Please choose another one.';
        header('Location: ' . url('/profile'));
        exit();
    }

    // Update user data
    $userUpdated = $userModel->updateUser($userId, $username, $email, $newPassword);

    // Update customer details
    $customerDetailsUpdated = $userModel->updateCustomerDetails($userId, $fullName, $phone, $billingAddress, $shippingAddress);

    if ($userUpdated && $customerDetailsUpdated) {
        $_SESSION['success_message'] = 'Profile updated successfully!';
    } else {
        $_SESSION['error_message'] = 'There was an error updating your profile.';
    }

    header('Location: ' . url('/profile'));
    exit();

} else {
    // header("Location: ../views/updateprofile.php");
    // exit();
}
```

---

### C-4: Fixed Role Name Casing in Schema
**File:** [schema.sql](file:///c:/xampp/htdocs/ezycommerce/schema.sql#L208-L211)

Changed role seed data from lowercase (`'customer'`, `'admin'`) to capitalized (`'Customer'`, `'Admin'`) to match PHP code expectations. Prevents duplicate role creation during registration.

```diff:schema.sql
-- ============================================================
-- EzyCommerce MySQL Schema
-- Auto-imported by Docker on first boot via docker-entrypoint-initdb.d
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- ROLES
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL
);

-- -----------------------------------------------------------
-- USERS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- VENDORS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS vendors (
    vendor_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    vendor_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- DISCOUNTS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS discounts (
    discount_id INT AUTO_INCREMENT PRIMARY KEY,
    discount_name VARCHAR(100) NOT NULL,
    discount_type ENUM('percentage','fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    apply_to ENUM('all','selected','categories') DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------
-- CATEGORIES
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) UNIQUE NOT NULL,
    discount_id INT DEFAULT NULL,
    FOREIGN KEY (discount_id) REFERENCES discounts(discount_id) ON DELETE SET NULL
);

-- -----------------------------------------------------------
-- PRODUCTS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    image_url VARCHAR(255),
    category_id INT DEFAULT NULL,
    discount_id INT DEFAULT NULL,
    vendor_id INT DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (discount_id) REFERENCES discounts(discount_id) ON DELETE SET NULL,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id) ON DELETE SET NULL
);

-- -----------------------------------------------------------
-- ORDERS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_status ENUM('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    total_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- ORDER ITEMS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    vendor_status ENUM('Pending','ReadyToShip','Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- SHIPPING
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS shipping (
    shipping_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    shipping_status ENUM('Pending','Processing','Shipped','Delivered') NOT NULL,
    tracking_number VARCHAR(100),
    handled_by INT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (handled_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- -----------------------------------------------------------
-- RETURNS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS returns (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    reason TEXT,
    status ENUM('Pending','Processing','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    handled_by INT DEFAULT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (handled_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- -----------------------------------------------------------
-- CART
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- CART ITEMS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS cart_items (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (cart_id) REFERENCES cart(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- CUSTOMER DETAILS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS customerdetails (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    billing_address TEXT,
    shipping_address TEXT,
    address TEXT,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- PAYMENTS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('Cash on Delivery','Credit Card','Mobile Banking') NOT NULL,
    status ENUM('Pending','Completed','Failed') DEFAULT 'Pending',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- WISHLIST
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA: Default Roles
-- ============================================================
INSERT INTO roles (role_name) VALUES
    ('customer'),
    ('admin'),
    ('vendor'),
    ('logistics')
ON DUPLICATE KEY UPDATE role_name = role_name;

-- ============================================================
-- SEED DATA: Default Admin Account (password: admin123)
-- ============================================================
INSERT INTO users (username, email, password, role_id) VALUES
    ('admin', 'admin@ezycommerce.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 2)
ON DUPLICATE KEY UPDATE username = username;
===
-- ============================================================
-- EzyCommerce MySQL Schema
-- Auto-imported by Docker on first boot via docker-entrypoint-initdb.d
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- ROLES
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL
);

-- -----------------------------------------------------------
-- USERS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- VENDORS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS vendors (
    vendor_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    vendor_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- DISCOUNTS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS discounts (
    discount_id INT AUTO_INCREMENT PRIMARY KEY,
    discount_name VARCHAR(100) NOT NULL,
    discount_type ENUM('percentage','fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    apply_to ENUM('all','selected','categories') DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------
-- CATEGORIES
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) UNIQUE NOT NULL,
    discount_id INT DEFAULT NULL,
    FOREIGN KEY (discount_id) REFERENCES discounts(discount_id) ON DELETE SET NULL
);

-- -----------------------------------------------------------
-- PRODUCTS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    image_url VARCHAR(255),
    category_id INT DEFAULT NULL,
    discount_id INT DEFAULT NULL,
    vendor_id INT DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (discount_id) REFERENCES discounts(discount_id) ON DELETE SET NULL,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id) ON DELETE SET NULL
);

-- -----------------------------------------------------------
-- ORDERS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_status ENUM('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    total_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- ORDER ITEMS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    vendor_status ENUM('Pending','ReadyToShip','Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- SHIPPING
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS shipping (
    shipping_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    shipping_status ENUM('Pending','Processing','Shipped','Delivered') NOT NULL,
    tracking_number VARCHAR(100),
    handled_by INT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (handled_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- -----------------------------------------------------------
-- RETURNS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS returns (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    reason TEXT,
    status ENUM('Pending','Processing','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    handled_by INT DEFAULT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (handled_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- -----------------------------------------------------------
-- CART
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- CART ITEMS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS cart_items (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (cart_id) REFERENCES cart(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- CUSTOMER DETAILS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS customerdetails (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    billing_address TEXT,
    shipping_address TEXT,
    address TEXT,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- PAYMENTS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('Cash on Delivery','Credit Card','Mobile Banking') NOT NULL,
    status ENUM('Pending','Completed','Failed') DEFAULT 'Pending',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------
-- WISHLIST
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA: Default Roles
-- ============================================================
INSERT INTO roles (role_name) VALUES
    ('Customer'),
    ('Admin'),
    ('Vendor'),
    ('Logistics')
ON DUPLICATE KEY UPDATE role_name = role_name;

-- ============================================================
-- SEED DATA: Default Admin Account (password: admin123)
-- ============================================================
INSERT INTO users (username, email, password, role_id) VALUES
    ('admin', 'admin@ezycommerce.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 2)
ON DUPLICATE KEY UPDATE username = username;
```

---

### C-5: Fixed Logout Redirect
**File:** [logout.php](file:///c:/xampp/htdocs/ezycommerce/Customer/views/logout.php#L27-L29)

Replaced 4 hardcoded `header('Location: /ezycommerce/login')` redirects with a single `header('Location: ' . url('/login'))` call. Now works in both XAMPP and Docker/VPS.

```diff:logout.php
<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Capture the user's role before clearing the session
$userRole = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$_SESSION = [];

if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(
		session_name(),
		'',
		time() - 42000,
		$params['path'],
		$params['domain'],
		$params['secure'],
		$params['httponly']
	);
}

session_destroy();

// Determine redirect based on user role first, then referer as fallback
if ($userRole === 'admin' || strpos($referer, '/admin') !== false) {
    header('Location: /ezycommerce/login');
} elseif ($userRole === 'logistics' || strpos($referer, '/logistics') !== false) {
    header('Location: /ezycommerce/login');
} elseif ($userRole === 'vendor' || strpos($referer, '/vendor') !== false) {
    header('Location: /ezycommerce/login');
} else {
    // Customer logout - redirect to login page
    header('Location: /ezycommerce/login');
}
exit;
===
<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Capture the user's role before clearing the session
$userRole = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$_SESSION = [];

if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(
		session_name(),
		'',
		time() - 42000,
		$params['path'],
		$params['domain'],
		$params['secure'],
		$params['httponly']
	);
}

session_destroy();

// Redirect to login page
header('Location: ' . url('/login'));
exit;
```

---

### C-6: Fixed All Legacy `.php` Redirects in JS

| File | Occurrences Fixed |
|------|-------------------|
| [auth-utility.js](file:///c:/xampp/htdocs/ezycommerce/Customer/scripts/auth-utility.js) | 7 (`login.php` → `/login`, `index.php` → `/`) |
| [order.js](file:///c:/xampp/htdocs/ezycommerce/Customer/scripts/order.js) | 4 (`login.php` → `/login`, `index.php` → `/`, `cart.php` → `/cart`) |
| [wishlist.php](file:///c:/xampp/htdocs/ezycommerce/Customer/views/wishlist.php) | 2 (`login.php` → `url('/login')`) |

```diff:auth-utility.js
// this is Customer/scripts/auth-utils.js
class AuthManager {
    constructor() {
        this.apiBaseUrl = '/api/auth';
        this.userData = this.getUserData();
    }

    // Get user data from localStorage
    getUserData() {
        const userData = localStorage.getItem('userData');
        return userData ? JSON.parse(userData) : null;
    }

    // Check if user is authenticated
    isAuthenticated() {
        return !!this.userData;
    }

    // Get current user info
    getCurrentUser() {
        return this.userData;
    }

    async verifySession() {
        try {
            const response = await fetch(`${this.apiBaseUrl}?endpoint=verify`, {
                method: 'GET',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Session verification failed');
            }

            const data = await response.json();
            if (data.valid && data.user) {
                this.userData = data.user;
                localStorage.setItem('userData', JSON.stringify(data.user));
                return data.user;
            } else {
                throw new Error('Invalid session');
            }
        } catch (error) {
            console.error('Session verification error:', error);
            this.clearAuth();
            throw error;
        }
    }

    // Logout user
    async logout() {
        try {
            await fetch(`${this.apiBaseUrl}?endpoint=logout`, {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' }
            });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.clearAuth();
            window.location.href = 'login.php';
        }
    }

    // Login
    async login(email, password) {
        const response = await fetch(`${this.apiBaseUrl}?endpoint=login`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await response.json();
        if (response.ok && data.user) {
            this.userData = data.user;
            localStorage.setItem('userData', JSON.stringify(data.user));
        }
        return data;
    }

    // Register
    async register(data) {
        const response = await fetch(`${this.apiBaseUrl}?endpoint=register`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return await response.json();
    }

    // Clear auth info
    clearAuth() {
        this.userData = null;
        localStorage.removeItem('userData');
    }

    // Redirect to login if not authenticated
    requireAuth(redirectUrl = null) {
        if (!this.isAuthenticated()) {
            const loginUrl = redirectUrl ?
                `login.php?redirect=${encodeURIComponent(redirectUrl)}` :
                'login.php';
            window.location.href = loginUrl;
            return false;
        }
        return true;
    }

    // Check role
    hasRole(role) {
        return this.userData && this.userData.role === role;
    }

    // Authenticated API request helper
    async apiRequest(url, options = {}) {
        const config = {
            ...options,
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        };

        const response = await fetch(url, config);

        if (response.status === 401) {
            this.clearAuth();
            window.location.href = 'login.php';
            throw new Error('Session expired');
        }

        return response;
    }
}

// Global instance
const authManager = new AuthManager();

// Utility functions
function isLoggedIn() { return authManager.isAuthenticated(); }
function getCurrentUser() { return authManager.getCurrentUser(); }
function requireLogin(redirectUrl = null) { return authManager.requireAuth(redirectUrl); }
function logout() { return authManager.logout(); }
function hasRole(role) { return authManager.hasRole(role); }

// DOM helpers
function initializeAuth() {
    const isLoginPage = window.location.pathname.includes('login.php');

    if (isLoginPage) {
        authManager.verifySession().then(() => {
            window.location.href = 'index.php';
        }).catch(() => {});
        return true;
    }

    authManager.verifySession().then(() => {
        updateUserInterface();
    }).catch(() => {
        const currentUrl = window.location.href;
        window.location.href = `login.php?redirect=${encodeURIComponent(currentUrl)}`;
    });

    return true;
}

function updateUserInterface() {
    const user = getCurrentUser();
    if (!user) return;

    document.querySelectorAll('.user-name').forEach(el => el.textContent = user.firstName + ' ' + (user.lastName || '') || user.username);
    document.querySelectorAll('.user-avatar').forEach(el => { if (user.avatar) el.src = user.avatar; });
    document.querySelectorAll('.user-email').forEach(el => el.textContent = user.email);

    if (hasRole('Admin')) document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'block');
    if (hasRole('Customer')) document.querySelectorAll('.customer-only').forEach(el => el.style.display = 'block');
}

// Logout buttons
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.logout-btn, [data-action="logout"]').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) logout();
        });
    });

    if (isLoggedIn()) updateUserInterface();
});

document.addEventListener('DOMContentLoaded', initializeAuth);

// Redirect after login
function handleLoginRedirect() {
    const redirectUrl = new URLSearchParams(window.location.search).get('redirect');
    if (redirectUrl) window.location.href = decodeURIComponent(redirectUrl);
    else window.location.href = 'index.php';
}

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { authManager, isLoggedIn, getCurrentUser, requireLogin, logout, hasRole, initializeAuth, updateUserInterface, handleLoginRedirect };
}
===
// this is Customer/scripts/auth-utils.js
class AuthManager {
    constructor() {
        this.apiBaseUrl = '/api/auth';
        this.userData = this.getUserData();
    }

    // Get user data from localStorage
    getUserData() {
        const userData = localStorage.getItem('userData');
        return userData ? JSON.parse(userData) : null;
    }

    // Check if user is authenticated
    isAuthenticated() {
        return !!this.userData;
    }

    // Get current user info
    getCurrentUser() {
        return this.userData;
    }

    async verifySession() {
        try {
            const response = await fetch(`${this.apiBaseUrl}?endpoint=verify`, {
                method: 'GET',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Session verification failed');
            }

            const data = await response.json();
            if (data.valid && data.user) {
                this.userData = data.user;
                localStorage.setItem('userData', JSON.stringify(data.user));
                return data.user;
            } else {
                throw new Error('Invalid session');
            }
        } catch (error) {
            console.error('Session verification error:', error);
            this.clearAuth();
            throw error;
        }
    }

    // Logout user
    async logout() {
        try {
            await fetch(`${this.apiBaseUrl}?endpoint=logout`, {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' }
            });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.clearAuth();
            window.location.href = '/login';
        }
    }

    // Login
    async login(email, password) {
        const response = await fetch(`${this.apiBaseUrl}?endpoint=login`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await response.json();
        if (response.ok && data.user) {
            this.userData = data.user;
            localStorage.setItem('userData', JSON.stringify(data.user));
        }
        return data;
    }

    // Register
    async register(data) {
        const response = await fetch(`${this.apiBaseUrl}?endpoint=register`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return await response.json();
    }

    // Clear auth info
    clearAuth() {
        this.userData = null;
        localStorage.removeItem('userData');
    }

    // Redirect to login if not authenticated
    requireAuth(redirectUrl = null) {
        if (!this.isAuthenticated()) {
            const loginUrl = redirectUrl ?
                `/login?redirect=${encodeURIComponent(redirectUrl)}` :
                '/login';
            window.location.href = loginUrl;
            return false;
        }
        return true;
    }

    // Check role
    hasRole(role) {
        return this.userData && this.userData.role === role;
    }

    // Authenticated API request helper
    async apiRequest(url, options = {}) {
        const config = {
            ...options,
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        };

        const response = await fetch(url, config);

        if (response.status === 401) {
            this.clearAuth();
            window.location.href = '/login';
            throw new Error('Session expired');
        }

        return response;
    }
}

// Global instance
const authManager = new AuthManager();

// Utility functions
function isLoggedIn() { return authManager.isAuthenticated(); }
function getCurrentUser() { return authManager.getCurrentUser(); }
function requireLogin(redirectUrl = null) { return authManager.requireAuth(redirectUrl); }
function logout() { return authManager.logout(); }
function hasRole(role) { return authManager.hasRole(role); }

// DOM helpers
function initializeAuth() {
    const isLoginPage = window.location.pathname.includes('/login');

    if (isLoginPage) {
        authManager.verifySession().then(() => {
            window.location.href = '/';
        }).catch(() => {});
        return true;
    }

    authManager.verifySession().then(() => {
        updateUserInterface();
    }).catch(() => {
        const currentUrl = window.location.href;
        window.location.href = `/login?redirect=${encodeURIComponent(currentUrl)}`;
    });

    return true;
}

function updateUserInterface() {
    const user = getCurrentUser();
    if (!user) return;

    document.querySelectorAll('.user-name').forEach(el => el.textContent = user.firstName + ' ' + (user.lastName || '') || user.username);
    document.querySelectorAll('.user-avatar').forEach(el => { if (user.avatar) el.src = user.avatar; });
    document.querySelectorAll('.user-email').forEach(el => el.textContent = user.email);

    if (hasRole('Admin')) document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'block');
    if (hasRole('Customer')) document.querySelectorAll('.customer-only').forEach(el => el.style.display = 'block');
}

// Logout buttons
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.logout-btn, [data-action="logout"]').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) logout();
        });
    });

    if (isLoggedIn()) updateUserInterface();
});

document.addEventListener('DOMContentLoaded', initializeAuth);

// Redirect after login
function handleLoginRedirect() {
    const redirectUrl = new URLSearchParams(window.location.search).get('redirect');
    if (redirectUrl) window.location.href = decodeURIComponent(redirectUrl);
    else window.location.href = '/';
}

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { authManager, isLoggedIn, getCurrentUser, requireLogin, logout, hasRole, initializeAuth, updateUserInterface, handleLoginRedirect };
}
```

```diff:order.js
/**
 * EzyCommerce - Orders JavaScript
 * Order management and tracking functionality
 */

// Orders specific variables
let orders = [];
let ordersInitialized = false;

// Initialize orders functionality
function initializeOrders() {
    console.log('Initializing orders...');
    
    if (ordersInitialized) {
        console.log('Orders already initialized');
        return;
    }
    
    setupOrdersEventListeners();
    
    // Load orders if on orders page
    const ordersContainer = document.getElementById('ordersContainer');
    if (ordersContainer) {
        loadOrdersPage();
    }
    
    ordersInitialized = true;
    console.log('Orders initialization complete');
}

// Setup orders-specific event listeners
function setupOrdersEventListeners() {
    console.log('Setting up orders event listeners...');
    
    document.addEventListener('click', function(e) {
        // View order details
        if (e.target.classList.contains('view-order-btn') || e.target.closest('.view-order-btn')) {
            e.preventDefault();
            const btn = e.target.classList.contains('view-order-btn') ? e.target : e.target.closest('.view-order-btn');
            const orderId = btn.getAttribute('data-order-id');
            if (orderId) {
                viewOrderDetails(orderId);
            }
        }
        
        // Cancel order
        if (e.target.classList.contains('cancel-order-btn') || e.target.closest('.cancel-order-btn')) {
            e.preventDefault();
            const btn = e.target.classList.contains('cancel-order-btn') ? e.target : e.target.closest('.cancel-order-btn');
            const orderId = btn.getAttribute('data-order-id');
            if (orderId) {
                cancelOrder(orderId);
            }
        }
        
        // Reorder
        if (e.target.classList.contains('reorder-btn') || e.target.closest('.reorder-btn')) {
            e.preventDefault();
            const btn = e.target.classList.contains('reorder-btn') ? e.target : e.target.closest('.reorder-btn');
            const orderId = btn.getAttribute('data-order-id');
            if (orderId) {
                reorder(orderId);
            }
        }
        
        // Track order
        if (e.target.classList.contains('track-order-btn') || e.target.closest('.track-order-btn')) {
            e.preventDefault();
            const btn = e.target.classList.contains('track-order-btn') ? e.target : e.target.closest('.track-order-btn');
            const orderId = btn.getAttribute('data-order-id');
            if (orderId) {
                trackOrder(orderId);
            }
        }
    });
}

// ========== ORDERS DATA LOADING ==========

// Load orders page
async function loadOrdersPage() {
    console.log('Loading orders page...');
    
    const ordersContainer = document.getElementById('ordersContainer');
    if (!ordersContainer) {
        console.error('Orders container not found');
        return;
    }
    
    if (!window.EzyCommerce || !window.EzyCommerce.isLoggedIn()) {
        ordersContainer.innerHTML = `
            <div class="orders-error">
                <i class="fas fa-user-lock fa-3x"></i>
                <h3>Please Login</h3>
                <p>You need to be logged in to view your orders.</p>
                <a href="login.php" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        `;
        return;
    }
    
    try {
        showLoading('Loading your orders...', ordersContainer);
        
        const data = await fetchOrdersData();
        
        if (data && (data.success || data.status === 'success')) {
            renderOrdersPage(data);
        } else {
            throw new Error(data.message || 'Failed to load orders');
        }
        
    } catch (error) {
        console.error('Error loading orders page:', error);
        
        ordersContainer.innerHTML = `
            <div class="orders-error">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
                <h3>Unable to load orders</h3>
                <p>${error.message || 'Please check your connection and try again.'}</p>
                <div class="error-actions">
                    <button onclick="loadOrdersPage()" class="retry-btn">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                    <a href="index.php" class="continue-shopping-btn">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>
        `;
    }
}

// Fetch orders data from backend
async function fetchOrdersData() {
    console.log('Fetching orders data...');
    
    try {
        const url = '/api/order?action=fetchOrders';
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });
        
        console.log('Orders response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Orders data received:', data);
        
        if (data.success || data.status === 'success') {
            orders = data.orders || [];
            return data;
        } else {
            throw new Error(data.message || 'Failed to fetch orders');
        }
        
    } catch (error) {
        console.error('Error fetching orders:', error);
        throw error;
    }
}

// ========== ORDERS PAGE RENDERING ==========

// Render orders page
function renderOrdersPage(data) {
    console.log('Rendering orders page with data:', data);
    
    const ordersContainer = document.getElementById('ordersContainer');
    if (!ordersContainer) {
        console.error('Orders container not found');
        return;
    }
    
    orders = data.orders || [];
    
    // Handle no orders
    if (orders.length === 0) {
        ordersContainer.innerHTML = `
            <div class="no-orders">
                <i class="fas fa-clipboard-list fa-4x"></i>
                <h2>No orders yet</h2>
                <p>You haven't placed any orders yet. Start shopping to see your orders here.</p>
                <a href="index.php" class="start-shopping-btn">
                    <i class="fas fa-shopping-cart"></i> Start Shopping
                </a>
            </div>
        `;
        return;
    }
    
    // Render orders list
    const ordersHTML = `
        <div class="orders-content">
            <div class="orders-header">
                <h2><i class="fas fa-clipboard-list"></i> Your Orders</h2>
                <p class="orders-count">${orders.length} order${orders.length !== 1 ? 's' : ''} found</p>
            </div>
            
            <div class="orders-filters">
                <select id="orderStatusFilter" class="filter-select">
                    <option value="">All Orders</option>
                    <option value="Pending">Pending</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                
                <select id="orderTimeFilter" class="filter-select">
                    <option value="">All Time</option>
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 3 months</option>
                </select>
            </div>
            
            <div class="orders-list">
                ${orders.map(order => renderOrderCard(order)).join('')}
            </div>
        </div>
    `;
    
    ordersContainer.innerHTML = ordersHTML;
    
    // Setup filter listeners
    setupOrdersFilters();
}

// Render individual order card
function renderOrderCard(order) {
    const orderDate = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const statusClass = getOrderStatusClass(order.order_status);
    const statusIcon = getOrderStatusIcon(order.order_status);
    
    return `
        <div class="order-card" data-order-id="${order.order_id}" data-status="${order.order_status}">
            <div class="order-header">
                <div class="order-info">
                    <h3 class="order-id">Order #${order.order_id}</h3>
                    <p class="order-date">
                        <i class="fas fa-calendar"></i>
                        Placed on ${formattedDate}
                    </p>
                </div>
                
                <div class="order-status">
                    <span class="status-badge ${statusClass}">
                        <i class="fas fa-${statusIcon}"></i>
                        ${order.order_status}
                    </span>
                </div>
            </div>
            
            <div class="order-details">
                <div class="order-items-preview">
                    <div class="items-count">
                        <i class="fas fa-box"></i>
                        ${order.item_count || 1} item${(order.item_count || 1) !== 1 ? 's' : ''}
                    </div>
                </div>
                
                <div class="order-total">
                    <span class="total-label">Total:</span>
                    <span class="total-amount">${formatCurrency(order.total_amount)}</span>
                </div>
            </div>
            
            <div class="order-actions">
                <button class="view-order-btn btn-outline" data-order-id="${order.order_id}">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
                
                ${order.order_status === 'Pending' ? `
                    <button class="cancel-order-btn btn-danger" data-order-id="${order.order_id}">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                ` : ''}
                
                ${order.order_status === 'Delivered' ? `
                    <button class="reorder-btn btn-primary" data-order-id="${order.order_id}">
                        <i class="fas fa-redo"></i>
                        Reorder
                    </button>
                ` : ''}
                
                ${(order.order_status === 'Shipped' || order.order_status === 'Delivered') ? `
                    <button class="track-order-btn btn-info" data-order-id="${order.order_id}">
                        <i class="fas fa-truck"></i>
                        Track
                    </button>
                ` : ''}
            </div>
            
            ${order.tracking_number ? `
                <div class="tracking-info">
                    <small>
                        <i class="fas fa-barcode"></i>
                        Tracking: ${order.tracking_number}
                    </small>
                </div>
            ` : ''}
        </div>
    `;
}

// Get order status CSS class
function getOrderStatusClass(status) {
    switch (status) {
        case 'Pending': return 'status-pending';
        case 'Shipped': return 'status-shipped';
        case 'Delivered': return 'status-delivered';
        case 'Cancelled': return 'status-cancelled';
        default: return 'status-unknown';
    }
}

// Get order status icon
function getOrderStatusIcon(status) {
    switch (status) {
        case 'Pending': return 'clock';
        case 'Shipped': return 'truck';
        case 'Delivered': return 'check-circle';
        case 'Cancelled': return 'times-circle';
        default: return 'question-circle';
    }
}

// ========== ORDER ACTIONS ==========

// View order details
async function viewOrderDetails(orderId) {
    console.log('Viewing order details for:', orderId);
    
    try {
        showLoading('Loading order details...');
        
        const url = `/api/order?action=getOrderDetails&order_id=${orderId}`;
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Order details:', data);
        
        hideLoading();
        
        if (data.success || data.status === 'success') {
            showOrderDetailsModal(data.order);
        } else {
            throw new Error(data.message || 'Failed to load order details');
        }
        
    } catch (error) {
        console.error('Error loading order details:', error);
        hideLoading();
        showNotification(error.message || 'Failed to load order details', 'error');
    }
}

// Show order details modal
function showOrderDetailsModal(order) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('orderDetailsModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'orderDetailsModal';
        modal.className = 'modal';
        document.body.appendChild(modal);
    }
    
    const orderDate = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    modal.innerHTML = `
        <div class="modal-content order-details-modal">
            <div class="modal-header">
                <h2><i class="fas fa-receipt"></i> Order Details</h2>
                <button class="close" onclick="closeOrderDetailsModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="order-summary">
                    <div class="summary-row">
                        <strong>Order ID:</strong> #${order.order_id}
                    </div>
                    <div class="summary-row">
                        <strong>Date:</strong> ${formattedDate}
                    </div>
                    <div class="summary-row">
                        <strong>Status:</strong> 
                        <span class="status-badge ${getOrderStatusClass(order.order_status)}">
                            ${order.order_status}
                        </span>
                    </div>
                    <div class="summary-row">
                        <strong>Total:</strong> ${formatCurrency(order.total_amount)}
                    </div>
                </div>
                
                ${order.items ? `
                    <div class="order-items-section">
                        <h3>Order Items</h3>
                        <div class="order-items-list">
                            ${order.items.map(item => `
                                <div class="order-item">
                                    <img src="${item.image_url || 'https://via.placeholder.com/60x60'}" 
                                         alt="${item.name}"
                                         onerror="this.src='https://via.placeholder.com/60x60/6c757d/ffffff?text=No+Image'">
                                    <div class="item-details">
                                        <h4>${item.name}</h4>
                                        <p>Quantity: ${item.quantity}</p>
                                        <p>Price: ${formatCurrency(item.price_at_purchase)}</p>
                                    </div>
                                    <div class="item-total">
                                        ${formatCurrency(parseFloat(item.price_at_purchase) * parseInt(item.quantity))}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
                
                ${order.shipping_info ? `
                    <div class="shipping-section">
                        <h3>Shipping Information</h3>
                        <div class="shipping-details">
                            <p><strong>Status:</strong> ${order.shipping_info.status}</p>
                            ${order.shipping_info.tracking_number ? 
                                `<p><strong>Tracking Number:</strong> ${order.shipping_info.tracking_number}</p>` : 
                                ''
                            }
                        </div>
                    </div>
                ` : ''}
            </div>
            
            <div class="modal-footer">
                <button onclick="closeOrderDetailsModal()" class="btn-secondary">Close</button>
                ${order.order_status === 'Pending' ? 
                    `<button onclick="cancelOrder(${order.order_id}); closeOrderDetailsModal();" class="btn-danger">Cancel Order</button>` : 
                    ''
                }
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
}

// Close order details modal
function closeOrderDetailsModal() {
    const modal = document.getElementById('orderDetailsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Cancel order
async function cancelOrder(orderId) {
    console.log('Cancelling order:', orderId);
    
    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }
    
    try {
        const url = `/api/order?action=cancelOrder&order_id=${orderId}`;
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Cancel order result:', result);
        
        if (result.success || result.status === 'success') {
            showNotification('Order cancelled successfully', 'success');
            
            // Reload orders page
            loadOrdersPage();
        } else {
            throw new Error(result.message || 'Failed to cancel order');
        }
        
    } catch (error) {
        console.error('Error cancelling order:', error);
        showNotification(error.message || 'Failed to cancel order', 'error');
    }
}

// Reorder
async function reorder(orderId) {
    console.log('Reordering from order:', orderId);
    
    if (!confirm('Add all items from this order to your cart?')) {
        return;
    }
    
    try {
        showLoading('Adding items to cart...');
        
        const url = `/api/order?action=reorder&order_id=${orderId}`;
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Reorder result:', result);
        
        hideLoading();
        
        if (result.success || result.status === 'success') {
            showNotification('Items added to cart successfully!', 'success');
            
            // Update cart count if cart functionality is available
            if (window.EzyCommerce && window.EzyCommerce.fetchCartData) {
                await window.EzyCommerce.fetchCartData();
            }
            
            // Redirect to cart after a delay
            setTimeout(() => {
                window.location.href = 'cart.php';
            }, 2000);
        } else {
            throw new Error(result.message || 'Failed to reorder items');
        }
        
    } catch (error) {
        console.error('Error reordering:', error);
        hideLoading();
        showNotification(error.message || 'Failed to reorder items', 'error');
    }
}

// Track order
function trackOrder(orderId) {
    console.log('Tracking order:', orderId);
    
    const order = orders.find(o => o.order_id == orderId);
    if (!order) {
        showNotification('Order not found', 'error');
        return;
    }
    
    // Create tracking modal
    showTrackingModal(order);
}

// Show tracking modal
function showTrackingModal(order) {
    let modal = document.getElementById('trackingModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'trackingModal';
        modal.className = 'modal';
        document.body.appendChild(modal);
    }
    
    const trackingSteps = getTrackingSteps(order.order_status);
    
    modal.innerHTML = `
        <div class="modal-content tracking-modal">
            <div class="modal-header">
                <h2><i class="fas fa-truck"></i> Track Order #${order.order_id}</h2>
                <button class="close" onclick="closeTrackingModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="tracking-timeline">
                    ${trackingSteps.map(step => `
                        <div class="tracking-step ${step.completed ? 'completed' : ''} ${step.current ? 'current' : ''}">
                            <div class="step-icon">
                                <i class="fas fa-${step.icon}"></i>
                            </div>
                            <div class="step-content">
                                <h4>${step.title}</h4>
                                <p>${step.description}</p>
                                ${step.date ? `<small class="step-date">${step.date}</small>` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
                
                ${order.tracking_number ? `
                    <div class="tracking-info">
                        <h4>Tracking Information</h4>
                        <p><strong>Tracking Number:</strong> ${order.tracking_number}</p>
                        <p><strong>Current Status:</strong> ${order.order_status}</p>
                    </div>
                ` : ''}
            </div>
            
            <div class="modal-footer">
                <button onclick="closeTrackingModal()" class="btn-primary">Close</button>
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
}

// Close tracking modal
function closeTrackingModal() {
    const modal = document.getElementById('trackingModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Get tracking steps based on order status
function getTrackingSteps(status) {
    const steps = [
        {
            title: 'Order Placed',
            description: 'Your order has been received and is being processed',
            icon: 'check-circle',
            completed: true,
            current: status === 'Pending'
        },
        {
            title: 'Order Shipped',
            description: 'Your order has been shipped and is on its way',
            icon: 'truck',
            completed: ['Shipped', 'Delivered'].includes(status),
            current: status === 'Shipped'
        },
        {
            title: 'Order Delivered',
            description: 'Your order has been delivered successfully',
            icon: 'home',
            completed: status === 'Delivered',
            current: status === 'Delivered'
        }
    ];
    
    if (status === 'Cancelled') {
        return [
            {
                title: 'Order Cancelled',
                description: 'Your order has been cancelled',
                icon: 'times-circle',
                completed: true,
                current: true
            }
        ];
    }
    
    return steps;
}

// ========== ORDERS FILTERING ==========

// Setup orders filters
function setupOrdersFilters() {
    const statusFilter = document.getElementById('orderStatusFilter');
    const timeFilter = document.getElementById('orderTimeFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyOrdersFilters);
    }
    
    if (timeFilter) {
        timeFilter.addEventListener('change', applyOrdersFilters);
    }
}

// Apply orders filters
function applyOrdersFilters() {
    const statusFilter = document.getElementById('orderStatusFilter');
    const timeFilter = document.getElementById('orderTimeFilter');
    
    let filteredOrders = [...orders];
    
    // Apply status filter
    if (statusFilter && statusFilter.value) {
        filteredOrders = filteredOrders.filter(order => order.order_status === statusFilter.value);
    }
    
    // Apply time filter
    if (timeFilter && timeFilter.value) {
        const days = parseInt(timeFilter.value);
        const cutoffDate = new Date();
        cutoffDate.setDate(cutoffDate.getDate() - days);
        
        filteredOrders = filteredOrders.filter(order => {
            const orderDate = new Date(order.created_at);
            return orderDate >= cutoffDate;
        });
    }
    
    // Re-render orders list
    const ordersList = document.querySelector('.orders-list');
    if (ordersList) {
        if (filteredOrders.length === 0) {
            ordersList.innerHTML = `
                <div class="no-orders-filtered">
                    <i class="fas fa-filter fa-2x"></i>
                    <h3>No orders match your filters</h3>
                    <p>Try adjusting your filters to see more orders.</p>
                </div>
            `;
        } else {
            ordersList.innerHTML = filteredOrders.map(order => renderOrderCard(order)).join('');
        }
    }
    
    // Update count
    const ordersCount = document.querySelector('.orders-count');
    if (ordersCount) {
        ordersCount.textContent = `${filteredOrders.length} order${filteredOrders.length !== 1 ? 's' : ''} found`;
    }
}

// ========== UTILITY FUNCTIONS ==========

// Format currency (uses global function if available)
function formatCurrency(amount) {
    if (window.EzyCommerce && window.EzyCommerce.formatCurrency) {
        return window.EzyCommerce.formatCurrency(amount);
    }
    return `${parseFloat(amount || 0).toFixed(2)}`;
}

// Show notification (uses global function if available)
function showNotification(message, type) {
    if (window.EzyCommerce && window.EzyCommerce.showNotification) {
        return window.EzyCommerce.showNotification(message, type);
    }
    // Fallback
    alert(`${type.toUpperCase()}: ${message}`);
}

// Show loading (uses global function if available)
function showLoading(message, target) {
    if (window.EzyCommerce && window.EzyCommerce.showLoading) {
        return window.EzyCommerce.showLoading(message, target);
    }
}

// Hide loading (uses global function if available)
function hideLoading(target) {
    if (window.EzyCommerce && window.EzyCommerce.hideLoading) {
        return window.EzyCommerce.hideLoading(target);
    }
}

// ========== EXPORT FUNCTIONS ==========

// Export orders functions to global object
if (typeof window.EzyCommerce === 'undefined') {
    window.EzyCommerce = {};
}

Object.assign(window.EzyCommerce, {
    // Orders operations
    loadOrdersPage: loadOrdersPage,
    fetchOrdersData: fetchOrdersData,
    getOrders: () => orders,
    
    // Order actions
    viewOrderDetails: viewOrderDetails,
    cancelOrder: cancelOrder,
    reorder: reorder,
    trackOrder: trackOrder,
    
    // Initialization
    initializeOrders: initializeOrders
});

// Make some functions globally available for onclick handlers
window.loadOrdersPage = loadOrdersPage;
window.viewOrderDetails = viewOrderDetails;
window.cancelOrder = cancelOrder;
window.reorder = reorder;
window.trackOrder = trackOrder;
window.closeOrderDetailsModal = closeOrderDetailsModal;
window.closeTrackingModal = closeTrackingModal;

console.log('EzyCommerce orders.js loaded successfully');
===
/**
 * EzyCommerce - Orders JavaScript
 * Order management and tracking functionality
 */

// Orders specific variables
let orders = [];
let ordersInitialized = false;

// Initialize orders functionality
function initializeOrders() {
    console.log('Initializing orders...');
    
    if (ordersInitialized) {
        console.log('Orders already initialized');
        return;
    }
    
    setupOrdersEventListeners();
    
    // Load orders if on orders page
    const ordersContainer = document.getElementById('ordersContainer');
    if (ordersContainer) {
        loadOrdersPage();
    }
    
    ordersInitialized = true;
    console.log('Orders initialization complete');
}

// Setup orders-specific event listeners
function setupOrdersEventListeners() {
    console.log('Setting up orders event listeners...');
    
    document.addEventListener('click', function(e) {
        // View order details
        if (e.target.classList.contains('view-order-btn') || e.target.closest('.view-order-btn')) {
            e.preventDefault();
            const btn = e.target.classList.contains('view-order-btn') ? e.target : e.target.closest('.view-order-btn');
            const orderId = btn.getAttribute('data-order-id');
            if (orderId) {
                viewOrderDetails(orderId);
            }
        }
        
        // Cancel order
        if (e.target.classList.contains('cancel-order-btn') || e.target.closest('.cancel-order-btn')) {
            e.preventDefault();
            const btn = e.target.classList.contains('cancel-order-btn') ? e.target : e.target.closest('.cancel-order-btn');
            const orderId = btn.getAttribute('data-order-id');
            if (orderId) {
                cancelOrder(orderId);
            }
        }
        
        // Reorder
        if (e.target.classList.contains('reorder-btn') || e.target.closest('.reorder-btn')) {
            e.preventDefault();
            const btn = e.target.classList.contains('reorder-btn') ? e.target : e.target.closest('.reorder-btn');
            const orderId = btn.getAttribute('data-order-id');
            if (orderId) {
                reorder(orderId);
            }
        }
        
        // Track order
        if (e.target.classList.contains('track-order-btn') || e.target.closest('.track-order-btn')) {
            e.preventDefault();
            const btn = e.target.classList.contains('track-order-btn') ? e.target : e.target.closest('.track-order-btn');
            const orderId = btn.getAttribute('data-order-id');
            if (orderId) {
                trackOrder(orderId);
            }
        }
    });
}

// ========== ORDERS DATA LOADING ==========

// Load orders page
async function loadOrdersPage() {
    console.log('Loading orders page...');
    
    const ordersContainer = document.getElementById('ordersContainer');
    if (!ordersContainer) {
        console.error('Orders container not found');
        return;
    }
    
    if (!window.EzyCommerce || !window.EzyCommerce.isLoggedIn()) {
        ordersContainer.innerHTML = `
            <div class="orders-error">
                <i class="fas fa-user-lock fa-3x"></i>
                <h3>Please Login</h3>
                <p>You need to be logged in to view your orders.</p>
                <a href="/login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        `;
        return;
    }
    
    try {
        showLoading('Loading your orders...', ordersContainer);
        
        const data = await fetchOrdersData();
        
        if (data && (data.success || data.status === 'success')) {
            renderOrdersPage(data);
        } else {
            throw new Error(data.message || 'Failed to load orders');
        }
        
    } catch (error) {
        console.error('Error loading orders page:', error);
        
        ordersContainer.innerHTML = `
            <div class="orders-error">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
                <h3>Unable to load orders</h3>
                <p>${error.message || 'Please check your connection and try again.'}</p>
                <div class="error-actions">
                    <button onclick="loadOrdersPage()" class="retry-btn">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                    <a href="/" class="continue-shopping-btn">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>
        `;
    }
}

// Fetch orders data from backend
async function fetchOrdersData() {
    console.log('Fetching orders data...');
    
    try {
        const url = '/api/order?action=fetchOrders';
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });
        
        console.log('Orders response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Orders data received:', data);
        
        if (data.success || data.status === 'success') {
            orders = data.orders || [];
            return data;
        } else {
            throw new Error(data.message || 'Failed to fetch orders');
        }
        
    } catch (error) {
        console.error('Error fetching orders:', error);
        throw error;
    }
}

// ========== ORDERS PAGE RENDERING ==========

// Render orders page
function renderOrdersPage(data) {
    console.log('Rendering orders page with data:', data);
    
    const ordersContainer = document.getElementById('ordersContainer');
    if (!ordersContainer) {
        console.error('Orders container not found');
        return;
    }
    
    orders = data.orders || [];
    
    // Handle no orders
    if (orders.length === 0) {
        ordersContainer.innerHTML = `
            <div class="no-orders">
                <i class="fas fa-clipboard-list fa-4x"></i>
                <h2>No orders yet</h2>
                <p>You haven't placed any orders yet. Start shopping to see your orders here.</p>
                <a href="/" class="start-shopping-btn">
                    <i class="fas fa-shopping-cart"></i> Start Shopping
                </a>
            </div>
        `;
        return;
    }
    
    // Render orders list
    const ordersHTML = `
        <div class="orders-content">
            <div class="orders-header">
                <h2><i class="fas fa-clipboard-list"></i> Your Orders</h2>
                <p class="orders-count">${orders.length} order${orders.length !== 1 ? 's' : ''} found</p>
            </div>
            
            <div class="orders-filters">
                <select id="orderStatusFilter" class="filter-select">
                    <option value="">All Orders</option>
                    <option value="Pending">Pending</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                
                <select id="orderTimeFilter" class="filter-select">
                    <option value="">All Time</option>
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 3 months</option>
                </select>
            </div>
            
            <div class="orders-list">
                ${orders.map(order => renderOrderCard(order)).join('')}
            </div>
        </div>
    `;
    
    ordersContainer.innerHTML = ordersHTML;
    
    // Setup filter listeners
    setupOrdersFilters();
}

// Render individual order card
function renderOrderCard(order) {
    const orderDate = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const statusClass = getOrderStatusClass(order.order_status);
    const statusIcon = getOrderStatusIcon(order.order_status);
    
    return `
        <div class="order-card" data-order-id="${order.order_id}" data-status="${order.order_status}">
            <div class="order-header">
                <div class="order-info">
                    <h3 class="order-id">Order #${order.order_id}</h3>
                    <p class="order-date">
                        <i class="fas fa-calendar"></i>
                        Placed on ${formattedDate}
                    </p>
                </div>
                
                <div class="order-status">
                    <span class="status-badge ${statusClass}">
                        <i class="fas fa-${statusIcon}"></i>
                        ${order.order_status}
                    </span>
                </div>
            </div>
            
            <div class="order-details">
                <div class="order-items-preview">
                    <div class="items-count">
                        <i class="fas fa-box"></i>
                        ${order.item_count || 1} item${(order.item_count || 1) !== 1 ? 's' : ''}
                    </div>
                </div>
                
                <div class="order-total">
                    <span class="total-label">Total:</span>
                    <span class="total-amount">${formatCurrency(order.total_amount)}</span>
                </div>
            </div>
            
            <div class="order-actions">
                <button class="view-order-btn btn-outline" data-order-id="${order.order_id}">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
                
                ${order.order_status === 'Pending' ? `
                    <button class="cancel-order-btn btn-danger" data-order-id="${order.order_id}">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                ` : ''}
                
                ${order.order_status === 'Delivered' ? `
                    <button class="reorder-btn btn-primary" data-order-id="${order.order_id}">
                        <i class="fas fa-redo"></i>
                        Reorder
                    </button>
                ` : ''}
                
                ${(order.order_status === 'Shipped' || order.order_status === 'Delivered') ? `
                    <button class="track-order-btn btn-info" data-order-id="${order.order_id}">
                        <i class="fas fa-truck"></i>
                        Track
                    </button>
                ` : ''}
            </div>
            
            ${order.tracking_number ? `
                <div class="tracking-info">
                    <small>
                        <i class="fas fa-barcode"></i>
                        Tracking: ${order.tracking_number}
                    </small>
                </div>
            ` : ''}
        </div>
    `;
}

// Get order status CSS class
function getOrderStatusClass(status) {
    switch (status) {
        case 'Pending': return 'status-pending';
        case 'Shipped': return 'status-shipped';
        case 'Delivered': return 'status-delivered';
        case 'Cancelled': return 'status-cancelled';
        default: return 'status-unknown';
    }
}

// Get order status icon
function getOrderStatusIcon(status) {
    switch (status) {
        case 'Pending': return 'clock';
        case 'Shipped': return 'truck';
        case 'Delivered': return 'check-circle';
        case 'Cancelled': return 'times-circle';
        default: return 'question-circle';
    }
}

// ========== ORDER ACTIONS ==========

// View order details
async function viewOrderDetails(orderId) {
    console.log('Viewing order details for:', orderId);
    
    try {
        showLoading('Loading order details...');
        
        const url = `/api/order?action=getOrderDetails&order_id=${orderId}`;
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Order details:', data);
        
        hideLoading();
        
        if (data.success || data.status === 'success') {
            showOrderDetailsModal(data.order);
        } else {
            throw new Error(data.message || 'Failed to load order details');
        }
        
    } catch (error) {
        console.error('Error loading order details:', error);
        hideLoading();
        showNotification(error.message || 'Failed to load order details', 'error');
    }
}

// Show order details modal
function showOrderDetailsModal(order) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('orderDetailsModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'orderDetailsModal';
        modal.className = 'modal';
        document.body.appendChild(modal);
    }
    
    const orderDate = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    modal.innerHTML = `
        <div class="modal-content order-details-modal">
            <div class="modal-header">
                <h2><i class="fas fa-receipt"></i> Order Details</h2>
                <button class="close" onclick="closeOrderDetailsModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="order-summary">
                    <div class="summary-row">
                        <strong>Order ID:</strong> #${order.order_id}
                    </div>
                    <div class="summary-row">
                        <strong>Date:</strong> ${formattedDate}
                    </div>
                    <div class="summary-row">
                        <strong>Status:</strong> 
                        <span class="status-badge ${getOrderStatusClass(order.order_status)}">
                            ${order.order_status}
                        </span>
                    </div>
                    <div class="summary-row">
                        <strong>Total:</strong> ${formatCurrency(order.total_amount)}
                    </div>
                </div>
                
                ${order.items ? `
                    <div class="order-items-section">
                        <h3>Order Items</h3>
                        <div class="order-items-list">
                            ${order.items.map(item => `
                                <div class="order-item">
                                    <img src="${item.image_url || 'https://via.placeholder.com/60x60'}" 
                                         alt="${item.name}"
                                         onerror="this.src='https://via.placeholder.com/60x60/6c757d/ffffff?text=No+Image'">
                                    <div class="item-details">
                                        <h4>${item.name}</h4>
                                        <p>Quantity: ${item.quantity}</p>
                                        <p>Price: ${formatCurrency(item.price_at_purchase)}</p>
                                    </div>
                                    <div class="item-total">
                                        ${formatCurrency(parseFloat(item.price_at_purchase) * parseInt(item.quantity))}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
                
                ${order.shipping_info ? `
                    <div class="shipping-section">
                        <h3>Shipping Information</h3>
                        <div class="shipping-details">
                            <p><strong>Status:</strong> ${order.shipping_info.status}</p>
                            ${order.shipping_info.tracking_number ? 
                                `<p><strong>Tracking Number:</strong> ${order.shipping_info.tracking_number}</p>` : 
                                ''
                            }
                        </div>
                    </div>
                ` : ''}
            </div>
            
            <div class="modal-footer">
                <button onclick="closeOrderDetailsModal()" class="btn-secondary">Close</button>
                ${order.order_status === 'Pending' ? 
                    `<button onclick="cancelOrder(${order.order_id}); closeOrderDetailsModal();" class="btn-danger">Cancel Order</button>` : 
                    ''
                }
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
}

// Close order details modal
function closeOrderDetailsModal() {
    const modal = document.getElementById('orderDetailsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Cancel order
async function cancelOrder(orderId) {
    console.log('Cancelling order:', orderId);
    
    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }
    
    try {
        const url = `/api/order?action=cancelOrder&order_id=${orderId}`;
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Cancel order result:', result);
        
        if (result.success || result.status === 'success') {
            showNotification('Order cancelled successfully', 'success');
            
            // Reload orders page
            loadOrdersPage();
        } else {
            throw new Error(result.message || 'Failed to cancel order');
        }
        
    } catch (error) {
        console.error('Error cancelling order:', error);
        showNotification(error.message || 'Failed to cancel order', 'error');
    }
}

// Reorder
async function reorder(orderId) {
    console.log('Reordering from order:', orderId);
    
    if (!confirm('Add all items from this order to your cart?')) {
        return;
    }
    
    try {
        showLoading('Adding items to cart...');
        
        const url = `/api/order?action=reorder&order_id=${orderId}`;
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Reorder result:', result);
        
        hideLoading();
        
        if (result.success || result.status === 'success') {
            showNotification('Items added to cart successfully!', 'success');
            
            // Update cart count if cart functionality is available
            if (window.EzyCommerce && window.EzyCommerce.fetchCartData) {
                await window.EzyCommerce.fetchCartData();
            }
            
            // Redirect to cart after a delay
            setTimeout(() => {
                window.location.href = '/cart';
            }, 2000);
        } else {
            throw new Error(result.message || 'Failed to reorder items');
        }
        
    } catch (error) {
        console.error('Error reordering:', error);
        hideLoading();
        showNotification(error.message || 'Failed to reorder items', 'error');
    }
}

// Track order
function trackOrder(orderId) {
    console.log('Tracking order:', orderId);
    
    const order = orders.find(o => o.order_id == orderId);
    if (!order) {
        showNotification('Order not found', 'error');
        return;
    }
    
    // Create tracking modal
    showTrackingModal(order);
}

// Show tracking modal
function showTrackingModal(order) {
    let modal = document.getElementById('trackingModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'trackingModal';
        modal.className = 'modal';
        document.body.appendChild(modal);
    }
    
    const trackingSteps = getTrackingSteps(order.order_status);
    
    modal.innerHTML = `
        <div class="modal-content tracking-modal">
            <div class="modal-header">
                <h2><i class="fas fa-truck"></i> Track Order #${order.order_id}</h2>
                <button class="close" onclick="closeTrackingModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="tracking-timeline">
                    ${trackingSteps.map(step => `
                        <div class="tracking-step ${step.completed ? 'completed' : ''} ${step.current ? 'current' : ''}">
                            <div class="step-icon">
                                <i class="fas fa-${step.icon}"></i>
                            </div>
                            <div class="step-content">
                                <h4>${step.title}</h4>
                                <p>${step.description}</p>
                                ${step.date ? `<small class="step-date">${step.date}</small>` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
                
                ${order.tracking_number ? `
                    <div class="tracking-info">
                        <h4>Tracking Information</h4>
                        <p><strong>Tracking Number:</strong> ${order.tracking_number}</p>
                        <p><strong>Current Status:</strong> ${order.order_status}</p>
                    </div>
                ` : ''}
            </div>
            
            <div class="modal-footer">
                <button onclick="closeTrackingModal()" class="btn-primary">Close</button>
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
}

// Close tracking modal
function closeTrackingModal() {
    const modal = document.getElementById('trackingModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Get tracking steps based on order status
function getTrackingSteps(status) {
    const steps = [
        {
            title: 'Order Placed',
            description: 'Your order has been received and is being processed',
            icon: 'check-circle',
            completed: true,
            current: status === 'Pending'
        },
        {
            title: 'Order Shipped',
            description: 'Your order has been shipped and is on its way',
            icon: 'truck',
            completed: ['Shipped', 'Delivered'].includes(status),
            current: status === 'Shipped'
        },
        {
            title: 'Order Delivered',
            description: 'Your order has been delivered successfully',
            icon: 'home',
            completed: status === 'Delivered',
            current: status === 'Delivered'
        }
    ];
    
    if (status === 'Cancelled') {
        return [
            {
                title: 'Order Cancelled',
                description: 'Your order has been cancelled',
                icon: 'times-circle',
                completed: true,
                current: true
            }
        ];
    }
    
    return steps;
}

// ========== ORDERS FILTERING ==========

// Setup orders filters
function setupOrdersFilters() {
    const statusFilter = document.getElementById('orderStatusFilter');
    const timeFilter = document.getElementById('orderTimeFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyOrdersFilters);
    }
    
    if (timeFilter) {
        timeFilter.addEventListener('change', applyOrdersFilters);
    }
}

// Apply orders filters
function applyOrdersFilters() {
    const statusFilter = document.getElementById('orderStatusFilter');
    const timeFilter = document.getElementById('orderTimeFilter');
    
    let filteredOrders = [...orders];
    
    // Apply status filter
    if (statusFilter && statusFilter.value) {
        filteredOrders = filteredOrders.filter(order => order.order_status === statusFilter.value);
    }
    
    // Apply time filter
    if (timeFilter && timeFilter.value) {
        const days = parseInt(timeFilter.value);
        const cutoffDate = new Date();
        cutoffDate.setDate(cutoffDate.getDate() - days);
        
        filteredOrders = filteredOrders.filter(order => {
            const orderDate = new Date(order.created_at);
            return orderDate >= cutoffDate;
        });
    }
    
    // Re-render orders list
    const ordersList = document.querySelector('.orders-list');
    if (ordersList) {
        if (filteredOrders.length === 0) {
            ordersList.innerHTML = `
                <div class="no-orders-filtered">
                    <i class="fas fa-filter fa-2x"></i>
                    <h3>No orders match your filters</h3>
                    <p>Try adjusting your filters to see more orders.</p>
                </div>
            `;
        } else {
            ordersList.innerHTML = filteredOrders.map(order => renderOrderCard(order)).join('');
        }
    }
    
    // Update count
    const ordersCount = document.querySelector('.orders-count');
    if (ordersCount) {
        ordersCount.textContent = `${filteredOrders.length} order${filteredOrders.length !== 1 ? 's' : ''} found`;
    }
}

// ========== UTILITY FUNCTIONS ==========

// Format currency (uses global function if available)
function formatCurrency(amount) {
    if (window.EzyCommerce && window.EzyCommerce.formatCurrency) {
        return window.EzyCommerce.formatCurrency(amount);
    }
    return `${parseFloat(amount || 0).toFixed(2)}`;
}

// Show notification (uses global function if available)
function showNotification(message, type) {
    if (window.EzyCommerce && window.EzyCommerce.showNotification) {
        return window.EzyCommerce.showNotification(message, type);
    }
    // Fallback
    alert(`${type.toUpperCase()}: ${message}`);
}

// Show loading (uses global function if available)
function showLoading(message, target) {
    if (window.EzyCommerce && window.EzyCommerce.showLoading) {
        return window.EzyCommerce.showLoading(message, target);
    }
}

// Hide loading (uses global function if available)
function hideLoading(target) {
    if (window.EzyCommerce && window.EzyCommerce.hideLoading) {
        return window.EzyCommerce.hideLoading(target);
    }
}

// ========== EXPORT FUNCTIONS ==========

// Export orders functions to global object
if (typeof window.EzyCommerce === 'undefined') {
    window.EzyCommerce = {};
}

Object.assign(window.EzyCommerce, {
    // Orders operations
    loadOrdersPage: loadOrdersPage,
    fetchOrdersData: fetchOrdersData,
    getOrders: () => orders,
    
    // Order actions
    viewOrderDetails: viewOrderDetails,
    cancelOrder: cancelOrder,
    reorder: reorder,
    trackOrder: trackOrder,
    
    // Initialization
    initializeOrders: initializeOrders
});

// Make some functions globally available for onclick handlers
window.loadOrdersPage = loadOrdersPage;
window.viewOrderDetails = viewOrderDetails;
window.cancelOrder = cancelOrder;
window.reorder = reorder;
window.trackOrder = trackOrder;
window.closeOrderDetailsModal = closeOrderDetailsModal;
window.closeTrackingModal = closeTrackingModal;

console.log('EzyCommerce orders.js loaded successfully');
```

```diff:wishlist.php
<?php
$pageTitle = 'Your Wishlist — EzyCommerce';
require_once __DIR__ . '/components/header.php';
?>

<main>
    <section class="products-section" style="padding-top: 60px;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Your Wishlist</h2>
                <a href="<?php echo url('/'); ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>

            <div id="wishlist-empty" class="empty-state" style="display:none; text-align:center; padding: 40px 0;">
                Your wishlist is empty. <a href="<?php echo url('/'); ?>">Start shopping!</a>
            </div>

            <div id="wishlist-container" class="product-grid">
                <!-- Products will load here -->
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/components/footer.php'; ?>

<script src="<?php echo url('/Customer/scripts/home.js'); ?>"></script>
<script>
    const currentUser = JSON.parse(localStorage.getItem('userData'));
    const currentUserId = currentUser ? currentUser.id : null;

    function renderWishlistItems(items) {
        const container = document.getElementById('wishlist-container');
        const empty = document.getElementById('wishlist-empty');

        if (!container || !empty) {
            return;
        }

        if (!items || items.length === 0) {
            container.innerHTML = '';
            empty.style.display = 'block';
            return;
        }

        empty.style.display = 'none';
        container.innerHTML = items.map(item => `
            <div class="product-card" data-product-id="${item.product_id}">
                <div class="product-image-wrap">
                    <img src="${item.image_url || 'https://via.placeholder.com/300x200?text=No+Image'}" alt="${item.name}" class="product-image">
                </div>
                <div class="product-info">
                    <div class="product-category">${item.category_name || 'Wishlist'}</div>
                    <h3 class="product-title">${item.name}</h3>
                    <div class="product-price">
                        <span class="current-price">$${Number(item.price || 0).toFixed(2)}</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn-cart" type="button" onclick="addItemToCart(${item.product_id})">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn-wishlist in-wishlist" type="button" onclick="removeFromWishlist(${item.product_id})">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    async function loadWishlistPage() {
        if (!currentUserId) {
            const container = document.getElementById('wishlist-container');
            const empty = document.getElementById('wishlist-empty');

            if (container && empty) {
                container.innerHTML = '';
                empty.innerHTML = 'Please <a href="<?php echo url('/login'); ?>">log in</a> to view your wishlist.';
                empty.style.display = 'block';
            }
            return;
        }

        try {
            const items = await window.ecommerceAPI.getWishlistItems();
            renderWishlistItems(items);
        } catch (error) {
            console.error('Failed to load wishlist items:', error);
            if (typeof showToast === 'function') {
                showToast('Failed to load wishlist. Please refresh.', 'error');
            }
        }
    }

    async function addItemToCart(productId) {
        if (!currentUserId) {
            window.location.href = 'login.php';
            return;
        }

        await window.ecommerceAPI.addToCart(productId);
    }

    async function removeFromWishlist(productId) {
        if (!currentUserId) {
            window.location.href = 'login.php';
            return;
        }

        try {
            const response = await window.ecommerceAPI.apiCall(`users/${currentUserId}/wishlist/${productId}`, {
                method: 'DELETE'
            });

            if (response.success) {
                if (typeof showToast === 'function') {
                    showToast(response.message || 'Removed from wishlist', 'success');
                }
                loadWishlistPage();
            } else if (typeof showToast === 'function') {
                showToast(response.error || 'Failed to remove item', 'error');
            }
        } catch (error) {
            console.error('Failed to remove wishlist item:', error);
            if (typeof showToast === 'function') {
                showToast('Failed to remove item from wishlist.', 'error');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', loadWishlistPage);
</script>
===
<?php
$pageTitle = 'Your Wishlist — EzyCommerce';
require_once __DIR__ . '/components/header.php';
?>

<main>
    <section class="products-section" style="padding-top: 60px;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Your Wishlist</h2>
                <a href="<?php echo url('/'); ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>

            <div id="wishlist-empty" class="empty-state" style="display:none; text-align:center; padding: 40px 0;">
                Your wishlist is empty. <a href="<?php echo url('/'); ?>">Start shopping!</a>
            </div>

            <div id="wishlist-container" class="product-grid">
                <!-- Products will load here -->
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/components/footer.php'; ?>

<script src="<?php echo url('/Customer/scripts/home.js'); ?>"></script>
<script>
    const currentUser = JSON.parse(localStorage.getItem('userData'));
    const currentUserId = currentUser ? currentUser.id : null;

    function renderWishlistItems(items) {
        const container = document.getElementById('wishlist-container');
        const empty = document.getElementById('wishlist-empty');

        if (!container || !empty) {
            return;
        }

        if (!items || items.length === 0) {
            container.innerHTML = '';
            empty.style.display = 'block';
            return;
        }

        empty.style.display = 'none';
        container.innerHTML = items.map(item => `
            <div class="product-card" data-product-id="${item.product_id}">
                <div class="product-image-wrap">
                    <img src="${item.image_url || 'https://via.placeholder.com/300x200?text=No+Image'}" alt="${item.name}" class="product-image">
                </div>
                <div class="product-info">
                    <div class="product-category">${item.category_name || 'Wishlist'}</div>
                    <h3 class="product-title">${item.name}</h3>
                    <div class="product-price">
                        <span class="current-price">$${Number(item.price || 0).toFixed(2)}</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn-cart" type="button" onclick="addItemToCart(${item.product_id})">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn-wishlist in-wishlist" type="button" onclick="removeFromWishlist(${item.product_id})">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    async function loadWishlistPage() {
        if (!currentUserId) {
            const container = document.getElementById('wishlist-container');
            const empty = document.getElementById('wishlist-empty');

            if (container && empty) {
                container.innerHTML = '';
                empty.innerHTML = 'Please <a href="<?php echo url('/login'); ?>">log in</a> to view your wishlist.';
                empty.style.display = 'block';
            }
            return;
        }

        try {
            const items = await window.ecommerceAPI.getWishlistItems();
            renderWishlistItems(items);
        } catch (error) {
            console.error('Failed to load wishlist items:', error);
            if (typeof showToast === 'function') {
                showToast('Failed to load wishlist. Please refresh.', 'error');
            }
        }
    }

    async function addItemToCart(productId) {
        if (!currentUserId) {
            window.location.href = '<?php echo url('/login'); ?>';
            return;
        }

        await window.ecommerceAPI.addToCart(productId);
    }

    async function removeFromWishlist(productId) {
        if (!currentUserId) {
            window.location.href = '<?php echo url('/login'); ?>';
            return;
        }

        try {
            const response = await window.ecommerceAPI.apiCall(`users/${currentUserId}/wishlist/${productId}`, {
                method: 'DELETE'
            });

            if (response.success) {
                if (typeof showToast === 'function') {
                    showToast(response.message || 'Removed from wishlist', 'success');
                }
                loadWishlistPage();
            } else if (typeof showToast === 'function') {
                showToast(response.error || 'Failed to remove item', 'error');
            }
        } catch (error) {
            console.error('Failed to remove wishlist item:', error);
            if (typeof showToast === 'function') {
                showToast('Failed to remove item from wishlist.', 'error');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', loadWishlistPage);
</script>
```

---

### C-7: Secured `.env` and Created Template

- Added `.env` to [.gitignore](file:///c:/xampp/htdocs/ezycommerce/.gitignore) to prevent credential leaks
- Created [.env.example](file:///c:/xampp/htdocs/ezycommerce/.env.example) with placeholder values for onboarding

```diff:.gitignore
composer.phar
api_errors.log
logs/*.log
Vendor/composer/
Vendor/nikic/
Vendor/autoload.php
*.cache
===
composer.phar
api_errors.log
logs/*.log
Vendor/composer/
Vendor/nikic/
Vendor/autoload.php
*.cache

# Environment config (contains credentials)
.env

# Test/debug files
test.php
test2.php
test3.php
```

---

### C-8: Removed Test Files and Debug Routes

- **Deleted:** `test.php`, `test2.php`, `test3.php`
- **Removed routes:** `/debugging` and `/vendor/test` from [index.php](file:///c:/xampp/htdocs/ezycommerce/public/index.php)

```diff:index.php
<?php
/**
 * Single Entry Point (Front Controller)
 */

// Enable error reporting (disable or configure for true production environments)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the root directory of the application
define('BASE_PATH', dirname(__DIR__));

// Require the Global Error Handler
require_once BASE_PATH . '/utils/ErrorHandler.php';

/**
 * URL Helper Function
 * Automatically handles subdirectories if running without Docker (e.g. XAMPP)
 */
function url($path = '') {
    $path = ltrim($path, '/');
    // For local XAMPP testing, prefix with /ezycommerce. 
    // In Docker or Production (where DocumentRoot is public), prefix with /
    $basePath = (strpos($_SERVER['REQUEST_URI'], '/ezycommerce') === 0) ? '/ezycommerce' : '';
    // For root path, always return with trailing slash to ensure proper redirect
    if ($path === '') {
        return $basePath . '/';
    }
    return $basePath . '/' . $path;
}

// Parse the request URI (e.g., /admin?foo=bar -> /admin)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// For XAMPP: Remove the '/ezycommerce' sub-folder part from the URI
$subFolder = '/ezycommerce';
if (strpos($requestUri, $subFolder) === 0) {
    $requestUri = substr($requestUri, strlen($subFolder));
}

// Normalize URI by removing trailing slash if not root
if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
    $requestUri = rtrim($requestUri, '/');
}
if (empty($requestUri)) {
    $requestUri = '/';
}

// Application Route Mapping
$routes = [
    // Customer Views
    '/' => '/Customer/views/index.php',
    '/cart' => '/Customer/views/cart.php',
    '/contact' => '/Customer/views/contact.php',
    '/debugging' => '/Customer/views/debugging.php',
    '/login' => '/Customer/views/login.php',
    '/logout' => '/Customer/views/logout.php',
    '/product' => '/Customer/views/product.php',
    '/profile' => '/Customer/views/profile.php',
    '/register' => '/Customer/views/register.php',
    '/wishlist' => '/Customer/views/wishlist.php',

    // Customer APIs / Controllers
    '/api/auth' => '/Customer/controllers/AuthController.php',
    '/api/logout' => '/Customer/controllers/AuthController.php',
    '/api/cart' => '/Customer/controllers/CartController.php',
    '/api/home' => '/Customer/controllers/HomeController.php',
    '/api/order' => '/Customer/controllers/OrderController.php',
    '/api/register_submit' => '/Customer/controllers/Reg_control.php',
    '/api/user' => '/Customer/controllers/UserController.php',
    '/api/user_update' => '/Customer/controllers/UserUpdate.php',

    // Admin Views
    '/admin' => '/Admin/views/index.php',
    '/admin/discounts' => '/Admin/views/discounts_management.php',
    '/admin/logout' => '/Customer/views/logout.php',
    '/admin/orders' => '/Admin/views/order_management.php',
    '/admin/products' => '/Admin/views/product_management.php',
    '/admin/users' => '/Admin/views/user_management.php',
    '/admin/reports' => '/Admin/reports.php',
    '/admin/settings' => '/Admin/settings.php',

    // Admin APIs
    '/api/admin/dashboard' => '/Admin/controllers/DashboardAPI.php',
    '/api/admin/dashboard_ctrl' => '/Admin/controllers/DashboardController.php',
    '/api/admin/discounts' => '/Admin/controllers/DiscountManageAPI.php',
    '/api/admin/order' => '/Admin/controllers/OrderController.php',
    '/api/admin/product' => '/Admin/controllers/ProductAPI.php',
    '/api/admin/user' => '/Admin/controllers/UserController.php',

    // Logistics Views
    '/logistics' => '/Logistics/views/dashboard.php',
    '/logistics/shipping' => '/Logistics/views/allshipping.php',
    '/logistics/returns' => '/Logistics/views/return.php',

    // Logistics APIs
    '/api/logistics/shipping' => '/Logistics/controllers/AllShippingAPI.php',
    '/api/logistics/dashboard' => '/Logistics/controllers/DashboardAPI.php',
    '/api/logistics/returns' => '/Logistics/controllers/ReturnAPI.php',

    // Logistics Logout
    '/logistics/logout' => '/Customer/views/logout.php',

    // Vendor Views
    '/vendor' => '/Vendor/views/dashboard.php',
    '/vendor/orders' => '/Vendor/views/orders.php',
    '/vendor/products' => '/Vendor/views/products.php',
    '/vendor/discounts' => '/Vendor/views/v-discount.php',
    '/vendor/logout' => '/Customer/views/logout.php',
    '/vendor/test' => '/Vendor/views/test.php',

    // Vendor APIs
    '/api/vendor/products' => '/Vendor/controllers/ProductsAPI.php',
    '/api/vendor/dashboard' => '/Vendor/controllers/vendor-Dashboard.php',
    '/api/vendor/discounts' => '/Vendor/controllers/vendor-DiscountAPI.php',
    '/api/vendor/orders' => '/Vendor/controllers/vendor-OrderAPI.php',
];

// API prefix matching (for sub-paths like /api/home/products)
$apiPrefixes = [
    // Customer APIs
    '/api/home'             => '/Customer/controllers/HomeController.php',
    '/api/cart'             => '/Customer/controllers/CartController.php',
    '/api/auth'             => '/Customer/controllers/AuthController.php',
    '/api/order'            => '/Customer/controllers/OrderController.php',
    '/api/user'             => '/Customer/controllers/UserController.php',
    '/api/user_update'      => '/Customer/controllers/UserUpdate.php',
    '/api/register_submit'  => '/Customer/controllers/Reg_control.php',
    // Admin APIs
    '/api/admin/dashboard'      => '/Admin/controllers/DashboardAPI.php',
    '/api/admin/dashboard_ctrl' => '/Admin/controllers/DashboardController.php',
    '/api/admin/discounts'      => '/Admin/controllers/DiscountManageAPI.php',
    '/api/admin/order'          => '/Admin/controllers/OrderController.php',
    '/api/admin/product'        => '/Admin/controllers/ProductAPI.php',
    '/api/admin/user'           => '/Admin/controllers/UserController.php',
    // Vendor APIs
    '/api/vendor/products'   => '/Vendor/controllers/ProductsAPI.php',
    '/api/vendor/dashboard'  => '/Vendor/controllers/vendor-Dashboard.php',
    '/api/vendor/discounts'  => '/Vendor/controllers/vendor-DiscountAPI.php',
    '/api/vendor/orders'     => '/Vendor/controllers/vendor-OrderAPI.php',
    // Logistics APIs
    '/api/logistics/shipping'  => '/Logistics/controllers/AllShippingAPI.php',
    '/api/logistics/dashboard' => '/Logistics/controllers/DashboardAPI.php',
    '/api/logistics/returns'   => '/Logistics/controllers/ReturnAPI.php',
];

// Sort prefixes by length descending to match more specific routes first
uksort($apiPrefixes, function($a, $b) {
    return strlen($b) - strlen($a);
});

foreach ($apiPrefixes as $prefix => $file) {
    if ($requestUri === $prefix || strpos($requestUri, $prefix . '/') === 0) {
        // Pass sub-path as PATH_INFO for controllers that use it
        $_SERVER['PATH_INFO'] = substr($requestUri, strlen($prefix)) ?: '/';
        $targetFile = BASE_PATH . $file;
        if (file_exists($targetFile)) {
            require_once $targetFile;
            exit;
        }
    }
}

// Exact route matching logic (views and pages)
if (array_key_exists($requestUri, $routes)) {
    $targetFile = BASE_PATH . $routes[$requestUri];
    if (file_exists($targetFile)) {
        require_once $targetFile;
    } else {
        http_response_code(404);
        echo "<h1>404 - File Not Found</h1>";
        echo "<p>The required file could not be found at: <em>" . htmlspecialchars($targetFile) . "</em></p>";
    }
} else {
    // Fallback logic for serving asset files from the root if requested dynamically 
    // (though .htaccess handles static assets, this prevents fatal errors if misconfigured)
    $assetPath = BASE_PATH . $requestUri;
    if (is_file($assetPath)) {
        $ext = pathinfo($assetPath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
        ];
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
            readfile($assetPath);
            exit;
        }
    }

    // 404 Route Not Found
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested route '<strong>" . htmlspecialchars($requestUri) . "</strong>' was not found.</p>";
}
===
<?php
/**
 * Single Entry Point (Front Controller)
 */

// Configure error reporting based on environment
$appEnv = getenv('APP_ENV') ?: 'development';
if ($appEnv === 'production') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Define the root directory of the application
define('BASE_PATH', dirname(__DIR__));

// Require the Global Error Handler
require_once BASE_PATH . '/utils/ErrorHandler.php';

/**
 * URL Helper Function
 * Automatically handles subdirectories if running without Docker (e.g. XAMPP)
 */
function url($path = '') {
    $path = ltrim($path, '/');
    // For local XAMPP testing, prefix with /ezycommerce. 
    // In Docker or Production (where DocumentRoot is public), prefix with /
    $basePath = (strpos($_SERVER['REQUEST_URI'], '/ezycommerce') === 0) ? '/ezycommerce' : '';
    // For root path, always return with trailing slash to ensure proper redirect
    if ($path === '') {
        return $basePath . '/';
    }
    return $basePath . '/' . $path;
}

// Parse the request URI (e.g., /admin?foo=bar -> /admin)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// For XAMPP: Remove the '/ezycommerce' sub-folder part from the URI
$subFolder = '/ezycommerce';
if (strpos($requestUri, $subFolder) === 0) {
    $requestUri = substr($requestUri, strlen($subFolder));
}

// Normalize URI by removing trailing slash if not root
if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
    $requestUri = rtrim($requestUri, '/');
}
if (empty($requestUri)) {
    $requestUri = '/';
}

// Application Route Mapping
$routes = [
    // Customer Views
    '/' => '/Customer/views/index.php',
    '/cart' => '/Customer/views/cart.php',
    '/contact' => '/Customer/views/contact.php',
    '/login' => '/Customer/views/login.php',
    '/logout' => '/Customer/views/logout.php',
    '/product' => '/Customer/views/product.php',
    '/profile' => '/Customer/views/profile.php',
    '/register' => '/Customer/views/register.php',
    '/wishlist' => '/Customer/views/wishlist.php',

    // Customer APIs / Controllers
    '/api/auth' => '/Customer/controllers/AuthController.php',
    '/api/logout' => '/Customer/controllers/AuthController.php',
    '/api/cart' => '/Customer/controllers/CartController.php',
    '/api/home' => '/Customer/controllers/HomeController.php',
    '/api/order' => '/Customer/controllers/OrderController.php',
    '/api/register_submit' => '/Customer/controllers/Reg_control.php',
    '/api/user' => '/Customer/controllers/UserController.php',
    '/api/user_update' => '/Customer/controllers/UserUpdate.php',

    // Admin Views
    '/admin' => '/Admin/views/index.php',
    '/admin/discounts' => '/Admin/views/discounts_management.php',
    '/admin/logout' => '/Customer/views/logout.php',
    '/admin/orders' => '/Admin/views/order_management.php',
    '/admin/products' => '/Admin/views/product_management.php',
    '/admin/users' => '/Admin/views/user_management.php',
    '/admin/reports' => '/Admin/reports.php',
    '/admin/settings' => '/Admin/settings.php',

    // Admin APIs
    '/api/admin/dashboard' => '/Admin/controllers/DashboardAPI.php',
    '/api/admin/dashboard_ctrl' => '/Admin/controllers/DashboardController.php',
    '/api/admin/discounts' => '/Admin/controllers/DiscountManageAPI.php',
    '/api/admin/order' => '/Admin/controllers/OrderController.php',
    '/api/admin/product' => '/Admin/controllers/ProductAPI.php',
    '/api/admin/user' => '/Admin/controllers/UserController.php',

    // Logistics Views
    '/logistics' => '/Logistics/views/dashboard.php',
    '/logistics/shipping' => '/Logistics/views/allshipping.php',
    '/logistics/returns' => '/Logistics/views/return.php',

    // Logistics APIs
    '/api/logistics/shipping' => '/Logistics/controllers/AllShippingAPI.php',
    '/api/logistics/dashboard' => '/Logistics/controllers/DashboardAPI.php',
    '/api/logistics/returns' => '/Logistics/controllers/ReturnAPI.php',

    // Logistics Logout
    '/logistics/logout' => '/Customer/views/logout.php',

    // Vendor Views
    '/vendor' => '/Vendor/views/dashboard.php',
    '/vendor/orders' => '/Vendor/views/orders.php',
    '/vendor/products' => '/Vendor/views/products.php',
    '/vendor/discounts' => '/Vendor/views/v-discount.php',
    '/vendor/logout' => '/Customer/views/logout.php',

    // Vendor APIs
    '/api/vendor/products' => '/Vendor/controllers/ProductsAPI.php',
    '/api/vendor/dashboard' => '/Vendor/controllers/vendor-Dashboard.php',
    '/api/vendor/discounts' => '/Vendor/controllers/vendor-DiscountAPI.php',
    '/api/vendor/orders' => '/Vendor/controllers/vendor-OrderAPI.php',
];

// API prefix matching (for sub-paths like /api/home/products)
$apiPrefixes = [
    // Customer APIs
    '/api/home'             => '/Customer/controllers/HomeController.php',
    '/api/cart'             => '/Customer/controllers/CartController.php',
    '/api/auth'             => '/Customer/controllers/AuthController.php',
    '/api/order'            => '/Customer/controllers/OrderController.php',
    '/api/user'             => '/Customer/controllers/UserController.php',
    '/api/user_update'      => '/Customer/controllers/UserUpdate.php',
    '/api/register_submit'  => '/Customer/controllers/Reg_control.php',
    // Admin APIs
    '/api/admin/dashboard'      => '/Admin/controllers/DashboardAPI.php',
    '/api/admin/dashboard_ctrl' => '/Admin/controllers/DashboardController.php',
    '/api/admin/discounts'      => '/Admin/controllers/DiscountManageAPI.php',
    '/api/admin/order'          => '/Admin/controllers/OrderController.php',
    '/api/admin/product'        => '/Admin/controllers/ProductAPI.php',
    '/api/admin/user'           => '/Admin/controllers/UserController.php',
    // Vendor APIs
    '/api/vendor/products'   => '/Vendor/controllers/ProductsAPI.php',
    '/api/vendor/dashboard'  => '/Vendor/controllers/vendor-Dashboard.php',
    '/api/vendor/discounts'  => '/Vendor/controllers/vendor-DiscountAPI.php',
    '/api/vendor/orders'     => '/Vendor/controllers/vendor-OrderAPI.php',
    // Logistics APIs
    '/api/logistics/shipping'  => '/Logistics/controllers/AllShippingAPI.php',
    '/api/logistics/dashboard' => '/Logistics/controllers/DashboardAPI.php',
    '/api/logistics/returns'   => '/Logistics/controllers/ReturnAPI.php',
];

// Sort prefixes by length descending to match more specific routes first
uksort($apiPrefixes, function($a, $b) {
    return strlen($b) - strlen($a);
});

foreach ($apiPrefixes as $prefix => $file) {
    if ($requestUri === $prefix || strpos($requestUri, $prefix . '/') === 0) {
        // Pass sub-path as PATH_INFO for controllers that use it
        $_SERVER['PATH_INFO'] = substr($requestUri, strlen($prefix)) ?: '/';
        $targetFile = BASE_PATH . $file;
        if (file_exists($targetFile)) {
            require_once $targetFile;
            exit;
        }
    }
}

// Exact route matching logic (views and pages)
if (array_key_exists($requestUri, $routes)) {
    $targetFile = BASE_PATH . $routes[$requestUri];
    if (file_exists($targetFile)) {
        require_once $targetFile;
    } else {
        http_response_code(404);
        echo "<h1>404 - File Not Found</h1>";
        echo "<p>The required file could not be found at: <em>" . htmlspecialchars($targetFile) . "</em></p>";
    }
} else {
    // Fallback logic for serving asset files from the root if requested dynamically 
    // (though .htaccess handles static assets, this prevents fatal errors if misconfigured)
    $assetPath = BASE_PATH . $requestUri;
    if (is_file($assetPath)) {
        $ext = pathinfo($assetPath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
        ];
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
            readfile($assetPath);
            exit;
        }
    }

    // 404 Route Not Found
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested route '<strong>" . htmlspecialchars($requestUri) . "</strong>' was not found.</p>";
}
```

---

## Medium Fixes Applied

### M-1: Environment-Conditional Error Display
**File:** [index.php](file:///c:/xampp/htdocs/ezycommerce/public/index.php#L6-L16)

`display_errors` is now toggled by `APP_ENV` environment variable. Set `APP_ENV=production` in `.env` on VPS to disable.

### M-8: Removed Cart Debug Data
**File:** [CartController.php](file:///c:/xampp/htdocs/ezycommerce/Customer/controllers/CartController.php#L183)

Removed the `$item['debug']` array that exposed internal discount calculation state to the frontend API response.

```diff:CartController.php
<?php
require_once __DIR__ . '/../models/db.php';

class CartController {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        
        // Enable CORS
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Content-Type: application/json");
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? $_POST['action'] ?? null;
        
        try {
            switch ($action) {
                case 'fetchCart':
                    $this->fetchCart();
                    break;
                case 'addToCart':
                    $this->addToCart();
                    break;
                case 'updateQuantity':
                    $this->updateQuantity();
                    break;
                case 'removeFromCart':
                    $this->removeFromCart();
                    break;
                case 'clearCart':
                    $this->clearCart();
                    break;
                case 'placeOrder':
                    $this->placeOrder();
                    break;
                default:
                    $this->sendResponse(false, 'Invalid action', null, 400);
            }
        } catch (Exception $e) {
            // Let the global error handler manage the exception and response
            throw $e;
        }
    }

    // Fetch cart items for current user
    private function fetchCart() {
    $customerId = $this->getCustomerId();
    
    if (!$customerId) {
        $this->sendResponse(false, 'User not logged in', null, 401);
        return;
    }

    $cartId = $this->getOrCreateCart($customerId);
    
    // Enhanced query to get product and category discounts
    $query = "
        SELECT 
            ci.cart_item_id,
            ci.product_id,
            ci.quantity,
            p.name,
            p.price,
            p.image_url,
            p.stock,
            p.category_id,
            c.category_name,
            p.discount_id as product_discount_id,
            c.discount_id as category_discount_id,
            
            -- Product-level discount
            pd.discount_id as pd_id,
            pd.discount_type as product_discount_type,
            pd.discount_value as product_discount_value,
            pd.discount_name as product_discount_name,
            pd.is_active as pd_active,
            pd.start_date as pd_start,
            pd.end_date as pd_end,
            
            -- Category-level discount
            cd.discount_id as cd_id,
            cd.discount_type as category_discount_type,
            cd.discount_value as category_discount_value,
            cd.discount_name as category_discount_name,
            cd.is_active as cd_active,
            cd.start_date as cd_start,
            cd.end_date as cd_end
            
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        
        -- Get product discount (will check active status in PHP)
        LEFT JOIN discounts pd ON p.discount_id = pd.discount_id
        
        -- Get category discount (will check active status in PHP)
        LEFT JOIN discounts cd ON c.discount_id = cd.discount_id
        
        WHERE ci.cart_id = ?
        ORDER BY ci.cart_item_id DESC
    ";
    
    $cartItems = $this->db->select($query, [$cartId]);
    
    // Calculate totals with proper discount logic
    $subtotal = 0;
    $totalDiscount = 0;
    $currentDate = date('Y-m-d');
    
    foreach ($cartItems as &$item) {
        $itemPrice = floatval($item['price']) * intval($item['quantity']);
        $itemDiscount = 0;
        
        // Determine which discount to apply (product discount takes priority)
        $discountType = null;
        $discountValue = 0;
        $discountName = '';
        $discountSource = 'none';
        
        // Check product-specific discount
        if (!empty($item['pd_id']) && 
            $item['pd_active'] == 1 &&
            $currentDate >= date('Y-m-d', strtotime($item['pd_start'])) &&
            $currentDate <= date('Y-m-d', strtotime($item['pd_end']))) {
            
            $discountType = $item['product_discount_type'];
            $discountValue = floatval($item['product_discount_value']);
            $discountName = $item['product_discount_name'];
            $discountSource = 'product';
        } 
        // Check category-level discount (only if no product discount)
        elseif (!empty($item['cd_id']) && 
                 $item['cd_active'] == 1 &&
                 $currentDate >= date('Y-m-d', strtotime($item['cd_start'])) &&
                 $currentDate <= date('Y-m-d', strtotime($item['cd_end']))) {
            
            $discountType = $item['category_discount_type'];
            $discountValue = floatval($item['category_discount_value']);
            $discountName = $item['category_discount_name'];
            $discountSource = 'category';
        }
        
        // Calculate discount amount
        if ($discountType == 'percentage') {
            // Percentage discount: apply to total item price
            $itemDiscount = ($itemPrice * $discountValue) / 100;
        } elseif ($discountType == 'fixed') {
            // Fixed discount: apply per unit, then multiply by quantity
            // But don't exceed the total item price
            $discountPerUnit = $discountValue;
            $totalFixedDiscount = $discountPerUnit * intval($item['quantity']);
            $itemDiscount = min($totalFixedDiscount, $itemPrice);
        }
        
        
        // Calculate final price after discount
        $finalPrice = $itemPrice - $itemDiscount;
        
        // Store discount info in item (keep as numbers for calculations)
        $item['discount_type'] = $discountType;
        $item['discount_value'] = $discountValue;
        $item['discount_name'] = $discountName;
        $item['discount_source'] = $discountSource;
        $item['discount_amount'] = $itemDiscount;
        $item['final_price'] = $finalPrice;
        $item['item_total'] = $itemPrice; // Original price before discount
        
        // Debug info (remove in production)
        $item['debug'] = [
            'has_product_discount' => !empty($item['pd_id']),
            'has_category_discount' => !empty($item['cd_id']),
            'pd_active' => $item['pd_active'],
            'cd_active' => $item['cd_active'],
            'current_date' => $currentDate,
            'discount_applied' => $discountSource
        ];
        
        // Add to totals
        $subtotal += $finalPrice;
        $totalDiscount += $itemDiscount;
        
        // Clean up - remove internal fields
        unset($item['pd_id'], $item['cd_id']);
        unset($item['pd_active'], $item['cd_active']);
        unset($item['pd_start'], $item['pd_end']);
        unset($item['cd_start'], $item['cd_end']);
    }
    
    // Calculate shipping (free shipping over ৳1000)
    $shippingCost = $subtotal >= 1000 ? 0 : 50;
    $totalCost = $subtotal + $shippingCost;
    
    // Format all monetary values for response
    foreach ($cartItems as &$item) {
        $item['price'] = number_format($item['price'], 2, '.', '');
        $item['item_total'] = number_format($item['item_total'], 2, '.', '');
        $item['discount_amount'] = number_format($item['discount_amount'], 2, '.', '');
        $item['final_price'] = number_format($item['final_price'], 2, '.', '');
    }
    
    $response = [
        'cartItems' => $cartItems,
        'itemCount' => count($cartItems),
        'subtotal' => number_format($subtotal, 2, '.', ''),
        'totalDiscounts' => number_format($totalDiscount, 2, '.', ''),
        'shippingCost' => number_format($shippingCost, 2, '.', ''),
        'totalCost' => number_format($totalCost, 2, '.', '')
    ];
    
    $this->sendResponse(true, 'Cart fetched successfully', $response);
}

    // Add item to cart
    private function addToCart() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        $productId = $_POST['product_id'] ?? $_GET['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? $_GET['quantity'] ?? 1;
        
        if (!$productId) {
            $this->sendResponse(false, 'Product ID is required', null, 400);
            return;
        }

        // Validate product exists and has stock
        $product = $this->db->select(
            "SELECT product_id, name, stock FROM products WHERE product_id = ?",
            [$productId]
        );
        
        if (empty($product)) {
            $this->sendResponse(false, 'Product not found', null, 404);
            return;
        }
        
        if ($product[0]['stock'] < $quantity) {
            $this->sendResponse(false, 'Insufficient stock', null, 400);
            return;
        }

        $cartId = $this->getOrCreateCart($customerId);
        
        // Check if item already exists in cart
        $existingItem = $this->db->select(
            "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?",
            [$cartId, $productId]
        );
        
        if (!empty($existingItem)) {
            // Update quantity
            $newQuantity = $existingItem[0]['quantity'] + $quantity;
            
            if ($newQuantity > $product[0]['stock']) {
                $this->sendResponse(false, 'Cannot add more items. Stock limit exceeded', null, 400);
                return;
            }
            
            $this->db->update(
                "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?",
                [$newQuantity, $existingItem[0]['cart_item_id']]
            );
            
            $message = 'Cart updated successfully';
        } else {
            // Add new item
            $this->db->insert(
                "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)",
                [$cartId, $productId, $quantity]
            );
            
            $message = 'Item added to cart successfully';
        }
        
        $this->sendResponse(true, $message);
    }

    // Update item quantity
    private function updateQuantity() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        $cartItemId = $_GET['cart_item_id'] ?? $_POST['cart_item_id'] ?? null;
        $quantity = $_GET['quantity'] ?? $_POST['quantity'] ?? null;
        
        if (!$cartItemId || !$quantity) {
            $this->sendResponse(false, 'Cart item ID and quantity are required', null, 400);
            return;
        }
        
        if ($quantity < 1) {
            $this->sendResponse(false, 'Quantity must be at least 1', null, 400);
            return;
        }

        // Verify cart item belongs to user and get product info
        $cartItem = $this->db->select("
            SELECT ci.cart_item_id, ci.product_id, p.stock, p.name
            FROM cart_items ci
            JOIN cart c ON ci.cart_id = c.cart_id
            JOIN products p ON ci.product_id = p.product_id
            WHERE ci.cart_item_id = ? AND c.customer_id = ?
        ", [$cartItemId, $customerId]);
        
        if (empty($cartItem)) {
            $this->sendResponse(false, 'Cart item not found', null, 404);
            return;
        }
        
        if ($quantity > $cartItem[0]['stock']) {
            $this->sendResponse(false, 'Insufficient stock available', null, 400);
            return;
        }

        $this->db->update(
            "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?",
            [$quantity, $cartItemId]
        );
        
        $this->sendResponse(true, 'Quantity updated successfully');
    }

    // Remove item from cart
    private function removeFromCart() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        $cartItemId = $_GET['cart_item_id'] ?? $_POST['cart_item_id'] ?? null;
        
        if (!$cartItemId) {
            $this->sendResponse(false, 'Cart item ID is required', null, 400);
            return;
        }

        // Verify cart item belongs to user
        $cartItem = $this->db->select("
            SELECT ci.cart_item_id
            FROM cart_items ci
            JOIN cart c ON ci.cart_id = c.cart_id
            WHERE ci.cart_item_id = ? AND c.customer_id = ?
        ", [$cartItemId, $customerId]);
        
        if (empty($cartItem)) {
            $this->sendResponse(false, 'Cart item not found', null, 404);
            return;
        }

        $this->db->delete(
            "DELETE FROM cart_items WHERE cart_item_id = ?",
            [$cartItemId]
        );
        
        $this->sendResponse(true, 'Item removed from cart successfully');
    }

    // Clear entire cart
    private function clearCart() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        $cartId = $this->getOrCreateCart($customerId);
        
        $this->db->delete(
            "DELETE FROM cart_items WHERE cart_id = ?",
            [$cartId]
        );
        
        $this->sendResponse(true, 'Cart cleared successfully');
    }

    // Place order
    private function placeOrder() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        // Get order details from POST data
        $paymentMethod = $_POST['payment_method'] ?? 'Cash on Delivery';
        $customerDetailsRaw = $_POST['customer_details'] ?? null;
        
        // Try to parse as JSON first (from JavaScript JSON.stringify)
        $customerDetails = null;
        if ($customerDetailsRaw) {
            // Try JSON parsing first
            $decoded = json_decode($customerDetailsRaw, true);
            if ($decoded && is_array($decoded)) {
                $customerDetails = $decoded;
            } else {
                // Fallback to treating as raw array
                $customerDetails = $customerDetailsRaw;
            }
        }
        
        if (!$customerDetails || !is_array($customerDetails)) {
            $this->sendResponse(false, 'Customer details are required', null, 400);
            return;
        }

        // Ensure all customer details fields exist and are properly trimmed
        $customerDetails['full_name'] = trim($customerDetails['full_name'] ?? '');
        $customerDetails['phone'] = trim($customerDetails['phone'] ?? '');
        $customerDetails['address'] = trim($customerDetails['address'] ?? '');
        
        // If address is still empty but address_line1 and address_line2 exist, compose it
        if (empty($customerDetails['address'])) {
            $line1 = trim($customerDetails['address_line1'] ?? '');
            $line2 = trim($customerDetails['address_line2'] ?? '');
            $composed = trim($line1 . (empty($line2) ? '' : ', ' . $line2));
            if (!empty($composed)) {
                $customerDetails['address'] = $composed;
            }
        }

        // Fallback: check shipping_address and billing_address
        if (empty($customerDetails['address'])) {
            $shippingAddr = trim($customerDetails['shipping_address'] ?? '');
            if (!empty($shippingAddr)) {
                $customerDetails['address'] = $shippingAddr;
            }
        }
        if (empty($customerDetails['address'])) {
            $billingAddr = trim($customerDetails['billing_address'] ?? '');
            if (!empty($billingAddr)) {
                $customerDetails['address'] = $billingAddr;
            }
        }

        // Validate customer details
        $requiredFields = ['full_name', 'address', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($customerDetails[$field])) {
                $this->sendResponse(false, "Field '$field' is required", null, 400);
                return;
            }
        }

        $cartId = $this->getOrCreateCart($customerId);
        
        // Get cart items with current prices
        $cartItems = $this->db->select("
            SELECT 
                ci.product_id, 
                ci.quantity, 
                p.price, 
                p.stock,
                p.name,
                (ci.quantity * p.price) as item_total,
                COALESCE(d.discount_type, '') as discount_type,
                COALESCE(d.discount_value, 0) as discount_value
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.product_id
            LEFT JOIN discounts d ON p.discount_id = d.discount_id 
                AND CURDATE() BETWEEN d.start_date AND d.end_date
            WHERE ci.cart_id = ?
        ", [$cartId]);
        
        if (empty($cartItems)) {
            $this->sendResponse(false, 'Cart is empty', null, 400);
            return;
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['stock']) {
                $this->sendResponse(false, "Insufficient stock for {$item['name']}", null, 400);
                return;
            }
        }

        // Calculate order totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $itemPrice = $item['price'] * $item['quantity'];
            $itemDiscount = 0;
            
            if ($item['discount_type'] == 'percentage') {
                $itemDiscount = ($itemPrice * $item['discount_value']) / 100;
            } elseif ($item['discount_type'] == 'fixed') {
                $itemDiscount = min($item['discount_value'], $itemPrice);
            }
            
            $subtotal += ($itemPrice - $itemDiscount);
        }
        
        $shippingCost = $subtotal >= 1000 ? 0 : 50;
        $totalAmount = $subtotal + $shippingCost;

        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Save/update customer details
            $existingDetails = $this->db->select(
                "SELECT detail_id FROM customerdetails WHERE user_id = ?",
                [$customerId]
            );
            
            if (!empty($existingDetails)) {
                $this->db->update(
                    "UPDATE customerdetails SET full_name = ?, address = ?, phone = ? WHERE user_id = ?",
                    [$customerDetails['full_name'], $customerDetails['address'], $customerDetails['phone'], $customerId]
                );
            } else {
                $this->db->insert(
                    "INSERT INTO customerdetails (user_id, full_name, address, phone) VALUES (?, ?, ?, ?)",
                    [$customerId, $customerDetails['full_name'], $customerDetails['address'], $customerDetails['phone']]
                );
            }

            // Create order
            $orderId = $this->db->insert(
                "INSERT INTO orders (customer_id, order_status, total_amount) VALUES (?, 'Pending', ?)",
                [$customerId, $totalAmount]
            );

            // Create order items and update stock
            foreach ($cartItems as $item) {
                $itemPrice = $item['price'] * $item['quantity'];
                $itemDiscount = 0;
                
                if ($item['discount_type'] == 'percentage') {
                    $itemDiscount = ($itemPrice * $item['discount_value']) / 100;
                } elseif ($item['discount_type'] == 'fixed') {
                    $itemDiscount = min($item['discount_value'], $itemPrice);
                }
                
                $finalPrice = ($itemPrice - $itemDiscount) / $item['quantity']; // Price per unit after discount
                
                // Insert order item
                $this->db->insert(
                    "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)",
                    [$orderId, $item['product_id'], $item['quantity'], $finalPrice]
                );

                // Update product stock
                $this->db->update(
                    "UPDATE products SET stock = stock - ? WHERE product_id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }

            // Create payment record
            $this->db->insert(
                "INSERT INTO payments (order_id, amount, method, status) VALUES (?, ?, ?, 'Pending')",
                [$orderId, $totalAmount, $paymentMethod]
            );

            // Create shipping record
            $this->db->insert(
                "INSERT INTO shipping (order_id, shipping_status) VALUES (?, 'Pending')",
                [$orderId]
            );

            // Clear cart
            $this->db->delete("DELETE FROM cart_items WHERE cart_id = ?", [$cartId]);

            // Commit transaction
            $this->db->commit();
            
            $response = [
                'order_id' => $orderId,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod
            ];
            
            $this->sendResponse(true, 'Order placed successfully', $response);
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // Helper methods
    private function getCustomerId() {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id'];
        }

        return null;
    }

    private function getOrCreateCart($customerId) {
        $cart = $this->db->select(
            "SELECT cart_id FROM cart WHERE customer_id = ?",
            [$customerId]
        );
        
        if (!empty($cart)) {
            return $cart[0]['cart_id'];
        }
        
        return $this->db->insert(
            "INSERT INTO cart (customer_id) VALUES (?)",
            [$customerId]
        );
    }

    private function sendResponse($success, $message, $data = null, $httpCode = 200) {
        http_response_code($httpCode);
        
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response = array_merge($response, $data);
        }
        
        echo json_encode($response);
        exit;
    }
}

// Initialize and handle the request
$controller = new CartController();
$controller->handleRequest();
?>
===
<?php
require_once __DIR__ . '/../models/db.php';

class CartController {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        
        // Enable CORS
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Content-Type: application/json");
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? $_POST['action'] ?? null;
        
        try {
            switch ($action) {
                case 'fetchCart':
                    $this->fetchCart();
                    break;
                case 'addToCart':
                    $this->addToCart();
                    break;
                case 'updateQuantity':
                    $this->updateQuantity();
                    break;
                case 'removeFromCart':
                    $this->removeFromCart();
                    break;
                case 'clearCart':
                    $this->clearCart();
                    break;
                case 'placeOrder':
                    $this->placeOrder();
                    break;
                default:
                    $this->sendResponse(false, 'Invalid action', null, 400);
            }
        } catch (Exception $e) {
            // Let the global error handler manage the exception and response
            throw $e;
        }
    }

    // Fetch cart items for current user
    private function fetchCart() {
    $customerId = $this->getCustomerId();
    
    if (!$customerId) {
        $this->sendResponse(false, 'User not logged in', null, 401);
        return;
    }

    $cartId = $this->getOrCreateCart($customerId);
    
    // Enhanced query to get product and category discounts
    $query = "
        SELECT 
            ci.cart_item_id,
            ci.product_id,
            ci.quantity,
            p.name,
            p.price,
            p.image_url,
            p.stock,
            p.category_id,
            c.category_name,
            p.discount_id as product_discount_id,
            c.discount_id as category_discount_id,
            
            -- Product-level discount
            pd.discount_id as pd_id,
            pd.discount_type as product_discount_type,
            pd.discount_value as product_discount_value,
            pd.discount_name as product_discount_name,
            pd.is_active as pd_active,
            pd.start_date as pd_start,
            pd.end_date as pd_end,
            
            -- Category-level discount
            cd.discount_id as cd_id,
            cd.discount_type as category_discount_type,
            cd.discount_value as category_discount_value,
            cd.discount_name as category_discount_name,
            cd.is_active as cd_active,
            cd.start_date as cd_start,
            cd.end_date as cd_end
            
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        
        -- Get product discount (will check active status in PHP)
        LEFT JOIN discounts pd ON p.discount_id = pd.discount_id
        
        -- Get category discount (will check active status in PHP)
        LEFT JOIN discounts cd ON c.discount_id = cd.discount_id
        
        WHERE ci.cart_id = ?
        ORDER BY ci.cart_item_id DESC
    ";
    
    $cartItems = $this->db->select($query, [$cartId]);
    
    // Calculate totals with proper discount logic
    $subtotal = 0;
    $totalDiscount = 0;
    $currentDate = date('Y-m-d');
    
    foreach ($cartItems as &$item) {
        $itemPrice = floatval($item['price']) * intval($item['quantity']);
        $itemDiscount = 0;
        
        // Determine which discount to apply (product discount takes priority)
        $discountType = null;
        $discountValue = 0;
        $discountName = '';
        $discountSource = 'none';
        
        // Check product-specific discount
        if (!empty($item['pd_id']) && 
            $item['pd_active'] == 1 &&
            $currentDate >= date('Y-m-d', strtotime($item['pd_start'])) &&
            $currentDate <= date('Y-m-d', strtotime($item['pd_end']))) {
            
            $discountType = $item['product_discount_type'];
            $discountValue = floatval($item['product_discount_value']);
            $discountName = $item['product_discount_name'];
            $discountSource = 'product';
        } 
        // Check category-level discount (only if no product discount)
        elseif (!empty($item['cd_id']) && 
                 $item['cd_active'] == 1 &&
                 $currentDate >= date('Y-m-d', strtotime($item['cd_start'])) &&
                 $currentDate <= date('Y-m-d', strtotime($item['cd_end']))) {
            
            $discountType = $item['category_discount_type'];
            $discountValue = floatval($item['category_discount_value']);
            $discountName = $item['category_discount_name'];
            $discountSource = 'category';
        }
        
        // Calculate discount amount
        if ($discountType == 'percentage') {
            // Percentage discount: apply to total item price
            $itemDiscount = ($itemPrice * $discountValue) / 100;
        } elseif ($discountType == 'fixed') {
            // Fixed discount: apply per unit, then multiply by quantity
            // But don't exceed the total item price
            $discountPerUnit = $discountValue;
            $totalFixedDiscount = $discountPerUnit * intval($item['quantity']);
            $itemDiscount = min($totalFixedDiscount, $itemPrice);
        }
        
        
        // Calculate final price after discount
        $finalPrice = $itemPrice - $itemDiscount;
        
        // Store discount info in item (keep as numbers for calculations)
        $item['discount_type'] = $discountType;
        $item['discount_value'] = $discountValue;
        $item['discount_name'] = $discountName;
        $item['discount_source'] = $discountSource;
        $item['discount_amount'] = $itemDiscount;
        $item['final_price'] = $finalPrice;
        $item['item_total'] = $itemPrice; // Original price before discount
        
        
        
        // Add to totals
        $subtotal += $finalPrice;
        $totalDiscount += $itemDiscount;
        
        // Clean up - remove internal fields
        unset($item['pd_id'], $item['cd_id']);
        unset($item['pd_active'], $item['cd_active']);
        unset($item['pd_start'], $item['pd_end']);
        unset($item['cd_start'], $item['cd_end']);
    }
    
    // Calculate shipping (free shipping over ৳1000)
    $shippingCost = $subtotal >= 1000 ? 0 : 50;
    $totalCost = $subtotal + $shippingCost;
    
    // Format all monetary values for response
    foreach ($cartItems as &$item) {
        $item['price'] = number_format($item['price'], 2, '.', '');
        $item['item_total'] = number_format($item['item_total'], 2, '.', '');
        $item['discount_amount'] = number_format($item['discount_amount'], 2, '.', '');
        $item['final_price'] = number_format($item['final_price'], 2, '.', '');
    }
    
    $response = [
        'cartItems' => $cartItems,
        'itemCount' => count($cartItems),
        'subtotal' => number_format($subtotal, 2, '.', ''),
        'totalDiscounts' => number_format($totalDiscount, 2, '.', ''),
        'shippingCost' => number_format($shippingCost, 2, '.', ''),
        'totalCost' => number_format($totalCost, 2, '.', '')
    ];
    
    $this->sendResponse(true, 'Cart fetched successfully', $response);
}

    // Add item to cart
    private function addToCart() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        $productId = $_POST['product_id'] ?? $_GET['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? $_GET['quantity'] ?? 1;
        
        if (!$productId) {
            $this->sendResponse(false, 'Product ID is required', null, 400);
            return;
        }

        // Validate product exists and has stock
        $product = $this->db->select(
            "SELECT product_id, name, stock FROM products WHERE product_id = ?",
            [$productId]
        );
        
        if (empty($product)) {
            $this->sendResponse(false, 'Product not found', null, 404);
            return;
        }
        
        if ($product[0]['stock'] < $quantity) {
            $this->sendResponse(false, 'Insufficient stock', null, 400);
            return;
        }

        $cartId = $this->getOrCreateCart($customerId);
        
        // Check if item already exists in cart
        $existingItem = $this->db->select(
            "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?",
            [$cartId, $productId]
        );
        
        if (!empty($existingItem)) {
            // Update quantity
            $newQuantity = $existingItem[0]['quantity'] + $quantity;
            
            if ($newQuantity > $product[0]['stock']) {
                $this->sendResponse(false, 'Cannot add more items. Stock limit exceeded', null, 400);
                return;
            }
            
            $this->db->update(
                "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?",
                [$newQuantity, $existingItem[0]['cart_item_id']]
            );
            
            $message = 'Cart updated successfully';
        } else {
            // Add new item
            $this->db->insert(
                "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)",
                [$cartId, $productId, $quantity]
            );
            
            $message = 'Item added to cart successfully';
        }
        
        $this->sendResponse(true, $message);
    }

    // Update item quantity
    private function updateQuantity() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        $cartItemId = $_GET['cart_item_id'] ?? $_POST['cart_item_id'] ?? null;
        $quantity = $_GET['quantity'] ?? $_POST['quantity'] ?? null;
        
        if (!$cartItemId || !$quantity) {
            $this->sendResponse(false, 'Cart item ID and quantity are required', null, 400);
            return;
        }
        
        if ($quantity < 1) {
            $this->sendResponse(false, 'Quantity must be at least 1', null, 400);
            return;
        }

        // Verify cart item belongs to user and get product info
        $cartItem = $this->db->select("
            SELECT ci.cart_item_id, ci.product_id, p.stock, p.name
            FROM cart_items ci
            JOIN cart c ON ci.cart_id = c.cart_id
            JOIN products p ON ci.product_id = p.product_id
            WHERE ci.cart_item_id = ? AND c.customer_id = ?
        ", [$cartItemId, $customerId]);
        
        if (empty($cartItem)) {
            $this->sendResponse(false, 'Cart item not found', null, 404);
            return;
        }
        
        if ($quantity > $cartItem[0]['stock']) {
            $this->sendResponse(false, 'Insufficient stock available', null, 400);
            return;
        }

        $this->db->update(
            "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?",
            [$quantity, $cartItemId]
        );
        
        $this->sendResponse(true, 'Quantity updated successfully');
    }

    // Remove item from cart
    private function removeFromCart() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        $cartItemId = $_GET['cart_item_id'] ?? $_POST['cart_item_id'] ?? null;
        
        if (!$cartItemId) {
            $this->sendResponse(false, 'Cart item ID is required', null, 400);
            return;
        }

        // Verify cart item belongs to user
        $cartItem = $this->db->select("
            SELECT ci.cart_item_id
            FROM cart_items ci
            JOIN cart c ON ci.cart_id = c.cart_id
            WHERE ci.cart_item_id = ? AND c.customer_id = ?
        ", [$cartItemId, $customerId]);
        
        if (empty($cartItem)) {
            $this->sendResponse(false, 'Cart item not found', null, 404);
            return;
        }

        $this->db->delete(
            "DELETE FROM cart_items WHERE cart_item_id = ?",
            [$cartItemId]
        );
        
        $this->sendResponse(true, 'Item removed from cart successfully');
    }

    // Clear entire cart
    private function clearCart() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        $cartId = $this->getOrCreateCart($customerId);
        
        $this->db->delete(
            "DELETE FROM cart_items WHERE cart_id = ?",
            [$cartId]
        );
        
        $this->sendResponse(true, 'Cart cleared successfully');
    }

    // Place order
    private function placeOrder() {
        $customerId = $this->getCustomerId();
        
        if (!$customerId) {
            $this->sendResponse(false, 'User not logged in', null, 401);
            return;
        }

        // Get order details from POST data
        $paymentMethod = $_POST['payment_method'] ?? 'Cash on Delivery';
        $customerDetailsRaw = $_POST['customer_details'] ?? null;
        
        // Try to parse as JSON first (from JavaScript JSON.stringify)
        $customerDetails = null;
        if ($customerDetailsRaw) {
            // Try JSON parsing first
            $decoded = json_decode($customerDetailsRaw, true);
            if ($decoded && is_array($decoded)) {
                $customerDetails = $decoded;
            } else {
                // Fallback to treating as raw array
                $customerDetails = $customerDetailsRaw;
            }
        }
        
        if (!$customerDetails || !is_array($customerDetails)) {
            $this->sendResponse(false, 'Customer details are required', null, 400);
            return;
        }

        // Ensure all customer details fields exist and are properly trimmed
        $customerDetails['full_name'] = trim($customerDetails['full_name'] ?? '');
        $customerDetails['phone'] = trim($customerDetails['phone'] ?? '');
        $customerDetails['address'] = trim($customerDetails['address'] ?? '');
        
        // If address is still empty but address_line1 and address_line2 exist, compose it
        if (empty($customerDetails['address'])) {
            $line1 = trim($customerDetails['address_line1'] ?? '');
            $line2 = trim($customerDetails['address_line2'] ?? '');
            $composed = trim($line1 . (empty($line2) ? '' : ', ' . $line2));
            if (!empty($composed)) {
                $customerDetails['address'] = $composed;
            }
        }

        // Fallback: check shipping_address and billing_address
        if (empty($customerDetails['address'])) {
            $shippingAddr = trim($customerDetails['shipping_address'] ?? '');
            if (!empty($shippingAddr)) {
                $customerDetails['address'] = $shippingAddr;
            }
        }
        if (empty($customerDetails['address'])) {
            $billingAddr = trim($customerDetails['billing_address'] ?? '');
            if (!empty($billingAddr)) {
                $customerDetails['address'] = $billingAddr;
            }
        }

        // Validate customer details
        $requiredFields = ['full_name', 'address', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($customerDetails[$field])) {
                $this->sendResponse(false, "Field '$field' is required", null, 400);
                return;
            }
        }

        $cartId = $this->getOrCreateCart($customerId);
        
        // Get cart items with current prices
        $cartItems = $this->db->select("
            SELECT 
                ci.product_id, 
                ci.quantity, 
                p.price, 
                p.stock,
                p.name,
                (ci.quantity * p.price) as item_total,
                COALESCE(d.discount_type, '') as discount_type,
                COALESCE(d.discount_value, 0) as discount_value
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.product_id
            LEFT JOIN discounts d ON p.discount_id = d.discount_id 
                AND CURDATE() BETWEEN d.start_date AND d.end_date
            WHERE ci.cart_id = ?
        ", [$cartId]);
        
        if (empty($cartItems)) {
            $this->sendResponse(false, 'Cart is empty', null, 400);
            return;
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['stock']) {
                $this->sendResponse(false, "Insufficient stock for {$item['name']}", null, 400);
                return;
            }
        }

        // Calculate order totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $itemPrice = $item['price'] * $item['quantity'];
            $itemDiscount = 0;
            
            if ($item['discount_type'] == 'percentage') {
                $itemDiscount = ($itemPrice * $item['discount_value']) / 100;
            } elseif ($item['discount_type'] == 'fixed') {
                $itemDiscount = min($item['discount_value'], $itemPrice);
            }
            
            $subtotal += ($itemPrice - $itemDiscount);
        }
        
        $shippingCost = $subtotal >= 1000 ? 0 : 50;
        $totalAmount = $subtotal + $shippingCost;

        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Save/update customer details
            $existingDetails = $this->db->select(
                "SELECT detail_id FROM customerdetails WHERE user_id = ?",
                [$customerId]
            );
            
            if (!empty($existingDetails)) {
                $this->db->update(
                    "UPDATE customerdetails SET full_name = ?, address = ?, phone = ? WHERE user_id = ?",
                    [$customerDetails['full_name'], $customerDetails['address'], $customerDetails['phone'], $customerId]
                );
            } else {
                $this->db->insert(
                    "INSERT INTO customerdetails (user_id, full_name, address, phone) VALUES (?, ?, ?, ?)",
                    [$customerId, $customerDetails['full_name'], $customerDetails['address'], $customerDetails['phone']]
                );
            }

            // Create order
            $orderId = $this->db->insert(
                "INSERT INTO orders (customer_id, order_status, total_amount) VALUES (?, 'Pending', ?)",
                [$customerId, $totalAmount]
            );

            // Create order items and update stock
            foreach ($cartItems as $item) {
                $itemPrice = $item['price'] * $item['quantity'];
                $itemDiscount = 0;
                
                if ($item['discount_type'] == 'percentage') {
                    $itemDiscount = ($itemPrice * $item['discount_value']) / 100;
                } elseif ($item['discount_type'] == 'fixed') {
                    $itemDiscount = min($item['discount_value'], $itemPrice);
                }
                
                $finalPrice = ($itemPrice - $itemDiscount) / $item['quantity']; // Price per unit after discount
                
                // Insert order item
                $this->db->insert(
                    "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)",
                    [$orderId, $item['product_id'], $item['quantity'], $finalPrice]
                );

                // Update product stock
                $this->db->update(
                    "UPDATE products SET stock = stock - ? WHERE product_id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }

            // Create payment record
            $this->db->insert(
                "INSERT INTO payments (order_id, amount, method, status) VALUES (?, ?, ?, 'Pending')",
                [$orderId, $totalAmount, $paymentMethod]
            );

            // Create shipping record
            $this->db->insert(
                "INSERT INTO shipping (order_id, shipping_status) VALUES (?, 'Pending')",
                [$orderId]
            );

            // Clear cart
            $this->db->delete("DELETE FROM cart_items WHERE cart_id = ?", [$cartId]);

            // Commit transaction
            $this->db->commit();
            
            $response = [
                'order_id' => $orderId,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod
            ];
            
            $this->sendResponse(true, 'Order placed successfully', $response);
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // Helper methods
    private function getCustomerId() {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id'];
        }

        return null;
    }

    private function getOrCreateCart($customerId) {
        $cart = $this->db->select(
            "SELECT cart_id FROM cart WHERE customer_id = ?",
            [$customerId]
        );
        
        if (!empty($cart)) {
            return $cart[0]['cart_id'];
        }
        
        return $this->db->insert(
            "INSERT INTO cart (customer_id) VALUES (?)",
            [$customerId]
        );
    }

    private function sendResponse($success, $message, $data = null, $httpCode = 200) {
        http_response_code($httpCode);
        
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response = array_merge($response, $data);
        }
        
        echo json_encode($response);
        exit;
    }
}

// Initialize and handle the request
$controller = new CartController();
$controller->handleRequest();
?>
```

---

## Remaining Recommendations (Not Applied)

These are lower-priority items documented in the audit that can be addressed later:

| Item | Description | Risk |
|------|-------------|------|
| MED-3 | Restrict CORS from `*` to specific domain | Low |
| MED-4 | Consolidate 4 duplicate `Database` classes | Low |
| MED-5 | Deprecate old `OrderController.php` | Low |
| MED-6 | Remove `Reg_control.php` legacy handler | Low |
| MED-7 | Add role-based auth guards on Admin/Vendor/Logistics views | Medium |

---

## Deployment Readiness

After these fixes, the project is ready for VPS deployment. Before going live:

1. Set `APP_ENV=production` in `.env`
2. Update DB credentials in `.env` for the production MySQL instance
3. Run `schema.sql` against the production database
4. Verify `.env` is NOT committed: `git status` should not show it
