<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../../models/db.php';

date_default_timezone_set('Asia/Jakarta');

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        echo "Task not found.";
        exit;
    }
    $stmt = $conn->prepare("SELECT * FROM group_tasks WHERE user_id = ?");
    $stmt->execute([$task['user_id']]); // Hanya ambil grup yang dimiliki oleh user ini
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Task ID is missing.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group_id'];
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $deadline = $_POST['deadline'];

    // Update query
    $stmt = $conn->prepare("UPDATE tasks SET group_task_id = ?, title = ?, description = ?, deadline = ? WHERE id = ?");
    if ($stmt->execute([$group_id, $task_name, $task_description, $deadline, $task_id])) {
        header("Location: task_list.php?message=Task updated successfully");
        exit();
    } else {
        echo "Failed to update the task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
</head>
<body>
    <h2>Edit Task</h2>
    <form action="" method="post">
        <label for="group_id">Select Group:</label>
        <select name="group_id" required>
            <?php foreach ($groups as $group): ?>
                <option value="<?= $group['id'] ?>" <?= $task['group_task_id'] == $group['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($group['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="task_name">Task Name:</label>
        <input type="text" name="task_name" value="<?= htmlspecialchars($task['title']) ?>" required>
        <br><br>
        <label for="task_description">Task Description:</label>
        <textarea name="task_description" required><?= htmlspecialchars($task['description']) ?></textarea>
        <br><br>
        <label for="deadline">Deadline:</label>
        <input type="datetime-local" name="deadline" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($task['deadline']))) ?>" required>
        <br><br>
        <button type="submit">Update Task</button>
    </form>
    <a href="task_list.php">Back to Task List</a>
</body>
</html>
