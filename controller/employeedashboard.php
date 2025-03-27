<?php
session_start();

include '../Config/config.php';

// Check if the user is logged in and is an employee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: ../index.php');
    exit();
}

$employeeId = $_SESSION['user_id'];
$employeeName = $_SESSION['name'];

// Fetch tasks assigned to this employee
$tasks = $conn->query("SELECT * FROM tasks WHERE assigned_to = $employeeId");
include ("../view/employee_dashboard_view.php");
?>

