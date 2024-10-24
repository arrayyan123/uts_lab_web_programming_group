<?php
// dashboard.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once __DIR__ . '/../../controllers/task_controller.php';
include_once __DIR__ . '/../../models/user.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$userId = $_SESSION['user_id'];
$groups = getAllGroups($userId);
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
    <nav class="bg-gray-200 shadow shadow-gray-300 w-100 px-8 md:px-auto">
        <div class="md:h-16 h-28 mx-auto md:px-4 container flex items-center justify-between flex-wrap md:flex-nowrap">
            <!-- Logo -->
            <div class="text-indigo-500 md:order-1">
                <!-- Heroicon - Chip Outline -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
            </div>
            <div class="text-gray-500 order-3 w-full md:w-auto md:order-2">
                <ul class="flex font-semibold justify-between">
                    <li class="md:px-4 md:py-2 text-indigo-500"><a href="dashboard.php">Dashboard</a></li>
                    <li class="md:px-4 md:py-2 hover:text-indigo-400"><a href="home.php">Home</a></li>
                    <li class="md:px-4 md:py-2 hover:text-indigo-400"><a href="user/edit_profile.php">Edit Profile</a></li>
                    <li class="md:px-4 md:py-2 hover:text-indigo-400"><a href="user/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Dashboard</h2>
        <a href="create_task.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-md mb-4">Create Task</a>

        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Groups</h3>
            <ul class="space-y-2">
                <?php foreach ($groups as $group): ?>
                    <?php 
                        $tasksInGroup = getTasks(null, $group['id']);
                        $allCompleted = array_reduce($tasksInGroup, function($carry, $task) {
                        return $carry && $task['is_completed'];
                    }, true);
                    ?>
                    <li class="bg-white shadow-md rounded-md p-4">
                        <div class="flex justify-between lg:items-center p-4 mb-2 md:flex-row flex-col <?= $allCompleted ? 'bg-green-100' : 'bg-red-100'; ?>">
                            <div class="flex items-center">
                                <input type="checkbox" id="group-<?= $group['id'] ?>" <?= $allCompleted ? 'checked' : '' ?> disabled>
                                <label for="group-<?= $group['id'] ?>" class="ml-2 font-bold"><?= htmlspecialchars($group['title']) ?></label>
                            </div>
                            <div class="flex justify-center space-x-2">
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
                                    <li class="flex justify-between  md:flex-row flex-col items-center <?= $task['is_completed'] ? 'bg-green-100' : 'bg-red-100'; ?> p-2 rounded-md">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="tasks[]" value="<?= $task['id'] ?>" id="task-<?= $task['id'] ?>" <?= $task['is_completed'] ? 'checked' : '' ?> disabled>
                                            <div class="flex flex-col">
                                                <label for="task-<?= $task['id'] ?>" class="ml-2 cursor-pointer font-bold"><?= htmlspecialchars($task['title']) ?></label>
                                                <p class="ml-2"><?= htmlspecialchars($task['description'])?></p>
                                            </div>
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
