<?php
/**
 * Model class for handling user profile editing operations
 */
class EditProfileModel {
    /** @var mysqli Database connection object */
    private $conn;

    /**
     * Constructor - initializes the database connection
     * @param mysqli $conn MySQLi database connection object
     */
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable MySQLi exceptions
    }

    /**
     * Retrieves user data by ID
     * @param int $user_id The ID of the user to fetch
     * @return array<string, string|null>|null Associative array of user data or null if not found
     */
    public function getUserById(int $user_id): ?array {
        try {
            $stmt = $this->conn->prepare("SELECT name, email, phone, qualification, photo FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            return $user ?: null;
        } catch (Exception $e) {
            error_log("Error fetching user data: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Updates user profile, with or without changing the password
     * @param int $user_id User ID to update
     * @param string $name User's name
     * @param string $email User's email
     * @param string $phone User's phone number
     * @param string $qualification User's qualification
     * @param string|null $photo Path to user's photo (nullable)
     * @param string|null $password New password to set (nullable)
     * @return bool True on success, false on failure
     */
    public function updateUser(
        int $user_id,
        string $name,
        string $email,
        string $phone,
        string $qualification,
        ?string $photo,
        ?string $password
    ): bool {
        try {
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Invalid email format.");
            }

            // Validate phone number (must be 10 digits)
            if (!preg_match('/^\d{10}$/', $phone)) {
                throw new InvalidArgumentException("Phone number must be exactly 10 digits.");
            }

            if ($password !== null) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, qualification = ?, photo = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssssssi", $name, $email, $phone, $qualification, $photo, $hashed_password, $user_id);
            } else {
                $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, qualification = ?, photo = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $name, $email, $phone, $qualification, $photo, $user_id);
            }

            $stmt->execute();
            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("Error updating user profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Closes the database connection
     */
    public function closeConnection(): void {
        $this->conn->close();
    }
}
?>
