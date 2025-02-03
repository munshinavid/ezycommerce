<?php
// Include the Database class
require_once 'Database.php';

session_start();

// Create an instance of the Database class
$db = new Database();

if ($_GET['action'] == 'placeOrder') {
    placeOrder($db);
} elseif ($_GET['action'] == 'clearCart') {
    clearCart($db);
}

// Place Order Function (No order details insertion)
function placeOrder($db) {
    $userId = $_SESSION['user_id'];
    $totalAmount = isset($_GET['total_amount']) ? floatval($_GET['total_amount']) : 0;

    if ($totalAmount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid total amount']);
        return;
    }

    // Insert order into orders table
    $query = "INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, ?)";
    $orderInserted = $db->execute($query, [$userId, $totalAmount, 'Pending']);

    if ($orderInserted) {
        // Clear the cart
        clearCart($db);

        echo json_encode(['success' => true, 'message' => 'Order placed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to place the order']);
    }
}

// Clear Cart Function
function clearCart($db) {
    $userId = $_SESSION['user_id'];
    $query = "DELETE FROM cart WHERE user_id = ?";
    $db->execute($query, [$userId]);

    echo json_encode(['success' => true, 'message' => 'Cart cleared']);
}
?>
