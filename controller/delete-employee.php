<?php
session_start();
require '../Config/Config.php';

class EmployeeDeleter {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function delete($employee_id) {
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
if ($deleter->delete($_GET['id'])) {
    header("Location: manage-employees.php?success=Employee+Deleted");
} else {
    die("Failed to delete employee");
}
exit();
?>