<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../../models/db.php';

date_default_timezone_set('Asia/Jakarta');

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        echo "Task not found.";
        exit;
    }
    $stmt = $conn->prepare("SELECT * FROM group_tasks WHERE user_id = ?");
    $stmt->execute([$task['user_id']]); // Hanya ambil grup yang dimiliki oleh user ini
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Task ID is missing.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group_id'];
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $deadline = $_POST['deadline'];

    // Update query
    $stmt = $conn->prepare("UPDATE tasks SET group_task_id = ?, title = ?, description = ?, deadline = ? WHERE id = ?");
    if ($stmt->execute([$group_id, $task_name, $task_description, $deadline, $task_id])) {
        header("Location: task_list.php?message=Task updated successfully");
        exit();
    } else {
        echo "Failed to update the task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="max-w-lg mx-auto bg-white p-8 shadow-lg rounded-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Edit Task</h2>
            <form action="" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="group_id">Select Group:</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300" name="group_id" required>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?= $group['id'] ?>" <?= $task['group_task_id'] == $group['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($group['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="task_name">Task Name:</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300" type="text" name="task_name" value="<?= htmlspecialchars($task['title']) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="task_description">Task Description:</label>
                    <textarea name="task_description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300" required><?= htmlspecialchars($task['description']) ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="deadline">Deadline:</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300" type="datetime-local" name="deadline" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($task['deadline']))) ?>" required>
                </div>
                <br><br>
                <button class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition duration-300" type="submit">Update Task</button>
            </form>
            <button class="bg-indigo-600 mt-4 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition duration-300">
                <a href="dashboard.php">Back to Task List</a>
            </button>
        </div>
    </div>
</body>
</html>
