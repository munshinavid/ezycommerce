<?php
class mydb {
    // Open database connection
    function openCon() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "mydb"; // Replace with your actual database name

        $conObj = new mysqli($servername, $username, $password, $dbname);

        // Check for connection error
        if ($conObj->connect_error) {
            die("Connection failed: " . $conObj->connect_error);
        }

        return $conObj;
    }

    // Insert user into the database using prepared statements
    function addUser($conObj, $table, $name, $gender, $email, $password, $phone, $vehicle_type, $nid_number, $address, $photo, $nid) {
        $querystring = "INSERT INTO $table 
                        (name, gender, email, password, phone, vehicle_type, nid_number, address, photo, nid) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conObj->prepare($querystring);

        if (!$stmt) {
            die("Error preparing query: " . $conObj->error);
        }

        // Bind parameters to prevent SQL injection
        $stmt->bind_param(
            "ssssssssss", 
            $name, 
            $gender, 
            $email, 
            $password, 
            $phone, 
            $vehicle_type, 
            $nid_number, 
            $address, 
            $photo, 
            $nid
        );

        // Execute the statement
        $result = $stmt->execute();

        // Close the statement
        $stmt->close();

        return $result;
    }

    // Close database connection
    function closeCon($conObj) {
        $conObj->close();
    }
}
?>
