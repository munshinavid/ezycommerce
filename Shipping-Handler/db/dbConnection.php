<?php
$servername = "localhost"; // default host name
$username = "root"; // default user is root
$password = ""; // default password is empty

// Create connection using mysqli function
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
