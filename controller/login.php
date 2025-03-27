<?php
session_start(); // Add this at the very top

//require_once __DIR__ . '/../models/login_model.php';
include("../models/login_model.php");

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function authenticate() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            $user = $this->userModel->authenticate($email, $password);

            if ($user) {
                $this->setSession($user);
                $this->redirectUser($user['role']);
                exit();
            } else {
                header("Location: ../index.php?error=invalid_credentials");
                exit();
            }
        }
    }

    private function setSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['photo'] = $user['photo'];
    }

    private function redirectUser($role) {
        switch ($role) {
            case 'admin':
                header("Location: admindashboard.php");
                break;
            case 'team_leader':
                header("Location: teamLeaderdashboard.php");
                break;
            case 'employee':
                header("Location: employeedashboard.php");
                break;
            default:
                header("Location: ../index.php?error=invalid_role");
                break;
        }
    }
}

// Instantiate and call the controller
$authController = new AuthController();
$authController->authenticate();
?>