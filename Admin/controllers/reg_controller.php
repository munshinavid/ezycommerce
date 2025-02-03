<?php
session_start();
require_once '../model/db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    $db = new Database(); // Create database instance

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['error_message'] = "All fields are required!";
        header("Location: ../views/admin_reg.php");
        exit();
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $_SESSION['error_message'] = "Passwords do not match!";
        header("Location: ../views/admin_reg.php");
        exit();
    }

    // Check if email is already taken
    if ($db->isEmailTaken($email)) {
        $_SESSION['error_message'] = "This email is already registered. Try another!";
        header("Location: ../views/admin_reg.php");
        exit();
    }

    // Register the admin
    if ($db->registerAdmin($username, $email, $password)) {
        $_SESSION['success_message'] = "Admin registration successful!";
        header("Location: ../views/admin_reg.php"); // Redirect to login page
    } else {
        $_SESSION['error_message'] = "Registration failed. Please try again.";
        header("Location: ../views/admin_reg.php");
    }

    $db->closeConnection();
}
?>
