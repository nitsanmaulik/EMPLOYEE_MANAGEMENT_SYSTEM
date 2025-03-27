<?php
session_start();
require_once __DIR__ . '/../Config/Config.php';
require_once __DIR__ . '/../models/teamLeader-dashboard-model.php';

class TeamLeaderController {
    private $model;
    private $teamLeaderId;

    public function __construct($model) {
        $this->model = $model;
        $this->checkAuthentication();
        $this->teamLeaderId = $_SESSION['user_id'];
    }

    private function checkAuthentication() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'team_leader') {
            header('Location: ../index.php');
            exit();
        }
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
        }
        $this->displayDashboard();
    }

    private function handlePostRequest() {
        if (isset($_POST['task_id']) && isset($_POST['status'])) {
            $this->updateTaskStatus();
        } elseif (isset($_POST['title']) && isset($_POST['assigned_to'])) {
            $this->assignNewTask();
        } elseif (isset($_POST['delete_task'])) {  
            $this->deleteTask();
        }
    }

    private function deleteTask() {
        try {
            $taskId = filter_input(INPUT_POST, 'delete_task', FILTER_VALIDATE_INT);
            if (!$taskId) {
                throw new Exception("Invalid task ID");
            }
    
            $success = $this->model->deleteTask($taskId, $this->teamLeaderId);
            $_SESSION['message'] = $success ? "Task deleted successfully!" : "Failed to delete task";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header("Location: team-leader-dashboard.php");
        exit();
    }

    private function updateTaskStatus() {
        try {
            $taskId = filter_input(INPUT_POST, 'task_id', FILTER_VALIDATE_INT);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

            if (!$taskId || !$status) {
                throw new Exception("Invalid input data");
            }

            $success = $this->model->updateTaskStatus($taskId, $status);
            $_SESSION['message'] = $success ? "Task status updated successfully!" : "Failed to update task status";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header("Location: team-leader-dashboard.php");
        exit();
    }

    private function assignNewTask() {
        try {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $assignedTo = (int)$_POST['assigned_to'];

            if (empty($title) || empty($description)) {
                throw new Exception("Title and description are required");
            }

            $success = $this->model->assignTask($title, $description, $assignedTo, $this->teamLeaderId);
            $_SESSION['message'] = $success ? "Task assigned successfully!" : "Failed to assign task";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header("Location: team-leader-dashboard.php");
        exit();
    }

    private function displayDashboard() {
        $teamLeader = $this->model->getTeamLeaderDetails($this->teamLeaderId);
        $teamMembers = $this->model->getTeamMembers();
        $assignedTasks = $this->model->getAssignedTasks($this->teamLeaderId);
        $myTasks = $this->model->getMyTasks($this->teamLeaderId);

        $data = [
            'teamLeader' => $teamLeader,
            'teamMembers' => $teamMembers,
            'assignedTasks' => $assignedTasks,
            'myTasks' => $myTasks,
            'message' => $_SESSION['message'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];

        unset($_SESSION['message'], $_SESSION['error']);
        require_once __DIR__ . '/../view/teamLeader-dashboard-view.php';
    }
}

// Instantiate and run the controller
$model = new TeamLeaderModel($conn);
$controller = new TeamLeaderController($model);
$controller->handleRequest();

$conn->close();
?>