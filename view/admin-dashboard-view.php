<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg  ">
        <div class="container">
        <h2>Admin Dashboard</h2>
            <div>
                <a href="manage-employees.php" class="btn btn-info me-2">Manage Employees</a>
                <a href="edit-profile.php" class="btn btn-info me-2">Edit Profile</a>
                <button class="btn btn-danger" id="logoutButton">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <div class="container mb-5">
            <h3>
            <img src="<?php echo $_SESSION['photo'] ?>" alt="profile photo" class="rounded-circle" width="100">
                Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (admin)
            </h3>
        </div>

        <div class="row g-4">
            <!-- Assign Task Section -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Assign Task</h4>
                        <form action="admin-dashboard.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Task Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Task Description</label>
                                <textarea class="form-control" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Assign To</label>
                                <select class="form-select" name="assigned_to" required>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>">
                                            <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['role']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Assign Task</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add Employee Section -->
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center">Add New Employee</h4>
                    <form id="registerEmployeeForm" action="register-employee.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" id="name">
                            <small class="text-danger" id="nameError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email">
                            <small class="text-danger" id="emailError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="phone">
                            <small class="text-danger" id="phoneError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" class="form-control" name="qualification" id="qualification">
                            <small class="text-danger" id="qualificationError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                            <small class="text-danger" id="passwordError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-control" name="role">
                                <option value="employee">Employee</option>
                                <option value="team_leader">Team Leader</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Photo</label>
                            <input type="file" class="form-control" name="photo" id="photo" accept="image/*">
                            <small class="text-danger" id="photoError"></small>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Register Employee</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- All Assigned Tasks Section -->
        <div class="mt-5">
            <h4 class="text-center mb-4">All Assigned Tasks</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Update Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['title']); ?></td>
                                <td><?php echo htmlspecialchars($task['description']); ?></td>
                                <td><?php echo htmlspecialchars($task['assigned_to']); ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-<?php 
                                        echo ($task['status'] == 'completed') ? 'success' : 
                                             (($task['status'] == 'in_progress') ? 'warning' : 'secondary'); 
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="update-task-progress-admin.php" method="POST" class="d-flex">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <select name="status" class="form-select me-2">
                                            <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="in_progress" <?php echo ($task['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-success">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="update-task-admin.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="admin-dashboard.php?delete_task=<?php echo $task['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this task?');">
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
    </div>

    <script src="../Assets/JS/login_validation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Logout confirmation
            document.getElementById("logoutButton").addEventListener("click", function(e) {
                e.preventDefault();
                if (confirm("Are you sure you want to logout?")) {
                    window.location.href = "../logout.php";
                }
            });

        });
    </script>
</body>
</html>