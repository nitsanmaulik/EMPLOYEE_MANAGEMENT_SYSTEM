<?php
/**
 * User class for handling user authentication and related operations
 */
class User {
    /** @var mysqli Database connection object */
    private $conn;

    /**
     * Constructor - initializes the database connection
     * @throws RuntimeException If database connection is not available
     */
    public function __construct() {
        global $conn;
        
        if (!isset($conn) || !($conn instanceof mysqli)) {
            throw new RuntimeException("Database connection not available");
        }
        
        $this->conn = $conn;
    }

    /**
     * Authenticates a user with email and password
     * @param string $email User's email address
     * @param string $password User's plain text password
     * @return array{id: int, name: string, password: string, role: string, photo: string|null}|false 
     *         Returns user data array on success, false on failure
     * @throws RuntimeException If database operation fails
     */
    public function authenticate(string $email, string $password) {
        $stmt = $this->conn->prepare("SELECT id, name, password, role, photo FROM users WHERE email = ?");
        
        if (!$stmt) {
            throw new RuntimeException("Database prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("s", $email);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Authentication query failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        
        return false;
    }
}