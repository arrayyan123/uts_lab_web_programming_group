<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once '../../models/user.php';
include_once '../../models/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}
$user = getUserById($_SESSION['user_id']);
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: user/login.php");
    exit();
}
$database = new Database();
$conn = $database->getConnection();

$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/output.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Home</title>
</head>
<body>
    <!--navbar yang baru ehe-->
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
    <!-- Welcome Message -->
    <h2>Welcome, <?= htmlspecialchars($user['username']) ?></h2>
    <p>Email: <?= htmlspecialchars($user['email']) ?></p>

    <!-- Task List -->
    <div class="task-list">
        <h3>Your Tasks</h3>
        <?php if (empty($tasks)): ?>
            <p>You have no tasks yet.</p>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
                <div class="task-item">
                    <p><strong>Title:</strong> <?= htmlspecialchars($task['title']); ?></p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($task['description']); ?></p>
                    <p class="task-status">
                        <strong>Status:</strong> <?= $task['is_completed'] ? 'Completed' : 'Incomplete'; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>
