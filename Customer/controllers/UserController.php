<?php
require_once '../models/db.php';
require_once '../models/UserModel.php';
//session_start();

class UserController {

    // Method to handle user login
    public function login($email, $password) {
        session_start();
        $db = new Database();

        // Prepare and execute the query to check if the user exists
        $query = "SELECT * FROM users WHERE email = ?";
        $user = $db->select($query, [$email]);

        // If the user exists and password matches
        if ($user && $password === $user[0]['password']) { 
            // Start the session and store user details

            $_SESSION['user_id'] = $user[0]['user_id'];
            $_SESSION['username'] = $user[0]['username'];
            $_SESSION['role'] = $user[0]['role'];

            // Get cart data for the user
            $this->getCartData($user[0]['user_id']);

            // Redirect the user to the homepage or cart
            header('Location: ../views/index.php');
            exit();
        } else {
            return "Invalid email or password.";
        }
    }

    // Get cart details (items count and total price)
    public function getCartData($userId) {
        $db = new Database();

        // Query to get cart items count and total price for the user
        $query = "SELECT 
                        SUM(ci.quantity) AS cart_count, 
                        SUM(ci.quantity * p.price) AS cart_total 
                  FROM Cart c
                  JOIN Cart_Items ci ON c.cart_id = ci.cart_id
                  JOIN Products p ON ci.product_id = p.product_id
                  WHERE c.customer_id = ?";

        $cartData = $db->select($query, [$userId]);

        // If cart data is available, store it in the session
        if ($cartData) {
            $_SESSION['cart_count'] = $cartData[0]['cart_count'] ?? 0;
            $_SESSION['cart_total'] = number_format($cartData[0]['cart_total'], 2) ?? 0.00;
        } else {
            // If no cart data found, set to 0
            $_SESSION['cart_count'] = 0;
            $_SESSION['cart_total'] = 0.00;
        }
    }

    public function updateProfile($userId, $name, $email, $password) {
        $userModel = new UserModel();

        //$user = $userModel->getUserById($userId);
        $result = $userModel->updateUser($userId, $name, $email, $password);

        if ($result===true) {
            $_SESSION['success_message'] = "Profile updated successfully!";
        } else {
            $_SESSION['error_message'] = "Duplicate Entry, Olease chosse another.";
        }

        header("Location: ../views/update_profile.php");
    }

    // Optionally, you can add a logout method
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: ../views/login.php');
        exit();
    }
}

// Handle profile update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../views/login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    //$address = trim($_POST['address']);

    $userController = new UserController();
    $userController->updateProfile($userId, $name, $email, $password);
}
