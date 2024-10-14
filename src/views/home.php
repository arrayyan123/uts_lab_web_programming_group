<?php
// home.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once '/Applications/XAMPP/xamppfiles/htdocs/uts_lab_web_programming_group/models/user.php';
include_once '/Applications/XAMPP/xamppfiles/htdocs/uts_lab_web_programming_group/models/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}
$user = getUserById($_SESSION['user_id']);
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: /Applications/XAMPP/xamppfiles/htdocs/uts_lab_web_programming_group/src/views/user/login.php");
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
    <title>Home</title>
    <style>
        /* Basic styling for the navbar */
        .navbar {
            background-color: #f0f0f0;
            padding: 10px;
        }
        .navbar a {
            margin: 0 15px;
            text-decoration: none;
            color: #333;
        }
        .navbar a:hover {
            text-decoration: underline;
        }

        /* Styling for the task list */
        .task-list {
            margin-top: 20px;
        }
        .task-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .task-status {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="home.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="edit_profile.php">Edit Profile</a>
        <a href="user/logout.php">Logout</a>
    </div>

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
