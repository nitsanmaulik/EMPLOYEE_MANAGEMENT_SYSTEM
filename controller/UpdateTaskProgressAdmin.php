<?php
session_start();
require '../Config/Config.php';
require '../models/UpdateTaskProgressAdmin.php';

class TaskProgressController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new UpdateTaskProgressAdmin($conn);
    }
    
    public function handleRequest() {
        $this->checkAuthentication();
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method");
            }

            $this->validatePostData();
            
            $taskId = $_POST['task_id'];
            $status = $_POST['status'];
            
            $this->model->updateTaskStatus($taskId, $status);
            
            $this->redirectWithSuccess();
            
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    private function checkAuthentication() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
            header("Location: ../index.php");
            exit();
        }
    }
    
    private function validatePostData() {
        if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
            throw new Exception("Missing task details");
        }
    }
    
    private function redirectWithSuccess() {
        header("Location: admin-dashboard.php?success=Task+Updated");
        exit();
    }
    
    private function handleError(Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

$controller = new TaskProgressController($conn);
$controller->handleRequest();
?>