<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
</head>
<body>
    <form id="form" action="signup_validation.php" method="POST">
        <h2>Signup</h2>

        <label>Firstname:</label>
        <input type="text" id="firstname-input" name="firstname">
        <span id="firstname-error"></span><br>

        <label>Email:</label>
        <input type="text" id="email-input" name="email">
        <span id="email-error"></span><br>

        <label>Password:</label>
        <input type="password" id="password-input" name="password">
        <span id="password-error"></span><br>

        <label>Repeat Password:</label>
        <input type="password" id="repeat-password-input" name="repeat_password">
        <span id="repeat-password-error"></span><br>

        <button type="submit">Signup</button>
    </form>

    <!-- Fixed Login link -->
    <p>Already have an account? <a href="Login.php">Login here</a></p>

    <!-- Include validation script -->
    <script src="validation.js"></script>
</body>
</html>