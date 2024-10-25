<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once '../../models/user.php';
include_once '../../models/db.php';
include_once __DIR__ . '/../../controllers/task_controller.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}
$user = getUserById($_SESSION['user_id']);
$userId = $_SESSION['user_id'];
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: user/login.php");
    exit();
}

$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
$groups = getAllGroups($userId);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$results = [];

if (!empty($search)) {
    $query = "SELECT group_tasks.title AS group_title, tasks.title, tasks.description, tasks.is_completed
        FROM group_tasks 
        INNER JOIN tasks ON group_tasks.id = tasks.group_task_id 
        WHERE (group_tasks.title LIKE :search OR tasks.title LIKE :search)
    ";
    if ($filterStatus === 'completed') {
        $query .= " AND tasks.is_completed = 1";
    } elseif ($filterStatus === 'incomplete') {
        $query .= " AND tasks.is_completed = 0";
    }

    $stmt = $db->prepare($query);
    $searchTerm = "%" . $search . "%";
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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
                    <li class="md:px-4 md:py-2 hover:text-indigo-500"><a href="dashboard.php">Dashboard</a></li>
                    <li class="md:px-4 md:py-2 hover:text-indigo-400"><a href="home.php">Home</a></li>
                    <li class="md:px-4 md:py-2 hover:text-indigo-400"><a href="user/edit_profile.php">Edit Profile</a></li>
                    <li class="md:px-4 md:py-2 hover:text-indigo-400"><a href="user/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Welcome Message -->
     <div class="relative bg-[url('../assets/umn_build.jpg')] bg-cover aspect-video h-screen w-full bg-no-repeat flex flex-col justify-center">
        <div class="absolute inset-0 bg-black/50 grid-overlay z-0"></div>
        <div class="relative z-10 text-white text-center">
        <h1 class="text-6xl md:text-8xl font-bold mb-4">Welcome, <?= htmlspecialchars($user['username']) ?><span class="blink">|</span></h1>
        <p class="text-sm max-w-md mx-auto mb-8">Email: <?= htmlspecialchars($user['email']) ?></p>
        </div>
     </div>

    <!-- Task List -->
    <div class="w-full h-screen">
        <div class="px-6 py-4">
            <h2 class="text-lg font-semibold flex items-center"><i class="fa fa-tasks mr-2"></i> Task Lists</h2>
            <p class="text-sm">Pengingat ampuh untuk mengatur dan mengorganisir kehidupan Anda. Dengan fitur "Task Lists", Anda dapat dengan mudah melacak semua tugas yang perlu diselesaikan, menghindari lupa, dan memastikan tidak ada tugas penting yang terlewat.</p>
        </div>
        <form method="GET" action="home.php" class="flex justify-center flex-row text-black gap-2 items-center m-5 lg:w-auto w-50">
            <input type="text" name="search" placeholder="Search Task..." class="border px-4 py-2 rounded-lg w-full sm:w-1/2 lg:w-2/3" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <select name="status" class="border px-4 py-2 rounded-lg">
                <option value="all" <?= $filterStatus === 'all' ? 'selected' : '' ?>>All</option>
                <option value="completed" <?= $filterStatus === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="incomplete" <?= $filterStatus === 'incomplete' ? 'selected' : '' ?>>Incomplete</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Search</button>
        </form>
        <!--Card Group Task-->
        <div class="w-100 flex justify-center items-center md:items-start flex-wrap gap-5">
            <?php if (!empty($search) && !empty($results)): ?>
                <?php 
                $groupedResults = [];
                foreach ($results as $result) {
                    $groupedResults[$result['group_title']][] = $result;
                }
                ?>
                <?php foreach ($groupedResults as $groupTitle => $tasks): ?>
                    <ul class="divide-y mx-5 max-h-96 w-96 overflow-y-auto rounded-lg shadow-lg">
                        <?php $hex_color = generate_light_hex_color(); ?>
                        <div class="font-semibold rounded-t-xl p-2" style="background-color: <?= htmlspecialchars($hex_color) ?>;">
                            <?= htmlspecialchars($groupTitle) ?>
                        </div>
                        <?php foreach ($tasks as $task): ?>
                            <li class="px-6 py-4 flex items-center">
                                <div class="flex-grow">
                                    <div class="font-semibold">
                                        <?= htmlspecialchars($task['title']); ?>
                                        <span class="ml-2 text-<?= $task['is_completed'] == 1 ? 'blue' : 'red'; ?>-600">
                                            <?= $task['is_completed'] ? 'Completed' : 'Incomplete'; ?>
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500"><i><?= htmlspecialchars($task['description']); ?></i></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Menampilkan Semua Group Task dan Task -->
                <?php foreach ($groups as $group): ?>
                    <ul class="divide-y mx-5 max-h-96 min-h-[300px] w-96 overflow-y-auto rounded-lg shadow-lg">
                        <?php
                            $tasksInGroup = getTasks(null, $group['id']);
                            if ($filterStatus === 'completed') {
                                $tasksInGroup = array_filter($tasksInGroup, fn($task) => $task['is_completed'] == 1);
                            } elseif ($filterStatus === 'incomplete') {
                                $tasksInGroup = array_filter($tasksInGroup, fn($task) => $task['is_completed'] == 0);
                            }
                        ?>
                        <?php $hex_color = generate_light_hex_color(); ?>
                        <div class="font-semibold rounded-t-xl p-2" style="background-color: <?= htmlspecialchars($hex_color) ?>;">
                            <?= htmlspecialchars($group['title']) ?>
                        </div>
                        <?php foreach ($tasksInGroup as $task): ?>
                            <li class="px-6 py-4 flex items-center">
                                <div class="flex-grow">
                                    <div class="font-semibold">
                                        <?= htmlspecialchars($task['title']); ?>
                                        <span class="ml-2 text-<?= $task['is_completed'] == 1 ? 'blue' : 'red'; ?>-600">
                                            <?= $task['is_completed'] ? 'Completed' : 'Incomplete'; ?>
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500"><i><?= htmlspecialchars($task['description']); ?></i></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <section class="text-gray-600 body-font">
            <div class="container px-5 py-24 mx-auto">
                <div class="flex flex-wrap w-full mb-20">
                <div class="lg:w-1/2 w-full mb-6 lg:mb-0">
                    <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900">Our Team Member</h1>
                    <div class="h-1 w-20 bg-indigo-500 rounded"></div>
                </div>
                <p class="lg:w-1/2 w-full leading-relaxed text-gray-500">Kami dari kelompok 3 Web Programming membuat sebuah aplikasi yang inovatif, user friendly, dan memiliki keunggulan fitur lainnya</p>
                </div>
                <div class="flex flex-wrap -m-4">
                    <div class="xl:w-1/4 md:w-1/2 p-4">
                        <div class="bg-gray-100 h-full p-6 rounded-lg">
                            <img class="h-40 rounded w-full object-cover object-center mb-6" src="../../assets/arrayyan.JPG" alt="content">
                            <h3 class="tracking-widest text-indigo-500 text-xs font-medium title-font">Full Stack</h3>
                            <h2 class="text-lg text-gray-900 font-medium title-font mb-4">Muhammad Arrayyan Aprilyanto</h2>
                            <p class="leading-relaxed text-base">Seorang pengembang full stack memiliki kemampuan untuk mengerjakan semua lapisan aplikasi, baik sisi front end (antarmuka pengguna) maupun back end (server, basis data, dan logika aplikasi). Mereka dapat mengembangkan aplikasi secara menyeluruh dari awal hingga akhir.</p>
                        </div>
                    </div>
                    <div class="xl:w-1/4 md:w-1/2 p-4">
                        <div class="bg-gray-100 h-full p-6 rounded-lg">
                            <img class="h-40 rounded w-full object-cover object-center mb-6" src="../../assets/savero.JPG" alt="content">
                            <h3 class="tracking-widest text-indigo-500 text-xs font-medium title-font">Semi-Full Stack</h3>
                            <h2 class="text-lg text-gray-900 font-medium title-font mb-4">Savero Madajaya</h2>
                            <p class="leading-relaxed text-base">Istilah ini biasanya merujuk pada pengembang yang memiliki keterampilan di kedua sisi (front end dan back end) tetapi mungkin tidak sepenuhnya ahli di salah satu sisi. Mereka dapat menangani sebagian besar tugas, tetapi mungkin memerlukan bantuan untuk tugas yang lebih kompleks di salah satu sisi.</p>
                        </div>
                    </div>
                    <div class="xl:w-1/4 md:w-1/2 p-4">
                        <div class="bg-gray-100 h-full p-6 rounded-lg">
                        <img class="h-40 rounded w-full object-cover object-center mb-6" src="../../assets/aryasatya.JPG" alt="content">
                        <h3 class="tracking-widest text-indigo-500 text-xs font-medium title-font">Front-End Dev</h3>
                        <h2 class="text-lg text-gray-900 font-medium title-font mb-4">Muhammad Aryasatya Triputra</h2>
                        <p class="leading-relaxed text-base">Pengembang front end fokus pada pengembangan antarmuka pengguna dari aplikasi. Mereka bekerja dengan teknologi seperti HTML, CSS, dan JavaScript untuk menciptakan pengalaman pengguna yang menarik dan responsif.</p>
                        </div>
                    </div>
                    <div class="xl:w-1/4 md:w-1/2 p-4">
                        <div class="bg-gray-100 h-full p-6 rounded-lg">
                            <img class="h-40 rounded w-full object-cover object-top mb-6" src="../../assets/fahry.jpg" alt="content">
                            <h3 class="tracking-widest text-indigo-500 text-xs font-medium title-font">Front-End Dev</h3>
                            <h2 class="text-lg text-gray-900 font-medium title-font mb-4">Fahry Prathama</h2>
                            <p class="leading-relaxed text-base">Pengembang front end fokus pada pengembangan antarmuka pengguna dari aplikasi. Mereka bekerja dengan teknologi seperti HTML, CSS, dan JavaScript untuk menciptakan pengalaman pengguna yang menarik dan responsif.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <footer class="flex flex-col space-y-10 justify-center m-10">

            <nav class="flex justify-center flex-wrap gap-6 text-gray-500 font-medium">
                <a class="hover:text-gray-900" href="home.php">Home</a>
                <a class="hover:text-gray-900" href="dashboard.php">Dashboard</a>
                <a class="hover:text-gray-900" href="user/edit_profile.php">Profile Info</a>
            </nav>

            <div class="flex justify-center space-x-5">
                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
                    <img src="https://img.icons8.com/fluent/30/000000/facebook-new.png" />
                </a>
                <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer">
                    <img src="https://img.icons8.com/fluent/30/000000/linkedin-2.png" />
                </a>
                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
                    <img src="https://img.icons8.com/fluent/30/000000/instagram-new.png" />
                </a>
                <a href="https://messenger.com" target="_blank" rel="noopener noreferrer">
                    <img src="https://img.icons8.com/fluent/30/000000/facebook-messenger--v2.png" />
                </a>
                <a href="https://twitter.com" target="_blank" rel="noopener noreferrer">
                    <img src="https://img.icons8.com/fluent/30/000000/twitter.png" />
                </a>
            </div>
            <p class="text-center text-gray-700 font-medium">&copy; 2024 To-Do List Inc. All rights reservered.</p>
        </footer>
    <script>
        const blink = document.querySelector('.blink');
        setInterval(() => {
            blink.style.visibility = (blink.style.visibility === 'hidden') ? 'visible' : 'hidden';
        }, 500);
    </script>
</body>

</html>