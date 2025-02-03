<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../css/admin_reg.css">
</head>
<body>
    <div class="registration-container">
        <h2>Admin Registration</h2>

        <!-- Display Backend Error (from PHP Session) -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <p class="error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
        <?php endif; ?>

        <form id="registration-form" action="../controllers/reg_controller.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" >

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" >

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" >

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" >

            <span id="js-error-message" class="error"></span> <!-- JS Error Message -->

            <button type="submit" name="register">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script src="../js/registration.js"></script>
</body>
</html>
