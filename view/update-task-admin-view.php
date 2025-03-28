<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Task (Admin)</title>
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Task (Admin)</h2>
        
        
        <form action="UpdateTaskAdmin.php" method="POST">
            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task_id); ?>">

            <div class="mb-3">
                <label class="form-label">Task Title</label>
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($task['title'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Task Description</label>
                <textarea class="form-control" name="description" required><?php echo htmlspecialchars($task['description'] ?? ''); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Assign To</label>
                <select name="assigned_to" class="form-control" required>
                    <?php foreach ($users as $user) { ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo ($user['id'] == ($task['assigned_to'] ?? '')) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Task</button>
            <?php 
                $dashboard = ($_SESSION['role'] === 'admin') ? 'admin-dashboard.php' : 'team-leader-dashboard.php';
            ?>
            <a href="<?php echo $dashboard; ?>" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</body>
</html>