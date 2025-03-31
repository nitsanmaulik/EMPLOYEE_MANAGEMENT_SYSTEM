<?php
session_start();

include '../Config/Config.php';

// Check if the user is logged in and is an employee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: ../index.php');
    exit();
}

if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}

$employeeId = $_SESSION['user_id'];
$employeeName = $_SESSION['name'];

// Fetch tasks assigned to this employee
$tasks = $conn->query("SELECT * FROM tasks WHERE assigned_to = $employeeId");
include ("../view/Employee/Dashboard.php");
?>

