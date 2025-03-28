<?php
session_start();
require '../Config/Config.php';
require '../models/update-task-admin-model.php';

class TaskAdminController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new TaskAdminModel($conn);
    }
    
    public function handleRequest() {
        $this->checkAdminAuth();
        $task_id = $this->validateTaskId();
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->processUpdate($task_id);
        }
        
        $data = [
            'task' => $this->model->getTask($task_id),
            'users' => $this->model->getAssignableUsers($_SESSION['role']),
            'task_id' => $task_id
        ];
        
        $this->displayView($data);
    }
    
    private function checkAdminAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../index.php");
            exit();
        }
        
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'team_leader') {
            header("Location: ../index.php");
            exit();
        }
    }
    
    private function validateTaskId() {
        $task_id = $_GET['id'] ?? $_POST['task_id'] ?? null;
        if (!$task_id || !ctype_digit($task_id)) {
            die("Invalid task ID");
        }
        return (int)$task_id;
    }
    
    private function processUpdate($task_id) {
        try {
            // Validate POST data
            if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['assigned_to'])) {
                throw new Exception("All fields are required");
            }
            
            $this->model->updateTask(
                $task_id,
                trim($_POST['title']),
                trim($_POST['description']),
                (int)$_POST['assigned_to']
            );
            
            $dashboard = ($_SESSION['role'] === 'admin') ? 'admin-dashboard.php' : 'team-leader-dashboard.php';
            header("Location: $dashboard?success=Task+Updated");
            exit();
        } catch (Exception $e) {
            header("Location: update-task-admin.php?id=$task_id&error=" . urlencode($e->getMessage()));
            exit();
        }
    }
    
    private function displayView($data) {
        extract($data);
        include '../view/update-task-admin-view.php';
    }
}

$controller = new TaskAdminController($conn);
$controller->handleRequest();
$conn->close();
?>