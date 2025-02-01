<?php
session_start();
include "../model/Model.php";

class ManagerController {
    private $model;

    public function __construct() {
        $this->model = new Model();
    }

    public function login($email, $password) {
        $user = $this->model->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: ../view/manager.php");
            exit();
        } else {
            echo "Invalid email or password.";
        }
    }

    public function signup($firstname, $email, $password) {
        if ($this->model->createUser($firstname, $email, $password)) {
            header("Location: ../view/login.php");
        } else {
            echo "Error: Could not create user.";
        }
    }

    public function checkAccess() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../view/login.php");
            exit();
        }
    }
}

// Handle requests
$manager = new ManagerController();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup'])) {
        $manager->signup($_POST['firstname'], $_POST['email'], $_POST['password']);
    } elseif (isset($_POST['login'])) {
        $manager->login($_POST['email'], $_POST['password']);
    }
}
?>
