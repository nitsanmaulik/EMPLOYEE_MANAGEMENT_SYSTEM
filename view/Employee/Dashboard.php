<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container d-flex justify-content-between align-items-center">
            <h2>Employee dashboard</h2>
            <div>
                <a href="EditProfile.php" class="btn btn-warning">Edit Profile</a>
                <button class="btn btn-danger" id="logoutButton">Logout</button>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="container">
            <h2><?php if (!empty($_SESSION['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['photo']); ?>" alt="profile photo" class="rounded-circle" width="100">
                <?php endif; ?>
            Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
        </div>
        <h4 class="mt-4 text-center">Your Assigned Tasks</h4>
        
        <?php if ($tasks->num_rows === 0): ?>
            <div class="alert alert-info text-center">
                There are no tasks assigned to you.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($task = $tasks->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['title']); ?></td>
                                <td><?php echo htmlspecialchars($task['description']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($task['status'] == 'completed') ? 'success' : (($task['status'] == 'in_progress') ? 'warning' : 'secondary'); ?>">
                                        <?php echo ucwords(str_replace('_', ' ', htmlspecialchars($task['status']))); ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="UpdateTaskProgress.php" method="POST">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <select name="status" class="form-select" <?php echo ($task['status'] == 'completed') ? 'disabled' : ''; ?>>
                                            <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="in_progress" <?php echo ($task['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm mt-2" <?php echo ($task['status'] == 'completed') ? 'disabled' : ''; ?>>
                                            Update Progress
                                        </button>
                                        <?php if ($task['status'] == 'completed'): ?>
                                            <div class="text-success mt-1">Task completed!</div>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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