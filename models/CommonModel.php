<?php

class CommonModel {
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
     * Retrieves basic details of an employee
     * @param int $employeeId The ID of the employee
     * @return array{name: string, photo: string|null}|null Associative array with employee details or null if not found
     * @throws RuntimeException If database operation fails
     */
    public function getEmployeeDetails(int $employeeId): ?array {
        $stmt = $this->conn->prepare("SELECT name, photo FROM users WHERE id = ?");
        $stmt->bind_param("i", $employeeId);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to fetch employee details: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
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


}
?>