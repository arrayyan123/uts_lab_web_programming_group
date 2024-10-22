<?php
// edit.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once '../../controllers/task_controller.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['task_id'])) {
    header("Location: login.php");
    exit();
}

$taskId = $_GET['task_id'];
$task = getTasks($_SESSION['user_id'])[0]; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    if (updateTask($taskId, $title, $description, $is_completed)) {
        header("Location: home.php");
        exit();
    } else {
        $message = "Failed to update task.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="max-w-lg mx-auto bg-white p-8 shadow-lg rounded-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Edit Task</h2>

            <!-- Error message -->
            <?php if (isset($message)) echo "<p class='text-red-500 text-center mb-4'>$message</p>"; ?>

            <!-- Edit Task Form -->
            <form method="POST">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-semibold mb-2">Title:</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-semibold mb-2">Description:</label>
                    <textarea id="description" name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-300"><?= htmlspecialchars($task['description']) ?></textarea>
                </div>

                <div class="mb-6">
                    <label for="is_completed" class="inline-flex items-center">
                        <input type="checkbox" id="is_completed" name="is_completed" <?= $task['is_completed'] ? 'checked' : '' ?> class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <span class="ml-2 text-gray-700 font-semibold">Completed</span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <input type="submit" value="Update" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition duration-300">
                    <a href="home.php" class="text-indigo-600 hover:underline">Back to Home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
