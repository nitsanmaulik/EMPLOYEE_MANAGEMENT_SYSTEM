<?php
session_start();
include("../Config/Config.php");
include("../models/admindashboard-model.php");

// Redirect if admin is not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Initialize model with database connection
$model = new AdminDashboardModel($conn);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to'])) {
        // Assign new task
        $success = $model->assignTask( $_POST['title'],$_POST['description'],$_POST['assigned_to'],
            $_SESSION['user_id']
        );
        $_SESSION['message'] = $success ? 'Task assigned successfully!' : 'Error assigning task.';
    } elseif (isset($_POST['task_id']) && isset($_POST['status'])) {
        // Update task status
        $success = $model->updateTaskStatus($_POST['task_id'], $_POST['status']);
        $_SESSION['message'] = $success ? 'Task status updated!' : 'Error updating task.';
    }
    
    header("Location: admin-dashboard.php");
    exit();
}


if (isset($_GET['delete_task'])) {
    $success = $model->deleteTask($_GET['delete_task']);
    $_SESSION['message'] = $success ? 'Task deleted successfully!' : 'Error deleting task.';
    header("Location: admindashboard.php");
    exit();
}


$users = $model->getAllUsers();
$tasks = $model->getAllTasks();


include "../view/admin-dashboard-view.php";
?>