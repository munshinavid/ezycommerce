<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once(__DIR__ . '/../models/Database.php');

class ProductAPI {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all products with joins
    public function listProducts() {
        try {
            $query = "SELECT p.*, 
                             c.category_name, 
                             v.vendor_name,
                             d.discount_name,
                             d.discount_type,
                             d.discount_value
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.category_id
                      LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                      LEFT JOIN discounts d ON p.discount_id = d.discount_id
                      ORDER BY p.product_id DESC";
            
            return $this->db->select($query);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error loading products: ' . $e->getMessage()];
        }
    }
    
    // Get single product by ID
    public function getProduct($id) {
        try {
            $query = "SELECT p.*, 
                             c.category_name, 
                             v.vendor_name,
                             d.discount_name
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.category_id
                      LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                      LEFT JOIN discounts d ON p.discount_id = d.discount_id
                      WHERE p.product_id = ?";
            
            $result = $this->db->select($query, [$id]);
            
            if (!empty($result)) {
                return ['success' => true, 'data' => $result[0]];
            }
            
            return ['success' => false, 'message' => 'Product not found'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error fetching product: ' . $e->getMessage()];
        }
    }
    
    // Add new product
    public function addProduct($data) {
        try {
            // Validate required fields
            if (empty($data['name']) || empty($data['price']) || !isset($data['stock'])) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }
            
            // Handle image upload
            $imageUrl = $this->handleImageUpload();
            
            $query = "INSERT INTO products (name, description, price, stock, image_url, category_id, discount_id, vendor_id) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['name'],
                $data['description'] ?? null,
                $data['price'],
                $data['stock'],
                $imageUrl,
                !empty($data['category_id']) ? $data['category_id'] : null,
                !empty($data['discount_id']) ? $data['discount_id'] : null,
                !empty($data['vendor_id']) ? $data['vendor_id'] : null
            ];
            
            $productId = $this->db->insert($query, $params);
            
            if ($productId) {
                return ['success' => true, 'message' => 'Product added successfully', 'id' => $productId];
            }
            
            return ['success' => false, 'message' => 'Failed to add product'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error adding product: ' . $e->getMessage()];
        }
    }
    
    // Update product
    public function updateProduct($id, $data) {
        try {
            // Validate required fields
            if (empty($data['name']) || empty($data['price']) || !isset($data['stock'])) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }
            
            // Handle image upload (if new image provided)
            $imageUrl = $this->handleImageUpload();
            
            if ($imageUrl) {
                // Update with new image
                $query = "UPDATE products 
                          SET name = ?, description = ?, price = ?, stock = ?, image_url = ?,
                              category_id = ?, discount_id = ?, vendor_id = ?
                          WHERE product_id = ?";
                
                $params = [
                    $data['name'],
                    $data['description'] ?? null,
                    $data['price'],
                    $data['stock'],
                    $imageUrl,
                    !empty($data['category_id']) ? $data['category_id'] : null,
                    !empty($data['discount_id']) ? $data['discount_id'] : null,
                    !empty($data['vendor_id']) ? $data['vendor_id'] : null,
                    $id
                ];
            } else {
                // Update without changing image
                $query = "UPDATE products 
                          SET name = ?, description = ?, price = ?, stock = ?,
                              category_id = ?, discount_id = ?, vendor_id = ?
                          WHERE product_id = ?";
                
                $params = [
                    $data['name'],
                    $data['description'] ?? null,
                    $data['price'],
                    $data['stock'],
                    !empty($data['category_id']) ? $data['category_id'] : null,
                    !empty($data['discount_id']) ? $data['discount_id'] : null,
                    !empty($data['vendor_id']) ? $data['vendor_id'] : null,
                    $id
                ];
            }
            
            $success = $this->db->update($query, $params);
            
            if ($success) {
                return ['success' => true, 'message' => 'Product updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to update product'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error updating product: ' . $e->getMessage()];
        }
    }
    
    // Delete product
    public function deleteProduct($id) {
        try {
            // Check if product exists and has orders
            $checkQuery = "SELECT COUNT(*) as order_count FROM order_items WHERE product_id = ?";
            $result = $this->db->select($checkQuery, [$id]);
            
            if (!empty($result) && $result[0]['order_count'] > 0) {
                return ['success' => false, 'message' => 'Cannot delete product with existing orders'];
            }
            
            // Delete the product
            $query = "DELETE FROM products WHERE product_id = ?";
            $success = $this->db->delete($query, [$id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Product deleted successfully'];
            }
            
            return ['success' => false, 'message' => 'Product not found or already deleted'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error deleting product: ' . $e->getMessage()];
        }
    }
    
    // Get all categories
    public function getCategories() {
        try {
            $query = "SELECT category_id, category_name FROM categories ORDER BY category_name";
            return $this->db->select($query);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error loading categories: ' . $e->getMessage()];
        }
    }
    
    // Get all vendors
    public function getVendors() {
        try {
            $query = "SELECT vendor_id, vendor_name FROM vendors ORDER BY vendor_name";
            return $this->db->select($query);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error loading vendors: ' . $e->getMessage()];
        }
    }
    
    // Get active discounts
    public function getDiscounts() {
        try {
            $query = "SELECT discount_id, discount_name, discount_type, discount_value 
                      FROM discounts 
                      WHERE is_active = 1 
                      AND NOW() BETWEEN start_date AND end_date
                      ORDER BY discount_name";
            
            return $this->db->select($query);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error loading discounts: ' . $e->getMessage()];
        }
    }
    
    // Handle image upload
    private function handleImageUpload() {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/images/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                return null;
            }
            
            // Validate file size (max 5MB)
            if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                return null;
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                return '../../uploads/images/' . $fileName;
            }
        }
        
        return null;
    }
}

// Initialize API
$api = new ProductAPI();
$action = $_GET['action'] ?? '';

// Route requests
try {
    switch ($action) {
        case 'list':
            echo json_encode($api->listProducts());
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            echo json_encode($api->getProduct($id));
            break;
            
        case 'add':
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'stock' => $_POST['stock'] ?? 0,
                'category_id' => $_POST['category_id'] ?? null,
                'discount_id' => $_POST['discount_id'] ?? null,
                'vendor_id' => $_POST['vendor_id'] ?? null
            ];
            echo json_encode($api->addProduct($data));
            break;
            
        case 'edit':
            $id = $_POST['id'] ?? 0;
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'stock' => $_POST['stock'] ?? 0,
                'category_id' => $_POST['category_id'] ?? null,
                'discount_id' => $_POST['discount_id'] ?? null,
                'vendor_id' => $_POST['vendor_id'] ?? null
            ];
            echo json_encode($api->updateProduct($id, $data));
            break;
            
        case 'delete':
            $id = $_GET['id'] ?? 0;
            echo json_encode($api->deleteProduct($id));
            break;
            
        case 'categories':
            echo json_encode($api->getCategories());
            break;
            
        case 'vendors':
            echo json_encode($api->getVendors());
            break;
            
        case 'discounts':
            echo json_encode($api->getDiscounts());
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>