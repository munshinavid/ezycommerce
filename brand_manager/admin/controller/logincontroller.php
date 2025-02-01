<?php
include '../model/model.php';
session_start();
if(isset($_REQUEST['submit']))
{
    if(empty($_REQUEST['email'])) 
    {
        echo "Enter email address";
        include '../view/login.php';
    }
    else if(empty($_REQUEST['password'])) 
    {
        echo "Enter password";
        include '../view/login.php';
    }
    else
    {
        $mydb= new Model();
        $conobj= $mydb->OpenCon();
        $result = $mydb->checkLoginCredentials($conobj, "user", $_REQUEST['email'], $_REQUEST['password']);


        if ($result->num_rows > 0) {
            echo "Login successful!";
        } else {
            echo "Invalid email or password.";
        }
    

    }
   
}



?>