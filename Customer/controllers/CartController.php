<?php
require_once '../models/db.php';

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
            $this->sendResponse(false, $e->getMessage(), null, 500);
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
            "SELECT product_id, name, stock FROM Products WHERE product_id = ?",
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
            "SELECT cart_item_id, quantity FROM Cart_Items WHERE cart_id = ? AND product_id = ?",
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
                "UPDATE Cart_Items SET quantity = ? WHERE cart_item_id = ?",
                [$newQuantity, $existingItem[0]['cart_item_id']]
            );
            
            $message = 'Cart updated successfully';
        } else {
            // Add new item
            $this->db->insert(
                "INSERT INTO Cart_Items (cart_id, product_id, quantity) VALUES (?, ?, ?)",
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
            FROM Cart_Items ci
            JOIN Cart c ON ci.cart_id = c.cart_id
            JOIN Products p ON ci.product_id = p.product_id
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
            "UPDATE Cart_Items SET quantity = ? WHERE cart_item_id = ?",
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
            FROM Cart_Items ci
            JOIN Cart c ON ci.cart_id = c.cart_id
            WHERE ci.cart_item_id = ? AND c.customer_id = ?
        ", [$cartItemId, $customerId]);
        
        if (empty($cartItem)) {
            $this->sendResponse(false, 'Cart item not found', null, 404);
            return;
        }

        $this->db->delete(
            "DELETE FROM Cart_Items WHERE cart_item_id = ?",
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
            "DELETE FROM Cart_Items WHERE cart_id = ?",
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
        $customerDetails = $_POST['customer_details'] ?? null;
        
        if (!$customerDetails) {
            $this->sendResponse(false, 'Customer details are required', null, 400);
            return;
        }

        // Compose unified address if sent as line1/line2
        if (!isset($customerDetails['address'])) {
            $line1 = $customerDetails['address_line1'] ?? '';
            $line2 = $customerDetails['address_line2'] ?? '';
            $composed = trim($line1 . (empty($line2) ? '' : ', ' . $line2));
            if (!empty($composed)) {
                $customerDetails['address'] = $composed;
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
            FROM Cart_Items ci
            JOIN Products p ON ci.product_id = p.product_id
            LEFT JOIN Discounts d ON p.discount_id = d.discount_id 
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
                "SELECT detail_id FROM CustomerDetails WHERE user_id = ?",
                [$customerId]
            );
            
            if (!empty($existingDetails)) {
                $this->db->update(
                    "UPDATE CustomerDetails SET full_name = ?, address = ?, phone = ? WHERE user_id = ?",
                    [$customerDetails['full_name'], $customerDetails['address'], $customerDetails['phone'], $customerId]
                );
            } else {
                $this->db->insert(
                    "INSERT INTO CustomerDetails (user_id, full_name, address, phone) VALUES (?, ?, ?, ?)",
                    [$customerId, $customerDetails['full_name'], $customerDetails['address'], $customerDetails['phone']]
                );
            }

            // Create order
            $orderId = $this->db->insert(
                "INSERT INTO Orders (customer_id, order_status, total_amount) VALUES (?, 'Pending', ?)",
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
                    "INSERT INTO Order_Items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)",
                    [$orderId, $item['product_id'], $item['quantity'], $finalPrice]
                );

                // Update product stock
                $this->db->update(
                    "UPDATE Products SET stock = stock - ? WHERE product_id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }

            // Create payment record
            $this->db->insert(
                "INSERT INTO Payments (order_id, amount, method, status) VALUES (?, ?, ?, 'Pending')",
                [$orderId, $totalAmount, $paymentMethod]
            );

            // Create shipping record
            $this->db->insert(
                "INSERT INTO Shipping (order_id, shipping_status) VALUES (?, 'Pending')",
                [$orderId]
            );

            // Clear cart
            $this->db->delete("DELETE FROM Cart_Items WHERE cart_id = ?", [$cartId]);

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
        // Try session first
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }

        //get user_id from json body
        // JSON body handling
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['user_id'])) {
            return $input['user_id'];
        }

        // after JSON and before return null
        if (isset($_POST['user_id'])) {
            return $_POST['user_id'];
        }
        
        // Try from GET data (for API calls)
        if (isset($_GET['user_id'])) {
            return $_GET['user_id'];
        }
        
        
        return null;
    }

    private function getOrCreateCart($customerId) {
        $cart = $this->db->select(
            "SELECT cart_id FROM Cart WHERE customer_id = ?",
            [$customerId]
        );
        
        if (!empty($cart)) {
            return $cart[0]['cart_id'];
        }
        
        return $this->db->insert(
            "INSERT INTO Cart (customer_id) VALUES (?)",
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