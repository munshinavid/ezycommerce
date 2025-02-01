<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script src="../js/validate1.js"></script> <!-- Link the JavaScript validation -->
</head>
<body>
    <h1>Login</h1>
    <!-- Display Error Message from Session -->
    <?php
    session_start();
    if (isset($_SESSION['error_message'])) {
        echo "<p style='color: red;'>{$_SESSION['error_message']}</p>";
        unset($_SESSION['error_message']); // Clear the error after displaying
    }
    ?>
    <form method="POST" action="../control/LoginController.php" onsubmit="return validateForm();">
        <label for="uname">Email:</label>
        <input type="text" id="uname" name="uname">
        <span id="error" style="color: red;"></span><br>

        <label for="pass">Password:</label>
        <input type="password" id="pass" name="pass">
        <span id="passError" style="color: red;"></span><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
