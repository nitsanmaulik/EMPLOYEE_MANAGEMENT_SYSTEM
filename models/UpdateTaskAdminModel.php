<?php
class UpdateTaskAdminModel {
    private $conn;
    
    /**
     * Constructor to initialize the database connection.
     * 
     * @param mysqli $conn Database connection object.
     */
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    
    /**
     * Updates an existing task.
     * 
     * @param int $taskId The ID of the task to update.
     * @param string $title The new title of the task.
     * @param string $description The new description of the task.
     * @param int $assignedTo The ID of the user assigned to the task.
     * 
     * @throws Exception If the assigned user does not exist or a database error occurs.
     * 
     * @return void
     */
    public function updateTask(int $taskId, string $title, string $description, int $assignedTo): void {
        // Verify user exists
        $userCheck = $this->conn->prepare("SELECT id FROM users WHERE id=?");
        $userCheck->bind_param("i", $assignedTo);
        $userCheck->execute();
        
        if ($userCheck->get_result()->num_rows === 0) {
            throw new Exception("Selected user does not exist");
        }
        
        $stmt = $this->conn->prepare("UPDATE tasks SET title=?, description=?, assigned_to=? WHERE id=?");
        $stmt->bind_param("ssii", $title, $description, $assignedTo, $taskId);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
    }
    
    /**
     * Retrieves a specific task's details.
     * 
     * @param int $taskId The ID of the task to retrieve.
     * 
     * @throws Exception If the task is not found.
     * 
     * @return array The task details (title, description, assignedTo).
     */
    public function getTask(int $taskId): array {
        $stmt = $this->conn->prepare("SELECT title, description, assigned_to FROM tasks WHERE id=?");
        $stmt->bind_param("i", $taskId);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
        
        if (!$task) {
            throw new Exception("Task not found");
        }
        
        return $task;
    }
    
    /**
     * Retrieves a list of assignable users based on the current role.
     * 
     * @param string $currentRole The role of the current user (admin or teamLeader).
     * 
     * @return array List of assignable users (id, name).
     */
    public function getAssignableUsers(string $currentRole): array {
        if ($currentRole === 'team_leader') {
            $stmt = $this->conn->prepare("SELECT id, name FROM users WHERE role = 'employee'");
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        $result = $this->conn->query("SELECT id, name FROM users WHERE role IN ('employee', 'team_leader')");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
