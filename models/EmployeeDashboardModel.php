<?php
/**
 * Model class for handling employee dashboard operations
 */
class EmployeeDashboardModel {
    /** @var mysqli Database connection object */
    private $conn;

    /**
     * Constructor - initializes the database connection
     * @param mysqli $connection MySQLi database connection object
     */
    public function __construct(mysqli $connection) {
        $this->conn = $connection;
    }

    /**
     * Retrieves all tasks assigned to a specific employee
     * @param int $employeeId The ID of the employee
     * @return array<array{id: int, title: string, description: string, status: string}> Array of task data
     * @throws RuntimeException If database operation fails
     */
    public function getEmployeeTasks(int $employeeId): array {
        $stmt = $this->conn->prepare("SELECT id, title, description, status FROM tasks WHERE assigned_to = ?");
        $stmt->bind_param("i", $employeeId);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to fetch employee tasks: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Updates the status of a task
     * @param int $taskId The ID of the task to update
     * @param string $status New status for the task (must be 'pending', 'in_progress', or 'completed')
     * @return bool True on success, false on failure
     * @throws InvalidArgumentException If invalid status is provided
     * @throws RuntimeException If database operation fails
     */
    public function updateTaskStatus(int $taskId, string $status): bool {
        $validStatuses = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException("Invalid task status: must be one of " . implode(', ', $validStatuses));
        }

        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $taskId);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to update task status: " . $stmt->error);
        }
        
        return true;
    }

    
}