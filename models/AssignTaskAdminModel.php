<?php
/**
 * Model class for handling task assignment operations in the admin context
 */
class AssignTaskAdminModel {
    /** @var mysqli Database connection object */
    private $conn;

    /**
     * Constructor - initializes the database connection
     * @param mysqli $connection MySQLi database connection object
     */
    public function __construct(mysqli $connection) {
        $this->conn = $connection;
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable MySQLi exceptions
    }

    /**
     * Assigns a new task to a user
     * @param string $title Task title
     * @param string $description Task description
     * @param int $assignedBy ID of the admin assigning the task
     * @param int $assignedTo ID of the user receiving the task
     * @return bool True on successful assignment, false on failure
     */
    public function assignTask(string $title, string $description, int $assignedBy, int $assignedTo): bool {
        try {
            // Validate input
            if (empty($title) || empty($description) || empty($assignedTo)) {
                throw new InvalidArgumentException("All fields are required.");
            }

            // Verify user exists
            if (!$this->userExists($assignedTo)) {
                throw new RuntimeException("Invalid employee selected.");
            }

            // Insert task
            $stmt = $this->conn->prepare("INSERT INTO tasks (title, description, assigned_by, assigned_to, status) 
                                        VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("ssii", $title, $description, $assignedBy, $assignedTo);
            $stmt->execute();
            $stmt->close();

            return true;
        } catch (Exception $e) {
            error_log("Error assigning task: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks if a user exists in the database
     * @param int $userId User ID to verify
     * @return bool True if user exists, false otherwise
     */
    private function userExists(int $userId): bool {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $exists = (bool)$stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $exists;
        } catch (Exception $e) {
            error_log("Error checking user existence: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves all users who can be assigned tasks
     * @return array<array{id: int, name: string, role: string}> Array of assignable users
     */
    public function getAssignableUsers(): array {
        try {
            $query = "SELECT id, name, role FROM users WHERE role IN ('employee', 'team_leader') ORDER BY name ASC";
            $result = $this->conn->query($query);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error retrieving assignable users: " . $e->getMessage());
            return [];
        }
    }
}
?>
