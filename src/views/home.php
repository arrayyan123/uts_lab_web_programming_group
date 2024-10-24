<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once '../../models/user.php';
include_once '../../models/db.php';
include_once __DIR__ . '/../../controllers/task_controller.php';

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

$groups = getAllGroups([$_SESSION['user_id']]);

function generate_light_hex_color() {
    do {
        $color = substr(md5(rand()), 0, 6);
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
    } while ($r < 0xF || $g < 0xF || $b < 0xF);

    return '#' . $color;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/output.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Home</title>
    <style>
        .grid-overlay {
            background-image: linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                            linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
        }
    </style>
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
     <div class="relative bg-[url('../assets/umn_build.jpg')] bg-cover aspect-video w-full bg-no-repeat flex flex-col justify-center">
        <div class="absolute inset-0 bg-black/50 grid-overlay z-0"></div>
        <div class="relative z-10 text-white text-center">
        <h1 class="text-6xl md:text-8xl font-bold mb-4">Welcome, <?= htmlspecialchars($user['username']) ?><span class="blink">|</span></h1>
        <p class="text-sm max-w-md mx-auto mb-8">Email: <?= htmlspecialchars($user['email']) ?></p>
        </div>
     </div>

    <!-- Task List -->
    <div class="w-full">
        <div class="px-6 py-4">
            <h2 class="text-lg font-semibold flex items-center"><i class="fa fa-tasks mr-2"></i> Task Lists</h2>
            <p class="text-sm">Pengingat ampuh untuk mengatur dan mengorganisir kehidupan Anda. Dengan fitur "Task Lists", Anda dapat dengan mudah melacak semua tugas yang perlu diselesaikan, menghindari lupa, dan memastikan tidak ada tugas penting yang terlewat.</p>
        </div>
        <div class="w-100 flex items-center md:items-start flex-col md:flex-row gap-5">
            <?php foreach ($groups as $group): ?>
                <ul class="divide-y mx-5 max-h-96 w-96 overflow-y-auto rounded-lg shadow-lg">
                    <?php
                        $tasksInGroup = getTasks(null, $group['id']); // Mengambil semua tugas berdasarkan group_task_id
                        $allCompleted = array_reduce($tasksInGroup, function ($carry, $task) {
                            return $carry && $task['is_completed'];
                        }, true);
                    ?>
                    <?php $hex_color = generate_light_hex_color(); ?>
                    <div class="font-semibold rounded-t-xl p-2" style="background-color: <?= htmlspecialchars($hex_color) ?>;"><?= htmlspecialchars($group['title']) ?></div>
                    <?php foreach ($tasksInGroup as $task): ?>
                        <li class="px-6 py-4 flex items-center">
                            <div class="flex-grow">
                                <div class="font-semibold"><?= htmlspecialchars($task['title']); ?><span class="ml-2 text-<?= $task['is_completed'] == 1 ? 'blue' : 'red'; ?>-600"><?= $task['is_completed'] ? 'Completed' : 'Incomplete'; ?></span></div>
                                <div class="text-sm text-gray-500"><i><?= htmlspecialchars($task['description']); ?></i></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        const blink = document.querySelector('.blink');
        setInterval(() => {
            blink.style.visibility = (blink.style.visibility === 'hidden') ? 'visible' : 'hidden';
        }, 500);
    </script>
</body>

</html>