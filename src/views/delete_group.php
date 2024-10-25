<?php
require '../../models/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_id'])) {
    $group_id = $_POST['group_id'];

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("DELETE FROM tasks WHERE group_task_id = :group_id");
    $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM group_tasks WHERE id = :group_id");
    $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>
