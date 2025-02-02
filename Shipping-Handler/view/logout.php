<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: delivery_dashboard.php");
    exit;
}
?>