<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <h2>Manage Employees</h2>
            <a href="admin-dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="text-center">Employee List</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Qualification</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td>
                                <img src="../<?php echo !empty($employee['photo']) ? htmlspecialchars($employee['photo']) : 'Assets/Images/default.png'; ?>" 
                                     class="rounded-circle" width="50" height="50">
                            </td>
                            <td><?php echo htmlspecialchars($employee['name']); ?></td>
                            <td><?php echo htmlspecialchars($employee['email']); ?></td>
                            <td><?php echo htmlspecialchars($employee['phone']); ?></td>
                            <td><?php echo htmlspecialchars($employee['qualification']); ?></td>
                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $employee['role']))); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="edit_employee.php?id=<?php echo $employee['id']; ?>" 
                                       class="btn btn-warning btn-sm">Edit</a>
                                    <a href="manage_employees.php?delete=<?php echo $employee['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this employee?');">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>