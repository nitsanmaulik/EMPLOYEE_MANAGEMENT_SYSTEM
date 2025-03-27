<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Leader Dashboard</title>
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container d-flex justify-content-between align-items-center">
            <h2>Team Leader Dashboard</h2>
            <div>
                <a href="edit_profile.php" class="btn btn-warning me-2">Edit Profile</a>
                <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#myTasksModal">
                    My Tasks
                </button>
                <button class="btn btn-danger" id="logoutButton">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="container mb-3">
            <h2><img src="../<?php echo $_SESSION['photo'] ?>" alt="profile photo" class="rounded-circle" width="100">
            Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
        </div>
        <div class="row g-4">
            <!-- Assign Task Form -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Assign Task</h4>
                        <form action="teamLeaderdashboard.php" method="POST">
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
                                    <?php foreach ($teamMembers as $member): ?>
                                        <option value="<?php echo $member['id']; ?>">
                                            <?php echo htmlspecialchars($member['name']); ?> (<?php echo htmlspecialchars($member['email']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Assign Task</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Assigned Tasks Table -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Assigned Employee Tasks</h4>
                        
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Assigned To</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assignedTasks as $task): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($task['title']); ?></td>
                                                <td><?php echo htmlspecialchars($task['description']); ?></td>
                                                <td><?php echo htmlspecialchars($task['employee_name']); ?></td>
                                                <td>
                                                    <span class="badge rounded-pill bg-<?php 
                                                        echo ($task['status'] == 'completed') ? 'success' : 
                                                             (($task['status'] == 'in_progress') ? 'warning' : 'secondary'); 
                                                    ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="update_task_admin.php?id=<?php echo $task['id']; ?>" 
                                                           class="btn btn-warning btn-sm">Edit</a>
                                                        <form action="teamLeaderdashboard.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="delete_task" value="<?php echo $task['id']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('Are you sure you want to delete this task?');">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Tasks Modal -->
    <div class="modal fade" id="myTasksModal" tabindex="-1" aria-labelledby="myTasksLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myTasksLabel">My Assigned Tasks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Update Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myTasks as $task): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                                        <td><?php echo htmlspecialchars($task['description']); ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-<?php 
                                                echo ($task['status'] == 'completed') ? 'success' : 
                                                     (($task['status'] == 'in_progress') ? 'warning' : 'secondary'); 
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form action="teamLeaderdashboard.php" method="POST">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <div class="d-flex gap-2">
                                                    <select name="status" class="form-select">
                                                        <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="in_progress" <?php echo ($task['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                                        <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-success btn-sm">Update</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const logoutButton = document.getElementById("logoutButton");
        if (logoutButton) {
            logoutButton.addEventListener("click", function(e) {
                e.preventDefault();
                if (confirm("Are you sure you want to logout?")) {
                    window.location.href = "../logout.php";
                }
            });
        }
    });
    </script>
</body>
</html>