<?php
require_once 'db.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getUserById($userId) {
        $query = "SELECT * FROM users WHERE user_id = ?";
        $result = $this->db->select($query, [$userId]);
        return $result ? $result[0] : null;
    }

    public function updateUser($userId, $username, $email, $password) {
        $query = "UPDATE users SET username = ?, email = ?, password = ? WHERE user_id = ?";
        $result= $this->db->execute($query, [$username, $email, $password, $userId]);
        if($result){
            return true;
        }else{
            return false;
        }
    }
}
