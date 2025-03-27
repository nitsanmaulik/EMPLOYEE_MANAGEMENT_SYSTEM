<?php
class EmployeeDashboardModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getEmployeeTasks($employeeId) {
        $stmt = $this->conn->prepare("SELECT id, title, description, status FROM tasks WHERE assigned_to = ?");
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateTaskStatus($taskId, $status) {
        $validStatuses = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid task status");
        }

        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $taskId);
        return $stmt->execute();
    }

    public function getEmployeeDetails($employeeId) {
        $stmt = $this->conn->prepare("SELECT name, photo FROM users WHERE id = ?");
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>