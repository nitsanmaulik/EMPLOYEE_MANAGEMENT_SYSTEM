<?php
session_start();
require '../Config/Config.php';

class TaskProgressUpdater {
    private $conn;
    private $userId;

    public function __construct($conn, $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
    }

    public function updateTaskStatus($taskId, $status) {
        $this->validateInput($taskId, $status);
        
        // Check current task status and ownership
        $currentTask = $this->getTask($taskId);
        
        // Prevent updating completed tasks
        if ($currentTask['status'] === 'completed') {
            throw new Exception("Cannot update a completed task");
        }
        
        // Verify the task is assigned to the current user (for employee dashboard)
        if ($_SESSION['role'] === 'employee' && $currentTask['assigned_to'] != $this->userId) {
            throw new Exception("You can only update your own tasks");
        }

        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $taskId);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
        
        return true;
    }

    private function getTask($taskId) {
        $stmt = $this->conn->prepare("SELECT status, assigned_to FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $taskId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Task not found");
        }
        
        return $result->fetch_assoc();
    }

    private function validateInput($taskId, $status) {
        if (!is_numeric($taskId) || $taskId <= 0) {
            throw new Exception("Invalid task ID");
        }
        
        $allowedStatuses = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $allowedStatuses)) {
            throw new Exception("Invalid status value");
        }
    }
}

// Authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header("Location: ../index.php");
    exit();
}

// Main execution
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
        throw new Exception("Missing task details");
    }

    $taskUpdater = new TaskProgressUpdater($conn, $_SESSION['user_id']);
    $taskUpdater->updateTaskStatus($_POST['task_id'], $_POST['status']);
    
    $_SESSION['success_message'] = "Task status updated successfully";
    header("Location: EmployeeDashboard.php");
    exit();
    
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: EmployeeDashboard.php");
    exit();
}
?>