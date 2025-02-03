<?php

session_start();

if (!isset($_SESSION['error_message'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <link rel="stylesheet" href="../css/registration.css">
</head>
<body>
    <div class="registration-container">
        <h2>Customer Registration</h2>

        <!-- Display Backend Error (from PHP Session) -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <p class="error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
        <?php endif; ?>

        <form id="registration-form" action="../controllers/Reg_control.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" >
            <span id="username-error" class="error"></span> <!-- JS Error Message for Username -->

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" >
            <span id="email-error" class="error"></span> <!-- JS Error Message for Email -->

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" >
            <span id="password-error" class="error"></span> <!-- JS Error Message for Password -->

            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" >
            <span id="full_name-error" class="error"></span> <!-- JS Error Message for Full Name -->

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" >
            <span id="phone-error" class="error"></span> <!-- JS Error Message for Phone -->

            <label for="billing_address">Billing Address:</label>
            <textarea id="billing_address" name="billing_address"></textarea>
            <span id="billing_address-error" class="error"></span> <!-- JS Error Message for Billing Address -->

            <label for="shipping_address">Shipping Address:</label>
            <textarea id="shipping_address" name="shipping_address"></textarea>
            <span id="shipping_address-error" class="error"></span> <!-- JS Error Message for Shipping Address -->

            <button type="submit" name="register">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script src="../scripts/register.js"></script>
</body>
</html>
