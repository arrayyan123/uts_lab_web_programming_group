<?php
// dashboard.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once '/Applications/XAMPP/xamppfiles/htdocs/uts_lab_web_programming_group/controllers/task_controller.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$tasks = getTasks($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Dashboard</h2>
    <a href="create_task.php">Create Task</a>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <?= htmlspecialchars($task['title']) ?>
                <a href="edit.php?task_id=<?= $task['id'] ?>">Edit</a>
                <a href="delete_task.php?task_id=<?= $task['id'] ?>">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="home.php">Back to Home</a>
</body>
</html>
