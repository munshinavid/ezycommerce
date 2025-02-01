<?php
session_start();
include_once '../model/Model.php';

class LoginController { 
    private $model;

    public function __construct() {
        // Initialize the Model instance
        $this->model = new Model();
    }

    public function login($email, $password) {
        // Check if email and password are provided
        if (empty($email) || empty($password)) {
            return "Email and Password are required.";
        }

        // Fetch user details by email
        $user = $this->model->getUserByEmail1($email); // Ensure this method interacts with the correct table

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Set session variable
                $_SESSION['email'] = $user['email'];
                header("Location: ../view/manager.php"); // Redirect to manager dashboard
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
    // Retrieve input from the login form
    $email = trim($_POST['uname']);
    $password = trim($_POST['pass']);

    // Initialize the LoginController
    $controller = new LoginController();

    // Attempt login
    $error = $controller->login($email, $password);

    // If login fails, set the error message and redirect back to login page
    if ($error) {
        $_SESSION['error_message'] = $error;
        header("Location: ../view/login.php");
        exit;
    }
}
?>
