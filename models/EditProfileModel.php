<?php
class EditProfileModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUserById($user_id) {
        $stmt = $this->conn->prepare("SELECT name, email, phone, qualification, photo FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function updateUserWithPassword($user_id, $name, $email, $phone, $qualification, $photo, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, qualification = ?, photo = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $name, $email, $phone, $qualification, $photo, $hashed_password, $user_id);
        return $stmt->execute();
    }

    public function updateUserWithoutPassword($user_id, $name, $email, $phone, $qualification, $photo) {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, qualification = ?, photo = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $qualification, $photo, $user_id);
        return $stmt->execute();
    }

    public function closeConnection() {
        $this->conn->close();
    }
}
?>