<?php
// complete_task.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '/Applications/XAMPP/xamppfiles/htdocs/uts_lab_web_programming_group/controllers/task_controller.php';

if (!isset($_GET['task_id']) || !isset($_GET['complete'])) {
    header("Location: home.php");
    exit();
}

$taskId = $_GET['task_id'];
$is_completed = $_GET['complete'] === '1';

if (updateTask($taskId, null, null, $is_completed)) {
    header("Location: home.php");
} else {
    echo "Failed to update task.";
}
?>
