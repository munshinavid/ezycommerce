<?php
// Disable error display and use error logging instead
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set headers first before any output
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Catch any errors and return JSON
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Server error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    exit();
});

try {
    require_once __DIR__ . '/../models/Database.php';
} catch (Exception $e) {
    http_response_code(500);
    
    echo json_encode([
        'message' => 'Database configuration error. Please check your config/Database.php file.',
        'error' => $e->getMessage()
    ]);
    exit();
}

class UserController {
    private $db;
    private $method;
    private $endpoint;
    private $params;

    public function __construct() {
        try {
            $this->db = new Database();
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'message' => 'Database connection failed',
                'error' => $e->getMessage()
            ]);
        }
        
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->parseRequest();
    }

    private function parseRequest() {
        // Get the request URI and remove query string
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        
        // Remove script name from URI to get the path
        $path = str_replace(dirname($scriptName), '', parse_url($requestUri, PHP_URL_PATH));
        $path = str_replace(basename($scriptName), '', $path);
        $path = trim($path, '/');
        
        // Split path into parts
        $parts = array_filter(explode('/', $path));
        $parts = array_values($parts); // Re-index array
        
        // First part is endpoint, rest are params
        $this->endpoint = !empty($parts) ? $parts[0] : 'users';
        $this->params = array_slice($parts, 1);
    }

    public function handleRequest() {
        try {
            // Route based on endpoint and method
            if ($this->endpoint === 'users' || $this->endpoint === '') {
                switch ($this->method) {
                    case 'GET':
                        if (!empty($this->params[0])) {
                            $this->getUserById($this->params[0]);
                        } else {
                            $this->getUsers();
                        }
                        break;
                    case 'POST':
                        $this->createUser();
                        break;
                    case 'PUT':
                        if (!empty($this->params[0])) {
                            $this->updateUser($this->params[0]);
                        } else {
                            $this->sendResponse(400, ['message' => 'User ID is required']);
                        }
                        break;
                    case 'DELETE':
                        if (!empty($this->params[0])) {
                            $this->deleteUser($this->params[0]);
                        } else {
                            $this->sendResponse(400, ['message' => 'User ID is required']);
                        }
                        break;
                    default:
                        $this->sendResponse(405, ['message' => 'Method not allowed']);
                }
            } else {
                $this->sendResponse(404, ['message' => 'Endpoint not found']);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'message' => 'Server error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    // GET /users - Get all users with pagination and filters
    private function getUsers() {
        try {
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $roleFilter = isset($_GET['role']) ? trim($_GET['role']) : 'all';
            
            $offset = ($page - 1) * $limit;
            
            // Build the WHERE clause
            $whereConditions = [];
            $params = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.username LIKE ? OR u.email LIKE ?)";
                $searchParam = "%{$search}%";
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            if ($roleFilter !== 'all' && !empty($roleFilter)) {
                $whereConditions[] = "r.role_name = ?";
                $params[] = $roleFilter;
            }
            
            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total 
                          FROM users u 
                          INNER JOIN roles r ON u.role_id = r.role_id 
                          {$whereClause}";
            $countResult = $this->db->select($countQuery, $params);
            $total = isset($countResult[0]['total']) ? (int)$countResult[0]['total'] : 0;
            
            // Get paginated users
            $query = "SELECT u.user_id as id, u.username, u.email, r.role_name as role, u.created_at 
                     FROM users u 
                     INNER JOIN roles r ON u.role_id = r.role_id 
                     {$whereClause} 
                     ORDER BY u.user_id DESC 
                     LIMIT ? OFFSET ?";
            
            $queryParams = array_merge($params, [$limit, $offset]);
            $users = $this->db->select($query, $queryParams);
            
            $response = [
                'users' => $users,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total > 0 ? (int)ceil($total / $limit) : 1,
                    'total' => $total,
                    'per_page' => $limit
                ]
            ];
            
            $this->sendResponse(200, $response);
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'message' => 'Error fetching users: ' . $e->getMessage()
            ]);
        }
    }

    // GET /users/:id - Get a single user by ID
    private function getUserById($userId) {
        try {
            $query = "SELECT u.user_id as id, u.username, u.email, r.role_name as role, u.created_at 
                     FROM users u 
                     INNER JOIN roles r ON u.role_id = r.role_id 
                     WHERE u.user_id = ?";
            
            $result = $this->db->select($query, [(int)$userId]);
            
            if (empty($result)) {
                $this->sendResponse(404, ['message' => 'User not found']);
                return;
            }
            
            $this->sendResponse(200, $result[0]);
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'message' => 'Error fetching user: ' . $e->getMessage()
            ]);
        }
    }

    // POST /users - Create a new user
    private function createUser() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendResponse(400, ['message' => 'Invalid JSON data']);
                return;
            }
            
            // Validate required fields
            if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
                $this->sendResponse(400, ['message' => 'Username, email, password, and role are required']);
                return;
            }
            
            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->sendResponse(400, ['message' => 'Invalid email format']);
                return;
            }
            
            // Check if username or email already exists
            $checkQuery = "SELECT user_id FROM users WHERE username = ? OR email = ?";
            $existing = $this->db->select($checkQuery, [$data['username'], $data['email']]);
            
            if (!empty($existing)) {
                $this->sendResponse(409, ['message' => 'Username or email already exists']);
                return;
            }
            
            // Get role_id from role_name
            $roleQuery = "SELECT role_id FROM roles WHERE role_name = ?";
            $roleResult = $this->db->select($roleQuery, [$data['role']]);
            
            if (empty($roleResult)) {
                $this->sendResponse(400, ['message' => 'Invalid role specified']);
                return;
            }
            
            $roleId = $roleResult[0]['role_id'];
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            
            // Insert user
            $insertQuery = "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)";
            
            $userId = $this->db->insert($insertQuery, [
                $data['username'],
                $data['email'],
                $hashedPassword,
                $roleId
            ]);
            
            $this->sendResponse(201, [
                'message' => 'User created successfully',
                'user_id' => $userId
            ]);
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'message' => 'Failed to create user: ' . $e->getMessage()
            ]);
        }
    }

    // PUT /users/:id - Update a user
    private function updateUser($userId) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendResponse(400, ['message' => 'Invalid JSON data']);
                return;
            }
            
            // Check if user exists
            $checkQuery = "SELECT user_id FROM users WHERE user_id = ?";
            $existing = $this->db->select($checkQuery, [(int)$userId]);
            
            if (empty($existing)) {
                $this->sendResponse(404, ['message' => 'User not found']);
                return;
            }
            
            // Build update query dynamically based on provided fields
            $updateFields = [];
            $params = [];
            
            if (isset($data['username']) && !empty($data['username'])) {
                $updateFields[] = "username = ?";
                $params[] = $data['username'];
            }
            
            if (isset($data['email']) && !empty($data['email'])) {
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->sendResponse(400, ['message' => 'Invalid email format']);
                    return;
                }
                $updateFields[] = "email = ?";
                $params[] = $data['email'];
            }
            
            if (isset($data['password']) && !empty($data['password'])) {
                $updateFields[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            
            if (isset($data['role']) && !empty($data['role'])) {
                // Get role_id from role_name
                $roleQuery = "SELECT role_id FROM roles WHERE role_name = ?";
                $roleResult = $this->db->select($roleQuery, [$data['role']]);
                
                if (empty($roleResult)) {
                    $this->sendResponse(400, ['message' => 'Invalid role specified']);
                    return;
                }
                
                $updateFields[] = "role_id = ?";
                $params[] = $roleResult[0]['role_id'];
            }
            
            if (empty($updateFields)) {
                $this->sendResponse(400, ['message' => 'No fields to update']);
                return;
            }
            
            // Add userId to params for WHERE clause
            $params[] = (int)$userId;
            
            $updateQuery = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE user_id = ?";
            
            $this->db->update($updateQuery, $params);
            $this->sendResponse(200, ['message' => 'User updated successfully']);
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'message' => 'Failed to update user: ' . $e->getMessage()
            ]);
        }
    }

    // DELETE /users/:id - Delete a user
    private function deleteUser($userId) {
        try {
            // Check if user exists
            $checkQuery = "SELECT user_id FROM users WHERE user_id = ?";
            $existing = $this->db->select($checkQuery, [(int)$userId]);
            
            if (empty($existing)) {
                $this->sendResponse(404, ['message' => 'User not found']);
                return;
            }
            
            // Delete user (related records will cascade delete)
            $deleteQuery = "DELETE FROM users WHERE user_id = ?";
            
            $this->db->delete($deleteQuery, [(int)$userId]);
            $this->sendResponse(200, ['message' => 'User deleted successfully']);
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ]);
        }
    }

    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
}

// Initialize and handle the request
try {
    $controller = new UserController();
    $controller->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Fatal error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>