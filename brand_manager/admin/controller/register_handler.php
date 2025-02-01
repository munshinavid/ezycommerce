<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('../model/model.php');
    $model = new Model();
    $conn = $model->OpenCon();

    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Insert data into the database
    $result = $model->AddStudent($conn, 'user', $username, $email, $password);

    if ($result) {
        echo "Registration successful!";
    } else {
        echo "Error during registration: " . $conn->error;
    }

    $conn->close();
}
?>
