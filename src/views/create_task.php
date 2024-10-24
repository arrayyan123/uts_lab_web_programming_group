<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require '../../models/db.php';
require __DIR__ . '/../../models/user.php';

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

    $userId = $_SESSION['user_id']; 
    $assigned_by = 1; 

    if (!empty($group_name)) {
        $stmt = $conn->prepare("INSERT INTO group_tasks (user_id, title) VALUES (?, ?)");
        $stmt->execute([$userId, $group_name]);
        $group_id = $conn->lastInsertId(); // Get the last inserted group ID
    }

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
        header("Location: dashboard.php");
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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="max-w-lg mx-auto bg-white p-8 shadow-lg rounded-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Create Task</h2>
            <form action="" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="group_id">Select Group:</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300" name="group_id">
                        <option value="">Select a group</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="group_name">Or Create New Group:</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300" type="text" name="group_name">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="task_name">Task Name:</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300" type="text" name="task_name" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="task_description">Task Description:</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300"  name="task_description" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="deadline">Deadline:</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300" type="datetime-local" name="deadline" required>
                </div>
                <button class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition duration-300" type="submit">Create Task</button>
            </form>
        </div>
    </div>
</body>
</html>
