<?php
session_start();
include_once '../../controllers/task_controller.php';

if (!isset($_GET['task_id'])) {
    header("Location: dashboard.php");
    exit();
}

$taskId = $_GET['task_id'];
$userId = $_SESSION['user_id'] ?? null;

if ($userId === null) {
    header("Location: user/login.php");
    exit();
}

if (deleteTaskAndRearrange($taskId, $userId)) {
    header("Location: dashboard.php?message=Task deleted successfully");
} else {
    header("Location: dashboard.php?message=Failed to delete task.");
}
?>
