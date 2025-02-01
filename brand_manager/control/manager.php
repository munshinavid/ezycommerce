<?php
session_start();
include('../model/Model.php');

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../view/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST') {
    $model = new Model();
    $conn = $model->OpenCon();

    $action = isset($_GET['action']) ? $_GET['action'] : '';

     
   switch ($action) {
    case 'view_users': // View all users
        $result = $model->getAllUsers('users'); // Only pass the table name
        if (!empty($result)) {
            echo "<h2>Users</h2><table border='1'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th></tr>";
            foreach ($result as $row) {
                echo "<tr><td>{$row['user_id']}</td><td>{$row['username']}</td><td>{$row['email']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No users found.</p>";
        }
        break;

        case 'show_all_products': // View all products
            $result = $model->getAllProducts( 'products');
            if (!empty($result)) {
                echo "<h2>All Products</h2><table border='1'>";
                echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Discount ID</th><th>Start Date</th><th>End Date</th><th>Image URL</th></tr>";
                foreach ($result as $row) {
                    $start_date = isset($row['start_date']) ? $row['start_date'] : 'N/A';
                    $end_date = isset($row['end_date']) ? $row['end_date'] : 'N/A';
                    echo "<tr>
                            <td>{$row['product_id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['price']}</td>
                            <td>{$row['stock']}</td>
                            <td>{$row['discount_id']}</td>
                            <td>{$start_date}</td>
                            <td>{$end_date}</td>
                            <td>{$row['image_url']}</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No products found.</p>";
            }
            break;
         

            case 'search_product': // Search for a product
                if (isset($_POST['search'])) {
                    $search = trim($_POST['search']);
                    $result = $model->getProductByIdOrName('products', $search);
            
                    if (!empty($result)) {
                        echo "<h2>Search Results</h2><table border='1'>";
                        echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th></tr>";
                        foreach ($result as $row) {
                            echo "<tr>
                                    <td>{$row['product_id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['price']}</td>
                                    <td>{$row['stock']}</td>
                                  </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No product found.</p>";
                    }
                } else {
                    echo "<p>No search input provided.</p>";
                }
                break;
 
                
                case 'add_product': // Add a product
                    if (isset($_POST['add_product'])) {
                        $product_id = trim($_POST['product_id']);
                        $name = trim($_POST['name']);
                        $price = trim($_POST['price']);
                        $stock = trim($_POST['stock']);
                
                        // File upload handling
                        $target_dir = "uploads/";
                        
                        // Ensure the uploads directory exists
                        if (!is_dir($target_dir)) {
                            mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
                        }
                
                        $target_file = $target_dir . uniqid() . "_" . basename($_FILES["fileToUpload"]["name"]);
                
                        // Check if temporary file exists
                        if (!file_exists($_FILES["fileToUpload"]["tmp_name"])) {
                            echo "<p style='color:red;'>Temporary file does not exist.</p>";
                            exit;
                        }
                
                        // Move the uploaded file
                        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                            $image_path = $target_file;
                        } else {
                            echo "<p style='color:red;'>Sorry, there was an error uploading your file.</p>";
                            break;
                        }
                
                        // Check for empty fields
                        if (empty($product_id) || empty($name) || empty($price) || empty($stock)) {
                            echo "<p style='color:red;'>All fields are required!</p>";
                        } else {
                            // Add product to the database
                            $result = $model->addProduct('products', $product_id, $name, $price, $stock, $image_path);
                
                            if ($result) {
                                echo "<p style='color:green;'>Product added successfully!</p>";
                            } else {
                                echo "<p style='color:red;'>Failed to add product. Please try again.</p>";
                            }
                        }
                    }
                    break;
                
                
                


                    case 'delete_product': // Delete a product
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
                            $product_id = trim($_POST['product_id']);
                    
                            if (empty($product_id)) {
                                echo "<p style='color:red;'>Product ID is required!</p>";
                            } else {
                                $result = $model->deleteProduct('products', $product_id);
                    
                                if ($result) {
                                    echo "<p style='color:green;'>Product deleted successfully!</p>";
                                } else {
                                    echo "<p style='color:red;'>Failed to delete product. Please try again.</p>";
                                }
                            }
                        }
                        break;
                    

        case 'logout': // Logout
            session_destroy();
            header("Location: ../view/login.php");
            break;

        default:
            echo "<p>No valid action specified.</p>";
            break;
    }

    $conn = null;
}
?>
