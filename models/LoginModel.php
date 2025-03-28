<?php

include("../Config/Config.php");

class User {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function authenticate($email, $password) {
        $stmt = $this->conn->prepare("SELECT id, name, password, role, photo FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        
        return false;
    }
}
?>