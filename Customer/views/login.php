<?php
// Include the UserController class
require_once '../controllers/UserController.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email and password from form input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Instantiate the controller and attempt to log in
    $userController = new UserController();
    $error_message = $userController->login($email, $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="this is an ecommerce project for making anis express" />
    <title>Ecommerce project</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/nav.css" />
    <link rel="stylesheet" href="../css/footer.css" />
</head>
<body>
    <!-- navbar starts here -->
    <?php include '../layout/navbar.php'; ?>
    <!-- navbar ends here -->

    <main>
        <section class="login">
            <h2 class="section-title text-center">User Login</h2>
            <div class="card">
                <?php if (isset($error_message)) : ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form action="" method="POST" class="form">
                    <div class="form-control flex-center">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required autocomplete="email" />
                    </div>
                    <div class="form-control flex-center">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required autocomplete="password" />
                    </div>

                    <div class="form-control flex-center form-btn-field">
                        <button type="submit" class="btn contact-btn">Submit</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- footer starts here -->
    <?php include '../layout/footer.php'; ?>
    <!-- footer ends here -->
    <script src="./scripts/index.js"></script>
</body>
</html>
