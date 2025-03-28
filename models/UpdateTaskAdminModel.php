<?php
class UpdateTaskAdminModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function updateTask($task_id, $title, $description, $assigned_to) {
        // Verify user exists
        $user_check = $this->conn->prepare("SELECT id FROM users WHERE id=?");
        $user_check->bind_param("i", $assigned_to);
        $user_check->execute();
        
        if ($user_check->get_result()->num_rows === 0) {
            throw new Exception("Selected user does not exist");
        }
        
        $stmt = $this->conn->prepare("UPDATE tasks SET title=?, description=?, assigned_to=? WHERE id=?");
        $stmt->bind_param("ssii", $title, $description, $assigned_to, $task_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
    }
    
    public function getTask($task_id) {
        $stmt = $this->conn->prepare("SELECT title, description, assigned_to FROM tasks WHERE id=?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
        
        if (!$task) {
            throw new Exception("Task not found");
        }
        
        return $task;
    }
    
    public function getAssignableUsers($current_role) {
        if ($current_role === 'team_leader') {
            $stmt = $this->conn->prepare(
                "SELECT id, name FROM users WHERE role = 'employee'"
            );
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        $result = $this->conn->query(
            "SELECT id, name FROM users WHERE role IN ('employee', 'team_leader')"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>