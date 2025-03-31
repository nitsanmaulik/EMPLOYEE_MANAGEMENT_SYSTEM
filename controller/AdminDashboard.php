<?php
session_start();
include("../Config/Config.php");
include("../models/AdminDashboardModel.php");

// Ensure only Admins can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$model = new AdminDashboardModel($conn);

// Handle Task Assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title'], $_POST['description'], $_POST['assigned_to'])) {
        $success = $model->assignTask($_POST['title'], $_POST['description'], $_POST['assigned_to'], $_SESSION['user_id']);
        $_SESSION['message'] = $success ? 'Task assigned successfully!' : 'Error assigning task.';
    } 
    // Handle Task Status Update
    elseif (isset($_POST['task_id'], $_POST['status'])) {
        $success = $model->updateTaskStatus($_POST['task_id'], $_POST['status']);
        $_SESSION['message'] = $success ? 'Task status updated!' : 'Error updating task.';
    }
    
    header("Location: AdminDashboard.php");
    exit();
}

// Handle Task Deletion
if (isset($_GET['delete_task'])) {
    $success = $model->deleteTask($_GET['delete_task']);
    $_SESSION['message'] = $success ? 'Task deleted successfully!' : 'Error deleting task.';
    header("Location: AdminDashboard.php");
    exit();
}

// Fetch Users and Tasks
$users = $model->getAllUsers();
$tasks = $model->getAllTasks();

include "../view/Admin/Dashboard.php.php";
?>
