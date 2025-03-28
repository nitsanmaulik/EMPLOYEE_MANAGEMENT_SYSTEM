<?php
class UpdateTaskProgressAdmin {
    private $conn;
    private $allowedStatuses = ['pending', 'in_progress', 'completed'];
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function updateTaskStatus($taskId, $status) {
        $this->validateStatus($status);
        
        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $taskId);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
        
        return true;
    }
    
    private function validateStatus($status) {
        if (!in_array($status, $this->allowedStatuses)) {
            throw new Exception("Invalid status value");
        }
    }
}
?>