<?php
require_once '../models/ProductModel.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/ProductModel.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function fetchProducts() {
        header('Content-Type: application/json');
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            // Fetch products from the database
            $products = $this->productModel->getPaginatedProducts($limit, $offset);

            // Optional: Add dummy data or additional fields for testing
            foreach ($products as &$product) {
                //$product['stock'] = rand(1, 20); // Example stock value
                $product['sold'] = rand(1, 50); // Example sold value
                $product['rating'] = number_format(mt_rand(300, 500) / 100, 2); // Example rating
            }

            echo json_encode([
                'status' => 'success',
                'data' => $products
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}

// Check for 'action' and call the corresponding method
if (isset($_GET['action'])) {
    $controller = new ProductController();

    if ($_GET['action'] === 'fetchProducts') {
        $controller->fetchProducts();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No action specified'
    ]);
}

