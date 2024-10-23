<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '../../controllers/task_controller.php';

if (isset($_POST['group_id'])) {
    $groupId = $_POST['group_id'];
    $tasks = getTasks(null, $groupId);
    $allCompleted = true;
    foreach ($tasks as $task) {
        if (!$task['is_completed']) {
            $allCompleted = false;
            break;
        }
    }
    if ($allCompleted) {
        foreach ($tasks as $task) {
            if (!updateTask($task['id'], false, null, null, null, null)) {
                echo "Failed to mark task {$task['id']} as incomplete.";
            }
        }
        header("Location: dashboard.php?message=All tasks in the group marked as incomplete successfully");
        exit();
    } else {
        foreach ($tasks as $task) {
            if (!updateTask($task['id'], true, null, null, null, null)) {
                echo "Failed to mark task {$task['id']} as complete.";
            }
        }
        header("Location: dashboard.php?message=All tasks in the group completed successfully");
        exit();
    }
} else {
    echo "Group ID is missing.";
}
?>
