<?php
include '../model/Model.php';// Ensure your database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $repeatPassword = $_POST["repeat_password"];

    // Check if any field is empty
    if (empty($firstname) || empty($email) || empty($password) || empty($repeatPassword)) {
        header("Location: signup.php?error=empty_fields");
        exit();
    }

    // Check if passwords match
    if ($password !== $repeatPassword) {
        header("Location: signup.php?error=password_mismatch");
        exit();
    }

    // Check if email already exists in the database
    $model = new Model();
    $existingUser = $model->getUserByEmail1($email);

    if ($existingUser) {
        // Email is already taken
        header("Location: signup.php?error=email_taken");
        exit();
    }

    // Create the new addmanager
    $result = $model->createUser1($firstname, $email, $password);

    if ($result) {
        // Redirect to login after successful signup
        header("Location: Login.php?signup=success");
        exit();
    } else {
        // Handle database error
        header("Location: signup.php?error=database_error");
        exit();
    }
}
?>