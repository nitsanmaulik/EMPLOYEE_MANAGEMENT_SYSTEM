<?php
/**
 * Model class for handling team leader dashboard operations
 */
class TeamLeaderDashboardModel {
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
     * Retrieves team leader details
     * @param int $teamLeaderId ID of the team leader
     * @return array{name: string, photo: string|null}|null Associative array with team leader details or null if not found
     * @throws RuntimeException If database operation fails
     */
    public function getTeamLeaderDetails(int $teamLeaderId): ?array {
        $stmt = $this->conn->prepare("SELECT name, photo FROM users WHERE id = ?");
        $stmt->bind_param("i", $teamLeaderId);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to fetch team leader details: " . $stmt->error);
        }
        
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Retrieves all team members (employees)
     * @return array<array{id: int, name: string, email: string}> Array of team member data
     * @throws RuntimeException If database operation fails
     */
    public function getTeamMembers(): array {
        $result = $this->conn->query(
            "SELECT id, name, email FROM users WHERE role = 'employee' ORDER BY name ASC"
        );
        
        if ($result === false) {
            throw new RuntimeException("Failed to fetch team members: " . $this->conn->error);
        }
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Retrieves tasks assigned by the team leader to employees
     * @param int $teamLeaderId ID of the team leader
     * @return array<array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     employee_name: string,
     *     status: string
     * }> Array of task data
     * @throws RuntimeException If database operation fails
     */
    public function getAssignedTasks(int $teamLeaderId): array {
        $stmt = $this->conn->prepare("
            SELECT t.id, t.title, t.description, u.name AS employee_name, t.status 
            FROM tasks t JOIN users u ON t.assigned_to = u.id 
            WHERE t.assigned_by = ? AND u.role = 'employee'
        ");
        $stmt->bind_param("i", $teamLeaderId);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to fetch assigned tasks: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Retrieves tasks assigned to the team leader
     * @param int $teamLeaderId ID of the team leader
     * @return array<array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     status: string
     * }> Array of task data
     * @throws RuntimeException If database operation fails
     */
    public function getMyTasks(int $teamLeaderId): array {
        $stmt = $this->conn->prepare("
            SELECT id, title, description, status FROM tasks 
            WHERE assigned_to = ?
        ");
        $stmt->bind_param("i", $teamLeaderId);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to fetch team leader tasks: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Assigns a new task to a team member
     * @param string $title Task title
     * @param string $description Task description
     * @param int $assignedTo ID of the employee being assigned the task
     * @param int $assignedBy ID of the team leader assigning the task
     * @return bool True on success, false on failure
     * @throws RuntimeException If database operation fails
     */
    public function assignTask(string $title, string $description, int $assignedTo, int $assignedBy): bool {
        $stmt = $this->conn->prepare("
            INSERT INTO tasks (title, description, assigned_by, assigned_to, status) 
            VALUES (?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("ssii", $title, $description, $assignedBy, $assignedTo);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to assign task: " . $stmt->error);
        }
        
        return true;
    }

    /**
     * Updates task status
     * @param int $taskId ID of the task to update
     * @param string $status New status ('pending', 'in_progress', or 'completed')
     * @return bool True on success, false on failure
     * @throws InvalidArgumentException If invalid status is provided
     * @throws RuntimeException If database operation fails
     */
    public function updateTaskStatus(int $taskId, string $status): bool {
        $validStatuses = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException(
                "Invalid status value. Must be one of: " . implode(', ', $validStatuses)
            );
        }

        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $taskId);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to update task status: " . $stmt->error);
        }
        
        return true;
    }

    /**
     * Deletes a task (only if it was assigned by this team leader)
     * @param int $taskId ID of the task to delete
     * @param int $teamLeaderId ID of the team leader attempting deletion
     * @return bool True on success, false if task doesn't exist or wasn't assigned by this leader
     * @throws RuntimeException If database operation fails
     */
    public function deleteTask(int $taskId, int $teamLeaderId): bool {
        // Verify the task exists and was assigned by this team leader
        $stmt = $this->conn->prepare("SELECT id FROM tasks WHERE id = ? AND assigned_by = ?");
        $stmt->bind_param("ii", $taskId, $teamLeaderId);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to verify task ownership: " . $stmt->error);
        }
        
        if ($stmt->get_result()->num_rows === 0) {
            return false;
        }
    
        // Delete the task
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $taskId);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to delete task: " . $stmt->error);
        }
        
        return true;
    }
}