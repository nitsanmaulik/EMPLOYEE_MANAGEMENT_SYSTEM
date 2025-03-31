<?php
/**
 * Model class for handling admin dashboard operations
 */
class AdminDashboardModel {
    /** @var mysqli Database connection object */
    private $conn;

    /**
     * Constructor - initializes the database connection
     * @param mysqli $conn MySQLi database connection object
     */
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable exceptions for MySQL errors
    }

    /**
     * Assigns a task to a user
     * @param string $title Task title
     * @param string $description Task description
     * @param int $assignedTo User ID of the assignee
     * @param int $assignedBy User ID of the assigner
     * @return bool True if task assignment was successful, false otherwise
     */
    public function assignTask(string $title, string $description, int $assignedTo, int $assignedBy): bool {
        try {
            $stmt = $this->conn->prepare("INSERT INTO tasks (title, description, assigned_to, assigned_by, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("ssii", $title, $description, $assignedTo, $assignedBy);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error assigning task: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates the status of a task
     * @param int $taskId ID of the task to update
     * @param string $status New status value ('pending', 'in_progress', 'completed')
     * @return bool True if update was successful, false otherwise
     */
    public function updateTaskStatus(int $taskId, string $status): bool {
        try {
            $validStatuses = ['pending', 'in_progress', 'completed'];
            if (!in_array($status, $validStatuses, true)) {
                throw new InvalidArgumentException("Invalid status provided: $status");
            }
            $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $taskId);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error updating task status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a task from the database
     * @param int $taskId ID of the task to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteTask(int $taskId): bool {
        try {
            $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->bind_param("i", $taskId);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error deleting task: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves all users with the role 'employee' or 'team_leader'
     * @return array<int, array{id: int, name: string}> Array of user data
     */
    public function getAllUsers(): array {
        try {
            $result = $this->conn->query("SELECT id, name, role FROM users WHERE role IN ('employee', 'team_leader')");
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error retrieving users: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves all tasks from the database
     * @return array<int, array{id: int, title: string, description: string, assigned_to: int, assigned_by: int, status: string}> Array of task data
     */
    public function getAllTasks(): array {
        try {
            $result = $this->conn->query("
                SELECT t.*, u.name as employee_name 
                FROM tasks t 
                LEFT JOIN users u ON t.assigned_to = u.id
            ");
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error retrieving tasks: " . $e->getMessage());
            return [];
        }
    }
}
?>
