<?php
class AdminDashboardModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getAllUsers() {
        $query = "SELECT id, name, email, role FROM users WHERE role IN ('team_leader', 'employee')";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAllTasks() {
        $query = "SELECT t.id, t.title, t.description, u.name AS assigned_to, t.status 
                  FROM tasks t 
                  JOIN users u ON t.assigned_to = u.id";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function assignTask($title, $description, $assignedTo, $assignedBy) {
        $stmt = $this->conn->prepare("INSERT INTO tasks (title, description, assigned_by, assigned_to, status) 
                                     VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("ssii", $title, $description, $assignedBy, $assignedTo);
        return $stmt->execute();
    }

    public function updateTaskStatus($taskId, $status) {
        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $taskId);
        return $stmt->execute();
    }

    public function deleteTask($taskId) {
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $taskId);
        return $stmt->execute();
    }
}
?>
