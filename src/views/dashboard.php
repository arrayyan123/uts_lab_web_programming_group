<?php
// dashboard.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once '../../controllers/task_controller.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$groups = getAllGroups();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function logErrorToConsole(message) {
            console.error(message);
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Dashboard</h2>
        <a href="create_task.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-md mb-4">Create Task</a>

        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Groups</h3>
            <ul class="space-y-2">
                <?php foreach ($groups as $group): ?>
                    <?php 
                    $tasksInGroup = getTasks(null, $group['id']); // Mengambil semua tugas berdasarkan group_task_id
                    $allCompleted = array_reduce($tasksInGroup, function($carry, $task) {
                        return $carry && $task['is_completed'];
                    }, true);
                    ?>
                    <li class="bg-white shadow-md rounded-md p-4">
                        <div class="flex justify-between items-center mb-2 <?= $allCompleted ? 'bg-green-100' : 'bg-red-100'; ?>">
                            <div class="flex items-center">
                                <input type="checkbox" id="group-<?= $group['id'] ?>" <?= $allCompleted ? 'checked' : '' ?>>
                                <label for="group-<?= $group['id'] ?>" class="ml-2"><?= htmlspecialchars($group['title']) ?></label>
                            </div>
                            <div class="flex space-x-2">
                                <form action="complete_group.php" method="POST" class="inline">
                                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                    <button type="submit" name="complete" value="<?= $allCompleted ? '0' : '1' ?>" class="bg-green-500 text-white px-3 py-1 rounded-md">
                                        <?= $allCompleted ? 'Mark as Incomplete' : 'Complete All Tasks' ?>
                                    </button>
                                </form>
                                <form action="delete_group.php" method="POST" class="inline">
                                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md">
                                        Delete Group
                                    </button>
                                </form>
                            </div>
                        </div>
                        <ul class="space-y-2">
                            <?php if (empty($tasksInGroup)): ?>
                                <li>No tasks found for this group.</li>
                                <script>
                                    logErrorToConsole("No tasks found for user_id: <?= $userId ?> and group_id: <?= $group['id'] ?>");
                                </script>
                            <?php else: ?>
                                <?php foreach ($tasksInGroup as $task): ?>
                                    <li class="flex justify-between items-center <?= $task['is_completed'] ? 'bg-green-100' : 'bg-red-100'; ?> p-2 rounded-md">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="tasks[]" value="<?= $task['id'] ?>" id="task-<?= $task['id'] ?>" <?= $task['is_completed'] ? 'checked' : '' ?>>
                                            <label for="task-<?= $task['id'] ?>" class="ml-2 cursor-pointer"><?= htmlspecialchars($task['title']) ?></label>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="edit_task.php?task_id=<?= $task['id'] ?>" class="bg-yellow-500 text-white px-3 py-1 rounded-md">Edit</a>
                                            <a href="delete_task.php?task_id=<?= $task['id'] ?>" class="bg-red-500 text-white px-3 py-1 rounded-md">Delete</a>
                                            <a href="complete_task.php?task_id=<?= $task['id'] ?>&complete=<?= $task['is_completed'] ? '0' : '1' ?>" class="bg-green-500 text-white px-3 py-1 rounded-md">
                                                <?= $task['is_completed'] ? 'Mark as Incomplete' : 'Complete' ?>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <a href="home.php" class="inline-block mt-6 bg-gray-500 text-white px-4 py-2 rounded-md">Back to Home</a>
    </div>
</body>
</html>
