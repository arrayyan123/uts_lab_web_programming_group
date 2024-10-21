<?php
// edit.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once '../../controllers/task_controller.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['task_id'])) {
    header("Location: login.php");
    exit();
}

$taskId = $_GET['task_id'];
$task = getTasks($_SESSION['user_id'])[0]; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    if (updateTask($taskId, $title, $description, $is_completed)) {
        header("Location: home.php");
        exit();
    } else {
        $message = "Failed to update task.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
</head>
<body>
    <h2>Edit Task</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required><br>
        <label>Description:</label>
        <textarea name="description"><?= htmlspecialchars($task['description']) ?></textarea><br>
        <label>Completed:</label>
        <input type="checkbox" name="is_completed" <?= $task['is_completed'] ? 'checked' : '' ?>><br>
        <input type="submit" value="Update">
    </form>
    <a href="home.php">Back to Home</a>
</body>
</html>
