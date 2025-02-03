<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../db/model.php';


class LoginController { 
    private $model;

    public function __construct() {
        $this->model = new mydb();
    }

    public function login($email, $password) {
        // Add validation
        if (empty($email) || empty($password)) {
            return "Email and password are required";
        }

        // Debugging check
        if (!method_exists($this->model, 'getUserByEmail1')) {
            die("Database method missing! Check model implementation.");
        }

        $user = $this->model->getUserByEmail1($email);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Set all required session variables
                $_SESSION['delivery_man_id'] = $user['id'];
                $_SESSION['delivery_man_name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['loggedin'] = true;
                $_SESSION['last_activity'] = time();

                // Check if password needs rehashing
                if (password_needs_rehash($user['password'], PASSWORD_BCRYPT)) {
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    // Update password in database here if needed
                }

                header("Location: ../view/dashboard.php");
                exit;
            } else {
                return "Invalid email or password.";
            }
        } else {
            return "Invalid email or password.";
        }
    }
}




// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Redirect if already logged in
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        header("Location: ../view/dashboard.php");
        exit;
    }

    $email = trim($_POST['uname']);
    $password = trim($_POST['pass']);

    $controller = new LoginController();
    $error = $controller->login($email, $password);

    if ($error) {
        $_SESSION['error_message'] = $error;
        header("Location: ../view/login.php");
        exit;
    }
}