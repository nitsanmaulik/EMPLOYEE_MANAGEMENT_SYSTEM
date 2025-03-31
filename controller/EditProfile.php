<?php
session_start();
require_once '../Config/Config.php';
require_once '../models/EditProfileModel.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$userModel = new EditProfileModel($conn);
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

    
    $success = $userModel->updateUser($user_id, $name, $email, $phone, $qualification, $photo, !empty($new_password) ? $new_password : null);


    if ($success) {
        $_SESSION['name'] = $name;
        $_SESSION['photo'] = $photo;
        
        
        $redirect_page = "dashboard.php";
        switch ($role) {
            case 'team_leader':
                $redirect_page = "TeamLeaderDashboard.php";
                break;
            case 'admin':
                $redirect_page = "AdminDashboard.php";
                break;
            case 'employee':
                $redirect_page = "EmployeeDashboard.php";
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