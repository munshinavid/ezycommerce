<?php
header('Content-Type: application/json');
require_once '../controllers/ProductManagementController.php';

$pmc = new ProductManagementController();
$action = $_GET['action'] ?? '';

$response = ['success' => false, 'message' => 'Invalid action'];

try {
    // --- List all products ---
    if ($action === 'list') {
        $products = $pmc->getAllProducts(); // should return array of products with category_name, vendor_name, discount_id
        echo json_encode($products);
        exit;
    }

    // --- Get single product ---
    elseif ($action === 'get' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $product = $pmc->getProductById($id);
        echo json_encode($product
            ? ['success'=>true,'data'=>$product]
            : ['success'=>false,'message'=>'Product not found']
        );
        exit;
    }

    // --- Add product ---
    elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = $_POST; // sanitize inside controller
        $added = $pmc->addProduct($data);
        echo json_encode($added
            ? ['success'=>true]
            : ['success'=>false,'message'=>'Failed to add product']
        );
        exit;
    }

    // --- Edit product ---
    elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['product_id'] ?? 0);
        $data = $_POST; // sanitize in controller
        $updated = $pmc->updateProduct($id, $data, $_FILES['image'] ?? null);
        echo json_encode($updated ? ['success'=>true] : ['success'=>false, 'message'=>'Failed to update product']);
        exit;
    }

    // --- Delete product ---
    elseif ($action === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $deleted = $pmc->deleteProduct($id);
        echo json_encode($deleted ? ['success'=>true] : ['success'=>false, 'message'=>'Failed to delete product']);
        exit;
    }

    // --- Get categories ---
    elseif ($action === 'categories') {
        $categories = $pmc->getAllCategories(); // return array of ['category_id', 'category_name']
        echo json_encode($categories);
        exit;
    }

    // --- Get vendors ---
    elseif ($action === 'vendors') {
        $vendors = $pmc->getAllVendors(); // return array of ['vendor_id', 'vendor_name']
        echo json_encode($vendors);
        exit;
    }

    // --- Get discounts ---
    elseif ($action === 'discounts') {
        $discounts = $pmc->getAllDiscounts(); // return array of ['discount_id', 'discount_name']
        echo json_encode($discounts);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
