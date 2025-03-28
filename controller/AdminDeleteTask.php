<?php
session_start();
include '../Config/Config.php';
include '../models/AdminDeleteTaskModel.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'team_leader') {
    header('location:../index.php');
    exit();
}

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    
    $taskModel = new AdminDeleteTaskModel($conn);
    $result = $taskModel->deleteTask($task_id);
    
    if ($result) {
        header('location: ../controller/admin-dashboard.php?success=task_deleted');
        exit();
    } else {
        die("Error deleting task");
    }
}
?>