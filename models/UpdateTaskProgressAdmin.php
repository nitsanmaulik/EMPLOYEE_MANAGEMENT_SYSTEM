<?php
class UpdateTaskProgressAdmin {
    private $conn;
    private array $allowedStatuses = ['pending', 'in_progress', 'completed'];
    
    /**
     * Constructor to initialize the database connection.
     * 
     * @param mysqli $conn Database connection object.
     */
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    
    /**
     * Updates the status of a task.
     * 
     * @param int $taskId The ID of the task to update.
     * @param string $status The new status of the task.
     * 
     * @throws Exception If the status is invalid or a database error occurs.
     * 
     * @return bool Returns true on success.
     */
    public function updateTaskStatus(int $taskId, string $status): bool {
        $this->validateStatus($status);
        
        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $taskId);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
        
        return true;
    }
    
    /**
     * Validates the provided status.
     * 
     * @param string $status The status to validate.
     * 
     * @throws Exception If the status is not allowed.
     * 
     * @return void
     */
    private function validateStatus(string $status): void {
        if (!in_array($status, $this->allowedStatuses, true)) {
            throw new Exception("Invalid status value");
        }
    }
}
?>
