<?php
// Start a session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if a success message is set in the session (optional)
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : 'Operation completed successfully!';

// Clear the success message from the session
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    
</head>
<body>
    <div class="success-container">
        <h1>Success!</h1>
        <p><?php echo htmlspecialchars($successMessage); ?></p>
        <a href="crud2.php">CRUD Operations</a>
    </div>
</body>
</html>
