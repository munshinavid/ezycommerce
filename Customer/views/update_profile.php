<?php
require_once '../models/UserModel.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userController = new UserModel();
$user = $userController->getUserById($_SESSION['user_id']);

if (!$user) {
    die("User not found.");
}
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

        <form action="../controllers/UserController.php" method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['username']) ?>" >

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" >

            <label>Phone:</label>
            <input type="text" name="password" value="<?= htmlspecialchars($user['password']) ?>" >

            

            <button type="submit" name="update">Save Changes</button>
        </form>
        <a href="profile.php">Back to Dashboard</a>
    </div>
</body>
</html>
