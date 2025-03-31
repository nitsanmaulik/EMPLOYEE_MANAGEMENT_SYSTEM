<?php
session_start();
require_once __DIR__ . '/../Config/Config.php';
require_once __DIR__ . '/../models/TeamLeaderDashboardModel.php';
require_once __DIR__ . '/../models/CommonModel.php';

/**
 * Controller for handling Team Leader dashboard operations
 */
class TeamLeaderController {
    /** @var TeamLeaderDashboardModel $model The team leader specific model */
    private $model;
    
    /** @var CommonModel $commonModel The common model for shared functionality */
    private $commonModel;
    
    /** @var int $teamLeaderId The current team leader's ID */
    private $teamLeaderId;

    /**
     * Constructor
     * 
     * @param TeamLeaderDashboardModel $model Team leader specific model
     * @param CommonModel $commonModel Common functionality model
     */
    public function __construct(TeamLeaderDashboardModel $model, CommonModel $commonModel) {
        $this->model = $model;
        $this->commonModel = $commonModel;
        $this->checkAuthentication();
        $this->teamLeaderId = (int)$_SESSION['user_id'];
    }

    /**
     * Checks if user is authenticated as team leader
     * 
     * @return void
     * @throws Exception If not authenticated
     */
    private function checkAuthentication(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'team_leader') {
            header('Location: ../index.php');
            exit();
        }
    }

    /**
     * Main request handler
     * 
     * @return void
     */
    public function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
        }
        $this->displayDashboard();
    }

    /**
     * Handles POST requests
     * 
     * @return void
     */
    private function handlePostRequest(): void {
        if (isset($_POST['task_id']) && isset($_POST['status'])) {
            $this->updateTaskStatus();
        } elseif (isset($_POST['title']) && isset($_POST['assigned_to'])) {
            $this->assignNewTask();
        } elseif (isset($_POST['delete_task'])) {  
            $this->deleteTask();
        }
    }

    /**
     * Deletes a task
     * 
     * @return void
     */
    private function deleteTask(): void {
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
        header("Location: TeamLeaderDashboard.php");
        exit();
    }

    /**
     * Updates task status
     * 
     * @return void
     */
    private function updateTaskStatus(): void {
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
        header("Location: TeamLeaderDashboard.php");
        exit();
    }

    /**
     * Assigns a new task to a team member
     * 
     * @return void
     */
    private function assignNewTask(): void {
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
        header("Location: TeamLeaderDashboard.php");
        exit();
    }

    /**
     * Displays the dashboard view
     * 
     * @return void
     */
    private function displayDashboard(): void {
        $teamLeader = $this->commonModel->getTeamLeaderDetails($this->teamLeaderId);
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
        require_once __DIR__ . '/../view/TeamLeader/Dashboard.php';
    }
}

// Instantiate and run the controller
try {
    $model = new TeamLeaderDashboardModel($conn);
    $commonModel = new CommonModel($conn);
    $controller = new TeamLeaderController($model, $commonModel);
    $controller->handleRequest();
} catch (Exception $e) {
    // Log error and show user-friendly message
    error_log("TeamLeaderDashboard error: " . $e->getMessage());
    $_SESSION['error'] = "An unexpected error occurred. Please try again later.";
    header("Location: TeamLeaderDashboard.php");
    exit();
} finally {
    $conn->close();
}