<?php
session_start();
require '../Config/Config.php';

/**
 * Handles updating task progress status with validation and authorization checks
 */
class TaskProgressUpdater {
    /** @var mysqli Database connection object */
    private $conn;
    
    /** @var int Current user ID */
    private $userId;

    /**
     * Constructor for TaskProgressUpdater
     * 
     * @param mysqli $conn Database connection object
     * @param int $userId Current user ID
     */
    public function __construct(mysqli $conn, int $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
    }

    /**
     * Updates the status of a task with validation and authorization checks
     * 
     * @param int $taskId ID of the task to update
     * @param string $status New status for the task
     * @return bool Returns true on success
     * @throws Exception On validation failure, authorization failure, or database error
     */
    public function updateTaskStatus(int $taskId, string $status): bool {
        $this->validateInput($taskId, $status);
        
        $currentTask = $this->getTask($taskId);
        
        if ($currentTask['status'] === 'completed') {
            throw new Exception("Cannot update a completed task");
        }
        
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

    /**
     * Retrieves task information from the database
     * 
     * @param int $taskId ID of the task to retrieve
     * @return array Task data (status and assigned_to)
     * @throws Exception If task is not found
     */
    private function getTask(int $taskId): array {
        $stmt = $this->conn->prepare("SELECT status, assigned_to FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $taskId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Task not found");
        }
        
        return $result->fetch_assoc();
    }

    /**
     * Validates input parameters
     * 
     * @param mixed $taskId Task ID to validate
     * @param mixed $status Status value to validate
     * @throws Exception If validation fails
     */
    private function validateInput($taskId, $status): void {
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

    $taskUpdater = new TaskProgressUpdater($conn, (int)$_SESSION['user_id']);
    $taskUpdater->updateTaskStatus((int)$_POST['task_id'], $_POST['status']);
    
    $_SESSION['success_message'] = "Task status updated successfully";
    header("Location: EmployeeDashboard.php");
    exit();
    
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: EmployeeDashboard.php");
    exit();
}