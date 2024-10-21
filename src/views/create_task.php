<?php
// create_task.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once '../../controllers/task_controller.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assigned_by = $_POST['assigned_by'];

    if (createTask($_SESSION['user_id'], $title, $description, $assigned_by)) {
        header("Location: home.php");
        exit();
    } else {
        $message = "Failed to create task.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Task</title>
</head>
<body>
    <h2>Create Task</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" required><br>
        <label>Description:</label>
        <textarea name="description"></textarea><br>
        <label>Assigned By:</label>
        <input type="text" name="assigned_by"><br>
        <input type="submit" value="Create">
    </form>
    <a href="home.php">Back to Home</a>
</body>
</html>
