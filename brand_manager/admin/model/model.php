<?php
class Model
{

    function OpenCon(){
      $conn= new mysqli("localhost","root","","mydb");
      return $conn;
    }
    function AddCustomer($conn, $table, $username, $email, $password) {
      $stmt = $conn->prepare("INSERT INTO $table (username, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $username, $email, $password);
      $result = $stmt->execute();
      $stmt->close();
      return $result;
  }
  
     
    function checkLoginCredentials($conn,$table, $email,$password){
      $sql="SELECT email, password from $table WHERE email = '$email' AND 
      password = '$password'";
      $result = $conn->query($sql);
      return $result;
    }
    function getAllusers($conn,$table){
      $sql="SELECT * FROM $table";
      $result = $conn->query($sql);
      return $result;
    }
    function getUserdata($conn,$table,$userid)
    {
      $sql="SELECT * FROM $table WHERE id = '$userid'";
      $result=$conn->query($sql);
      return $result;
    }
    function Adduser($conn,$table, $username, $email, 
    $password){
        $sql="INSERT INTO $table (username,email, password) VALUES 
        ('$username', '$email', '$password')";
       $result= $conn->query($sql);
       return $result;
    }
    function updateUser($conn,$table,$userid,$name,$password)
    {
      $sql="UPDATE $table SET name='$name', password='$password' WHERE id='$userid'";
      $result=$conn->query($sql);
      return $result;

    }



}

?>