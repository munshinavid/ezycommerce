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
