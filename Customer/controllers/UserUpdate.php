<?php
require_once __DIR__ . '/../models/UserModel.php';
session_start();

// Check if the update request was made
if (isset($_POST['update'])) {
    // Assuming you have all the necessary data from the form
    $userId = $_SESSION['user']['id'] ?? null;
    $username = $_POST['name'];
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'] ?? null;
    $fullName = $_POST['full_name'];
    $phone = $_POST['phone'];
    $billingAddress = $_POST['billing_address'];
    $shippingAddress = $_POST['shipping_address'];

    $userModel = new UserModel();

    // Check if the new email is duplicated
    if ($userModel->isEmailTaken($email, $userId)) {
        $_SESSION['error_message'] = 'This email is already in use. Please choose another one.';
        header('Location: ' . url('/profile'));
        exit();
    }

    // Update user data
    $userUpdated = $userModel->updateUser($userId, $username, $email, $newPassword);

    // Update customer details
    $customerDetailsUpdated = $userModel->updateCustomerDetails($userId, $fullName, $phone, $billingAddress, $shippingAddress);

    if ($userUpdated && $customerDetailsUpdated) {
        $_SESSION['success_message'] = 'Profile updated successfully!';
    } else {
        $_SESSION['error_message'] = 'There was an error updating your profile.';
    }

    header('Location: ' . url('/profile'));
    exit();

} else {
    // header("Location: ../views/updateprofile.php");
    // exit();
}
