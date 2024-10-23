<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../../models/db.php';

date_default_timezone_set('Asia/Jakarta');

$database = new Database();
$conn = $database->getConnection();

// Fetch existing groups
$stmt = $conn->prepare("SELECT * FROM group_tasks");
$stmt->execute();
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = $_POST['group_name'];
    $group_id = $_POST['group_id'];
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $deadline = $_POST['deadline'];

    $userId = 1; 
    $assigned_by = 1; 

    // Create new group if provided
    if (!empty($group_name)) {
        // Insert new group with user_id
        $stmt = $conn->prepare("INSERT INTO group_tasks (user_id, title) VALUES (?, ?)");
        $stmt->execute([$userId, $group_name]);
        $group_id = $conn->lastInsertId(); // Get the last inserted group ID
    }

    // Insert the task
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, group_task_id, title, description, deadline, assigned_by) 
          VALUES (:user_id, :group_task_id, :title, :description, :deadline, :assigned_by)");

    $params = [
        ':user_id' => $userId,
        ':group_task_id' => $group_id, // Use group_task_id here
        ':title' => $task_name,
        ':description' => $task_description,
        ':deadline' => $deadline,
        ':assigned_by' => $assigned_by
    ];

    if ($stmt->execute($params)) {
        echo "Task created successfully.";
        header("Location: task_list.php");
        exit();
    } else {
        echo "Failed to create the task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
</head>
<body>
    <h2>Create Task</h2>
    <form action="" method="post">
        <label for="group_id">Select Group:</label>
        <select name="group_id">
            <option value="">Select a group</option>
            <?php foreach ($groups as $group): ?>
                <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['title']) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="group_name">Or Create New Group:</label>
        <input type="text" name="group_name">
        <br><br>
        <label for="task_name">Task Name:</label>
        <input type="text" name="task_name" required>
        <br><br>
        <label for="task_description">Task Description:</label>
        <textarea name="task_description" required></textarea>
        <br><br>
        <label for="deadline">Deadline:</label>
        <input type="datetime-local" name="deadline" required>
        <br><br>
        <button type="submit">Create Task</button>
    </form>
</body>
</html>
