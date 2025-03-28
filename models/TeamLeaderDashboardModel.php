<?php
class TeamLeaderDashboardModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getTeamLeaderDetails($teamLeaderId) {
        $stmt = $this->conn->prepare("SELECT name, photo FROM users WHERE id = ?");
        $stmt->bind_param("i", $teamLeaderId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getTeamMembers() {
        $result = $this->conn->query(
            "SELECT id, name, email FROM users WHERE role = 'employee' ORDER BY name ASC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAssignedTasks($teamLeaderId) {
        $stmt = $this->conn->prepare("
            SELECT t.id, t.title, t.description, u.name AS employee_name, t.status 
            FROM tasks t JOIN users u ON t.assigned_to = u.id WHERE t.assigned_by = ? AND u.role = 'employee'");
        $stmt->bind_param("i", $teamLeaderId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getMyTasks($teamLeaderId) {
        $stmt = $this->conn->prepare("
            SELECT id, title, description, status FROM tasks 
            WHERE assigned_to = ?
        ");
        $stmt->bind_param("i", $teamLeaderId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function assignTask($title, $description, $assignedTo, $assignedBy) {
        $stmt = $this->conn->prepare("
            INSERT INTO tasks (title, description, assigned_by, assigned_to, status) 
            VALUES (?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("ssii", $title, $description, $assignedBy, $assignedTo);
        return $stmt->execute();
    }

    public function updateTaskStatus($taskId, $status) {
        $validStatuses = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status value");
        }

        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $taskId);
        return $stmt->execute();
    }

    public function deleteTask($taskId, $teamLeaderId) {
        // Verify the task exists and was assigned by this team leader
        $stmt = $this->conn->prepare("SELECT id FROM tasks WHERE id = ? AND assigned_by = ?");
        $stmt->bind_param("ii", $taskId, $teamLeaderId);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            return false; 
        }
    
        // Delete the task
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $taskId);
        return $stmt->execute();
    }
}
?>