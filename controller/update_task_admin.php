<?php
session_start();
require '../Config/config.php';

class TaskAdminController {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function handleRequest() {
        $this->checkAdminAuth();
        $task_id = $this->validateTaskId();
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->processUpdate($task_id);
        }
        
        $data = [
            'task' => $this->getTask($task_id),
            'users' => $this->getAssignableUsers(),
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
            
            $this->updateTask(
                $task_id,
                trim($_POST['title']),
                trim($_POST['description']),
                (int)$_POST['assigned_to']
            );
            
            $dashboard = ($_SESSION['role'] === 'admin') ? 'admindashboard.php' : 'teamLeaderdashboard.php';
            header("Location: $dashboard?success=Task+Updated");
            exit();
        } catch (Exception $e) {
            header("Location: update_task_admin.php?id=$task_id&error=" . urlencode($e->getMessage()));
            exit();
        }
    }
    
    private function updateTask($task_id, $title, $description, $assigned_to) {
        // Verify user exists
        $user_check = $this->conn->prepare("SELECT id FROM users WHERE id=?");
        $user_check->bind_param("i", $assigned_to);
        $user_check->execute();
        
        if ($user_check->get_result()->num_rows === 0) {
            throw new Exception("Selected user does not exist");
        }
        
        $stmt = $this->conn->prepare("UPDATE tasks SET title=?, description=?, assigned_to=? WHERE id=?");
        $stmt->bind_param("ssii", $title, $description, $assigned_to, $task_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
    }
    
    private function getTask($task_id) {
        $stmt = $this->conn->prepare("SELECT title, description, assigned_to FROM tasks WHERE id=?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
        
        if (!$task) {
            throw new Exception("Task not found");
        }
        
        return $task;
    }
    
    private function getAssignableUsers() {
        
        if ($_SESSION['role'] === 'team_leader') {
            $stmt = $this->conn->prepare(
                "SELECT id, name FROM users WHERE role = 'employee'"
            );
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        
        $result = $this->conn->query(
            "SELECT id, name FROM users WHERE role IN ('employee', 'team_leader')"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    private function displayView($data) {
        extract($data);
        include '../view/update_task_admin_view.php';
    }
}

$controller = new TaskAdminController($conn);
$controller->handleRequest();
$conn->close();
?>