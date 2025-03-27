<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Edit Employee</h2>
        <form action="manage-employees.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
            
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($employee['phone']); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Qualification</label>
                <input type="text" class="form-control" name="qualification" value="<?php echo htmlspecialchars($employee['qualification']); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select class="form-select" name="role" required>
                    <option value="employee" <?php echo $employee['role'] === 'employee' ? 'selected' : ''; ?>>Employee</option>
                    <option value="team_leader" <?php echo $employee['role'] === 'team_leader' ? 'selected' : ''; ?>>Team Leader</option>
                </select>
            </div>
            
            <button type="submit" name="update_employee" class="btn btn-primary w-100">Update Employee</button>
            <a href="manage-employees.php" class="btn btn-secondary w-100 mt-3">Cancel</a>
        </form>
    </div>
</body>
</html>