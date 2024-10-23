<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '../../controllers/task_controller.php';

if (!isset($_GET['complete'])) {
    header("Location: dashboard.php");
    exit();
}

$is_completed = $_GET['complete'] === '1';

if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];
    if (empty($taskId)) {
        header("Location: dashboard.php?message=Error: taskId cannot be null.");
        exit();
    }
    if (updateTask($taskId, $is_completed)) {
        header("Location: dashboard.php?message=Task updated successfully");
        exit();
    } else {
        header("Location: dashboard.php?message=Failed to update task.");
        exit();
    }
} 
?>
