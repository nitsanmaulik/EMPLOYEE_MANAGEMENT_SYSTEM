<?php
class TaskModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function assignTask($title, $description, $assignedBy, $assignedTo) {
        
        if (empty($title) || empty($description) || empty($assignedTo)) {
            throw new Exception("All fields are required");
        }

        
        if (!$this->userExists($assignedTo)) {
            throw new Exception("Invalid employee selected");
        }

        // Insert task
        $stmt = $this->conn->prepare("INSERT INTO tasks (title, description, assigned_by, assigned_to, status) 
                                     VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("ssii", $title, $description, $assignedBy, $assignedTo);

        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }

        return true;
    }

    private function userExists($userId) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (bool)$stmt->get_result()->fetch_assoc();
    }

    public function getAssignableUsers() {
        $query = "SELECT id, name, role FROM users WHERE role IN ('employee', 'team_leader') ORDER BY name ASC";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>