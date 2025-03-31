<?php
session_start();
require '../Config/Config.php';

class EmployeeDeleter {
    private $conn;
    
    /**
     * Constructor for EmployeeDeleter class
     * 
     * @param mysqli $conn Database connection object
     */
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    
    /**
     * Deletes an employee from the database
     * 
     * @param int $employee_id ID of the employee to delete
     * @return bool Returns true if deletion was successful, false otherwise
     */
    public function delete(int $employee_id): bool {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $employee_id);
        return $stmt->execute();
    }
}

// Authentication Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Input Validation
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid employee ID");
}

// Process Deletion
$deleter = new EmployeeDeleter($conn);
if ($deleter->delete((int)$_GET['id'])) {
    header("Location: ManageEmployees.php?success=Employee+Deleted");
} else {
    die("Failed to delete employee");
}
exit();
?>