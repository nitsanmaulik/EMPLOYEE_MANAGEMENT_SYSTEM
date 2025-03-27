
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

// Get employee data
$employee = null;
if (isset($_GET['id'])) {
    $employee = $model->getEmployeeById($_GET['id']);
    if (!$employee) {
        $_SESSION['message'] = 'Employee not found!';
        header("Location: manage_employees.php");
        exit();
    }
}

// Include view
require_once __DIR__ . '/../view/edit_employee_view.php';
?>