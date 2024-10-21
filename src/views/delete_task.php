<?php
// delete_task.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../../controllers/task_controller.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php"); 
    exit();
}

if (!isset($_GET['task_id'])) {
    header("Location: home.php"); 
    exit();
}

$taskId = $_GET['task_id'];
$userId = $_SESSION['user_id'];

deleteTaskAndRearrange($taskId, $userId);

header("Location: home.php");
exit();
?>
