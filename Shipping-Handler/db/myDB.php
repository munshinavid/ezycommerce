<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myDB"; // including database name as a connection variable

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Drop table if it already exists
$qry_drop = "DROP TABLE IF EXISTS Users";
$conn->query($qry_drop);

// Below is the query string to create the table
$qry = "CREATE TABLE Users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    email VARCHAR(50)
)";							

$res = $conn->query($qry); // Execute query

if ($res) {
    echo "Table created successfully";
} else {
    echo "Error occurred: " . $conn->error;
}

$conn->close();
?>
