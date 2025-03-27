<?php
session_start();
require '../Config/config.php';

class TaskProgressUpdater {
    private $conn;
    private $adminId;

    public function __construct($conn, $adminId) {
        $this->conn = $conn;
        $this->adminId = $adminId;
    }

    public function updateTaskStatus($taskId, $status) {
        $this->validateInput($taskId, $status);
        
        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $taskId);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
        
        return true;
    }

    private function validateInput($taskId, $status) {
        
        $allowedStatuses = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $allowedStatuses)) {
            throw new Exception("Invalid status value");
        }
    }
}

// Authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header("Location: admin_login.php");
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
    
    header("Location: admindashboard.php?success=Task+Updated");
    exit();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>