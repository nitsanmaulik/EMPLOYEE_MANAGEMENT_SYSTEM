
<?php
session_start();
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../models/manage_employee_model.php';

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$model = new EmployeeModel($conn);

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_employee'])) {
        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'qualification' => $_POST['qualification'],
            'role' => $_POST['role']
        ];
        
        $success = $model->updateEmployee($id, $data);
        $_SESSION['message'] = $success ? 'Employee updated successfully!' : 'Error updating employee.';
        header("Location: manage_employees.php");
        exit();
    }
}

if (isset($_GET['delete'])) {
    $success = $model->deleteEmployee($_GET['delete']);
    $_SESSION['message'] = $success ? 'Employee deleted successfully!' : 'Error deleting employee.';
    header("Location: manage_employees.php");
    exit();
}

// Get all employees for the view
$employees = $model->getAllEmployees();

// Include view
//require_once __DIR__ . '../view/manage_employee_view.php';
include '../view/manage_employees_view.php';
?>





<!-- <?php
session_start();
require '../Config/config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

class EmployeeManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllEmployees() {
        return $this->conn->query("
            SELECT id, name, email, phone, qualification, role, photo 
            FROM users 
            WHERE role IN ('employee', 'team_leader')
            ORDER BY name ASC
        ");
    }
}


$employeeManager = new EmployeeManager($conn);
$employees = $employeeManager->getAllEmployees();


include '../view/manage_employees_view.php';
?> -->
