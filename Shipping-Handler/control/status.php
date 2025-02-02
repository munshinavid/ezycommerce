<?php
session_start();
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['delivery_man_id']) || !$_SESSION['loggedin']) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Get input data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!$data || !isset($data['order_id'], $data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Process update
try {
    require_once '../db/model.php';
    $orderModel = new Order();
    
    $result = $orderModel->updateOrderStatus(
        $data['order_id'],
        $data['status'],
        $_SESSION['delivery_man_id'] // Add delivery man ID
    );

    echo json_encode(['success' => $result]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}