<?php


require_once '../models/CartModel.php';
include '../controllers/UserController.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in first.']);
    exit;
}

$userId = $_SESSION['user_id'];
$cartModel = new CartModel();
$userController = new UserController();

// Action: Fetch Cart Items
if (isset($_GET['action']) && $_GET['action'] == 'fetchCart') {
    $cartItems = $cartModel->getCartItems($userId);

    // Calculate totals
    $subtotal = 0;
    $shippingCost = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
        $shippingCost += 2.50; // Example flat rate shipping per item
    }
    $totalCost = $subtotal + $shippingCost;

    echo json_encode([
        'success' => true,
        'cartItems' => $cartItems,
        'subtotal' => number_format($subtotal, 2),
        'shippingCost' => number_format($shippingCost, 2),
        'totalCost' => number_format($totalCost, 2),
    ]);
    exit;
}

// Action: Add to Cart

if ($_GET['action'] === 'addToCart') {
    if (!isset($_SESSION['user_id'])) {
        // Send a JSON response indicating the user is not logged in
        echo json_encode(['success' => false, 'message' => 'Please log in first.']);
        exit;
    }
    
    $userId = intval($_SESSION['user_id']);
    $productId = $_GET['product_id'];
    
    if ($productId <= 0) {
        // Send a JSON response indicating invalid product ID
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        exit;
    }

    try {
        //$cartModel = new CartModel(); // Assume CartModel is responsible for cart operations
        
        if ($cartModel->addToCart($userId, $productId)) {
            // Send a success response in JSON format
            $userController->getCartData($userId);
            echo json_encode(['success' => true, 'message' => 'Product added to cart successfully.']);
            
        } else {
            // Send a failure response in JSON format
            echo json_encode(['success' => false, 'message' => 'Failed to update the cart.']);
        }
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Error in addToCart: " . $e->getMessage());
        
        // Send a JSON response indicating an error occurred
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
    }
    exit;
}



// Action: Remove from Cart
if (isset($_POST['action']) && $_POST['action'] == 'removeFromCart') {
    $cartItemId = $_POST['cart_item_id'];

    // Debugging: Log request
    error_log("Removing cart item ID: " . $cartItemId);

    // Remove product from cart
    $result = $cartModel->removeFromCart($cartItemId);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Product removed from cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove product from cart']);
    }
    exit;
}

// Action: Update Cart Quantity
if (isset($_POST['action']) && $_POST['action'] == 'updateQuantity') {
    $cartItemId = $_POST['cart_item_id'];
    $quantity = $_POST['quantity'];

    // Debugging: Log request
    error_log("Updating quantity: Item ID: " . $cartItemId . " New Qty: " . $quantity);

    // Update quantity
    $result = $cartModel->updateQuantity($cartItemId, $quantity);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cart updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
    }
    exit;
}
?>
