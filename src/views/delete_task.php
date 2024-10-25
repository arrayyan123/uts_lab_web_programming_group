<?php
session_start();
include_once __DIR__ . '/../../controllers/task_controller.php';

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

echo $userId. ' - ' . $taskId;
if (deleteTaskAndRearrange($taskId, $userId)) {
    echo $userId. ' 2 ' . $taskId;
    header("Location: dashboard.php?message=Task deleted successfully");
} else { 
    header("Location: dashboard.php?message=Failed_to_delete_task_$taskId");
}
?>
