<?php
// Start session at the VERY TOP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect logged-in users
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: delivery_dashboard.php');
    exit();
}

// Handle error messages
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../CSS/style2.css">
</head>
<body>
    <h1>Login</h1>
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>
    
    <form method="POST" action="../control/LoginController.php" onsubmit="return validateForm();">
        <label for="uname">Email:</label>
        <input type="text" id="uname" name="uname"><br>

        <label for="pass">Password:</label>
        <input type="password" id="pass" name="pass"></span><br>

        <button type="submit">Login</button>
    </form>
    <script src="../JS/validate1.js"></script>
</body>
</html>