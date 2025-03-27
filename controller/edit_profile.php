<?php
session_start();
include '../Config/config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, phone, qualification, photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $qualification = trim($_POST['qualification']);
    $photo = $user['photo']; // Keep existing photo by default
    $new_password = trim($_POST['password']);

    // Handle File Upload
    if (!empty($_FILES['photo']['name'])) {
        $upload_dir = "../Assets/Images/";
        $photo_name = time() . "_" . basename($_FILES['photo']['name']);
        $photo_path = $upload_dir . $photo_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            $photo = $photo_path;
            // Delete old photo if it exists and isn't the default
            if (!empty($user['photo']) && strpos($user['photo'], 'default_profile.jpg') === false) {
                @unlink($user['photo']);
            }
        } else {
            echo "Error uploading file.";
        }
    }

    // Update Query (With Password Update if Provided)
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, qualification = ?, photo = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $name, $email, $phone, $qualification, $photo, $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, qualification = ?, photo = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $qualification, $photo, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['name'] = $name;
        $_SESSION['photo'] = $photo;
        
        // Redirect based on role
        $redirect_page = "dashboard.php"; // Default dashboard
        if ($role === 'team_leader') {
            $redirect_page = "teamLeaderdashboard.php";
        } elseif ($role === 'admin') {
            $redirect_page = "admindashboard.php";
        } elseif ($role === 'employee') {
            $redirect_page = "employeedashboard.php";
        }
        
        header("Location: $redirect_page");
        exit();
    } else {
        echo "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
include '../view/edit_profile_view.php';
?>

