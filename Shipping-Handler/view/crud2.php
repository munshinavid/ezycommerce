<?php
// Include the model to access database functions
include '../db/db2.php';

// Create an instance of the Database class
$db = new mydb();

// Initialize variables
$result = null;
$query_type = '';

// Check which button was clicked and call the appropriate method from the model
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['view_all'])) {
        $result = $db->getAllUsers();
        $query_type = 'All Users';
    } elseif (isset($_POST['view_limit'])) {
        $result = $db->getLimitedUsers();
        $query_type = 'Limited Users';
    } elseif (isset($_POST['view_like']) && isset($_POST['search'])) {
        $result = $db->getUsersByLike($_POST['search']);
        $query_type = "Users matching '" . $_POST['search'] . "' in ID";
    }
}

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
</head>
<body>
    <h1>View Users</h1>
    
    <!-- Form with buttons to trigger different queries -->
    <form method="POST" action="">
        <button type="submit" name="view_all">View All</button>
        <button type="submit" name="view_limit">View Limit (10)</button>
        <br><br>
        Search by ID: <input type="text" name="search">
        <button type="submit" name="view_like">View by LIKE (ID)</button>
    </form>

    <!-- Display query type -->
    <h2><?php echo $query_type ? "Showing: $query_type" : "Click a button to view records"; ?></h2>

    <!-- Display results in a table if available -->
    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Vehicle Type</th>
                <th>NID Number</th>
                <th>Address</th>
                <th>Photo</th>
                <th>NID</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['vehicle_type']; ?></td>
                    <td><?php echo $row['nid_number']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['photo']; ?></td>
                    <td><?php echo $row['nid']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>No results found.</p>
    <?php endif; ?>
</body>
</html>
