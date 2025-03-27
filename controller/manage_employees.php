
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


$employees = $model->getAllEmployees();


include '../view/manage_employees_view.php';
?>

