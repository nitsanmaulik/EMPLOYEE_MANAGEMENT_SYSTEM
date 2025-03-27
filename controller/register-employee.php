<?php
session_start();
require_once __DIR__ . '/../Config/Config.php';
require_once __DIR__ . '/../models/register-employee-model.php';

class RegisterEmployeeController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->handleRegistration();
        }
    }

    private function handleRegistration() {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("Unauthorized access");
            }

            $this->model->registerEmployee($_POST, $_FILES['photo'] ?? null);
            $_SESSION['success_message'] = "Employee registered successfully!";
            header('Location: admin-dashboard.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: admin-dashboard.php'); // Redirect back to form
            exit();
        }
    }
}

// Main execution
try {
    $model = new RegisterEmployeeModel($conn);
    $controller = new RegisterEmployeeController($model);
    $controller->handleRequest();
} catch (Exception $e) {
    die("System error: " . $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>