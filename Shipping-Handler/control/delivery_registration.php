<?php
include '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and capture form data
    $name = trim(htmlspecialchars($_POST['name']));
    $gender = $_POST['gender'];
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim(htmlspecialchars($_POST['phone']));
    $vehicle_type = $_POST['vehicle_type'];
    $nid_number = trim(htmlspecialchars($_POST['nid_number']));
    $address = trim(htmlspecialchars($_POST['address']));

    // File uploads
    $photo = $_FILES['photo']['name'];
    $nid = $_FILES['nid']['name'];
    $errors = [];

    // Validate name
    if (empty($name) || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors['name'] = "Please enter a valid name (letters and spaces only).";
    }

    // Validate gender
    if (empty($gender) || !in_array($gender, ['male', 'female'])) {
        $errors['gender'] = "Please select a valid gender.";
    }

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    // Validate password
    if (empty($password) || strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters long.";
    }

    // Confirm password match
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // Validate phone number
    if (empty($phone) || !preg_match("/^[0-9]{10,15}$/", $phone)) {
        $errors['phone'] = "Please enter a valid phone number (10-15 digits).";
    }

    // Validate NID number
    if (empty($nid_number) || !is_numeric($nid_number)) {
        $errors['nid_number'] = "Please enter a valid NID number (numeric only).";
    }

    // File upload directory
    $upload_dir = '../uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // File validation function
    function validateFile($file, $allowed_types, $upload_dir, $field_name, &$errors) {
        if (!empty($file['name'])) {
            $tmp_name = $file['tmp_name'];
            $type = mime_content_type($tmp_name);
            $size = $file['size'];

            if (!in_array($type, $allowed_types)) {
                $errors[$field_name] = "Only JPEG, JPG, and PNG formats are allowed.";
            } elseif ($size > 5 * 1024 * 1024) { // 5MB max size
                $errors[$field_name] = "File size must not exceed 5MB.";
            } else {
                $new_name = uniqid() . "_" . basename($file['name']);
                if (!move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                    $errors[$field_name] = "Failed to upload the file.";
                }
                return $new_name;
            }
        } else {
            $errors[$field_name] = ucfirst($field_name) . " upload is required.";
        }
        return null;
    }

    // Validate photo and NID
    $photo_name = validateFile($_FILES['photo'], ['image/jpeg', 'image/png', 'image/jpg'], $upload_dir, 'photo', $errors);
    $nid_name = validateFile($_FILES['nid'], ['image/jpeg', 'image/png', 'image/jpg'], $upload_dir, 'nid', $errors);

    // Proceed with database insertion if no errors
    if (empty($errors)) {
        $db = new mydb();
        $con = $db->openCon();

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $result = $db->addUser(
            $con,
            'delivery_man',
            $name,
            $gender,
            $email,
            $hashed_password,
            $phone,
            $vehicle_type,
            $nid_number,
            $address,
            $photo_name,
            $nid_name
        );

        if ($result) {
            session_start();
$_SESSION['success_message'] = 'You have successfully registered!';
header('Location: ../view/success.php');
exit();
        } else {
            echo "Database Error: " . $con->error;
        }

        $db->closeCon($con);
    } else {
        // Display validation errors
        echo "<div class='errors'>";
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo "</div>";
    }
}
?>
