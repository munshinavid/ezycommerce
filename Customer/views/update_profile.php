<?php
require_once '../models/UserModel.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userModel = new UserModel();
$user = $userModel->getUserById($_SESSION['user_id']);

if (!$user) {
    die("User not found.");
}

$customerDetails = $userModel->getCustomerDetails($_SESSION['user_id']); // Fetch details

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="../css/update_profile.css">
</head>
<body>
    <div class="container">
        <h2>Update Profile</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <p class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <p class="error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
        <?php endif; ?>

        <form action="../controllers/UserUpdate.php" method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= $user['username'] ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= $user['email'] ?>" required>

            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?= $customerDetails['full_name'] ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?= $customerDetails['phone'] ?>" required>

            <label>Billing Address:</label>
            <textarea name="billing_address" required><?= $customerDetails['billing_address'] ?></textarea>

            <label>Shipping Address:</label>
            <textarea name="shipping_address" required><?= $customerDetails['shipping_address'] ?></textarea>

            <label>Change Password:</label>
            <input type="password" name="new_password" placeholder="Leave blank to keep current password">

            <button type="submit" name="update">Save Changes</button>
        </form>
        <a href="profile.php">Back to Dashboard</a>
    </div>
</body>
</html>
