<?php
class EmployeeModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getAllEmployees() {
        $query = "SELECT id, name, email, phone, qualification, role, photo 
                 FROM users 
                 WHERE role IN ('employee', 'team_leader')
                 ORDER BY name ASC";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getEmployeeById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateEmployee($id, $data) {
        $stmt = $this->conn->prepare("UPDATE users SET 
                                    name = ?, email = ?, phone = ?, qualification = ?, role = ?  WHERE id = ?");
        $stmt->bind_param("sssssi", $data['name'],$data['email'], $data['phone'],$data['qualification'],$data['role'], $id );
        return $stmt->execute();
    }

    public function deleteEmployee($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>