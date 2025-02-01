<?php
include '../db/db.php';

$db = new mydb();
$conn = $db->openCon();
$query_type = ''; 
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['view_all'])) {
        $sql = "SELECT id, name, gender, email, phone, vehicle_type, nid_number, address, photo, nid FROM delivery_man";
        $result = $conn->query($sql);
        $query_type = 'All Users';
    } elseif (isset($_POST['view_limit'])) {
        // Query to view only 10 records
        $sql = "SELECT id, name, gender, email, phone, vehicle_type, nid_number, address, photo, nid FROM delivery_man LIMIT 10";
        $result = $conn->query($sql);
        $query_type = 'Limited Users';
    }
}

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
    
    <form method="POST" action="">
        <button type="submit" name="view_all">View All</button>
        <button type="submit" name="view_limit">View Limit (10)</button>
    </form>

    <h2><?php echo $query_type ? "Showing: $query_type" : "Click a button to view records"; ?></h2>

    <?php 
    if ($result && $result->num_rows > 0): ?>
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
    <?php 
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>No results found.</p>
    <?php endif; ?>

</body>
</html>

<?php
$db->closeCon($conn);
?>
