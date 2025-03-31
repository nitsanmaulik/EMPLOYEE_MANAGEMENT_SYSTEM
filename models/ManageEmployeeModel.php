<?php
/**
 * Model class for managing employee data and operations
 */
class ManageEmployeeModel {
    /** @var mysqli Database connection object */
    private $conn;

    /**
     * Constructor - initializes the database connection
     * @param mysqli $connection MySQLi database connection object
     * @throws InvalidArgumentException If connection is invalid
     */
    public function __construct(mysqli $connection) {
        if (!$connection instanceof mysqli) {
            throw new InvalidArgumentException("Invalid database connection provided");
        }
        $this->conn = $connection;
    }

    /**
     * Retrieves all employees and team leaders
     * @return array<array{
     *     id: int,
     *     name: string,
     *     email: string,
     *     phone: string,
     *     qualification: string,
     *     role: string,
     *     photo: string|null
     * }> Array of employee data
     * @throws RuntimeException If database operation fails
     */
    public function getAllEmployees(): array {
        $query = "SELECT id, name, email, phone, qualification, role, photo 
                 FROM users 
                 WHERE role IN ('employee', 'team_leader')
                 ORDER BY name ASC";
        $result = $this->conn->query($query);
        
        if ($result === false) {
            throw new RuntimeException("Failed to fetch employees: " . $this->conn->error);
        }
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Retrieves a single employee by ID
     * @param int $id Employee ID
     * @return array<string, mixed>|null Employee data or null if not found
     * @throws RuntimeException If database operation fails
     */
    public function getEmployeeById(int $id): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        if (!$stmt) {
            throw new RuntimeException("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    /**
     * Updates employee information
     * @param int $id Employee ID
     * @param array{
     *     name: string,
     *     email: string,
     *     phone: string,
     *     qualification: string,
     *     role: string
     * } $data Employee data to update
     * @return bool True on success, false on failure
     * @throws RuntimeException If database operation fails
     */
    public function updateEmployee(int $id, array $data): bool {
        $requiredKeys = ['name', 'email', 'phone', 'qualification', 'role'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException("Missing required field: $key");
            }
        }

        $stmt = $this->conn->prepare("UPDATE users SET 
                                    name = ?, email = ?, phone = ?, qualification = ?, role = ? 
                                    WHERE id = ?");
        if (!$stmt) {
            throw new RuntimeException("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param(
            "sssssi", 
            $data['name'],
            $data['email'], 
            $data['phone'],
            $data['qualification'],
            $data['role'], 
            $id
        );
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Update failed: " . $stmt->error);
        }
        
        return true;
    }

    /**
     * Deletes an employee
     * @param int $id Employee ID to delete
     * @return bool True on success, false on failure
     * @throws RuntimeException If database operation fails
     */
    public function deleteEmployee(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        if (!$stmt) {
            throw new RuntimeException("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Delete failed: " . $stmt->error);
        }
        
        return true;
    }
}