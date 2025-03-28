<?php
class TaskModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function deleteTask($task_id) {
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param('i', $task_id);
        
        return $stmt->execute();
    }
    
    
}
?>