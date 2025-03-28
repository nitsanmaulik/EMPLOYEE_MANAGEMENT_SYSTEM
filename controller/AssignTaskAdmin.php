<?php
session_start();
require_once __DIR__ . '/../Config/Config.php';
require_once __DIR__ . '/../models/AssignTaskAdminModel.php';

class TaskController {
    private $model;
    private $assignedBy;
    private $role;

    public function __construct ($model, $userId, $userRole) {
        $this->model = $model;
        $this->assignedBy = $userId;
        $this->role = $userRole;
    }

    public function handleRequest () {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->handlePostRequest();
        }
    }

    private function handlePostRequest () {
        try {
            $this->model->assignTask(
                trim($_POST['title']),
                trim($_POST['description']),
                $this->assignedBy,
                intval($_POST['assigned_to'])
            );
            
            $_SESSION['message'] = 'Task assigned successfully!';
            $this->redirect();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect();
        }
    }

    private function redirect () {
        $url = $this->role === 'admin' ? "admin-dashboard.php" : "team-leader-dashboard.php";
        header("Location: $url");
        exit();
    }
}

// Authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}


$taskModel = new AssignTaskAdminModel($conn);
$controller = new TaskController($taskModel, $_SESSION['user_id'], $_SESSION['role']);
$controller->handleRequest();


$conn->close();
?>