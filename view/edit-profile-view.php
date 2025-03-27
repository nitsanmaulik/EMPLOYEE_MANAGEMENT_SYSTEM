<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../Assets/CSS/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Profile</h2>
        <form action="edit-profile.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                <small class="text-danger" id="nameError"></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                <small class="text-danger" id="emailError"></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                <small class="text-danger" id="phoneError"></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Qualification</label>
                <input type="text" class="form-control" name="qualification" value="<?php echo htmlspecialchars($user['qualification']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Profile Photo</label>
                <input type="file" class="form-control" name="photo">
                
            </div>
            <div class="mb-3">
                <label class="form-label">New Password (Optional)</label>
                <input type="password" class="form-control" name="password" placeholder="Enter new password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Profile</button>
            <a href="<?php 
                if ($role === 'team_leader') {
                    echo 'team-leader-dashboard.php';
                } elseif ($role === 'admin') {
                    echo 'admin-dashboard.php';
                } else {
                    echo 'employee-dashboard.php';
                }
            ?>" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
    <script src="../Assets/JS/validation.js"></script>
</body>
</html>
