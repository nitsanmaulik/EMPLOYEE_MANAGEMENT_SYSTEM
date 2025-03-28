<?php
session_start();
require_once '../Config/Config.php';
require_once '../models/edit-profile-model.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$userModel = new UserModel($conn);
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$user = $userModel->getUserById($user_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $qualification = trim($_POST['qualification']);
    $photo = $user['photo']; 
    $new_password = trim($_POST['password']);

    
    if (!empty($_FILES['photo']['name'])) {
        $upload_dir = "../Assets/Images/";
        $photo_name = time() . "_" . basename($_FILES['photo']['name']);
        $photo_path = $upload_dir . $photo_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            $photo = $photo_path;
        
            if (!empty($user['photo']) && strpos($user['photo'], 'default_profile.jpg') === false) {
                @unlink($user['photo']);
            }
        }
    }

    
    if (!empty($new_password)) {
        $success = $userModel->updateUserWithPassword($user_id, $name, $email, $phone, $qualification, $photo, $new_password);
    } else {
        $success = $userModel->updateUserWithoutPassword($user_id, $name, $email, $phone, $qualification, $photo);
    }

    if ($success) {
        $_SESSION['name'] = $name;
        $_SESSION['photo'] = $photo;
        
        
        $redirect_page = "dashboard.php";
        switch ($role) {
            case 'team_leader':
                $redirect_page = "team-leader-dashboard.php";
                break;
            case 'admin':
                $redirect_page = "admin-dashboard.php";
                break;
            case 'employee':
                $redirect_page = "employee-dashboard.php";
                break;
        }
        
        header("Location: $redirect_page");
        exit();
    } else {
        $error = "Error updating profile";
    }
}

$userModel->closeConnection();
require_once '../view/edit-profile-view.php';
?>