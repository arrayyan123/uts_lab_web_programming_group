<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once '../../../models/user.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = getUserById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (updateUserProfile($_SESSION['user_id'], $username, $email, $password)) {
        $message = "Profile updated successfully.";
    } else {
        $message = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
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
                    <li class="md:px-4 md:py-2 hover:text-indigo-400"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="md:px-4 md:py-2 hover:text-indigo-400"><a href="../home.php">Home</a></li>
                    <li class="md:px-4 md:py-2 text-indigo-500"><a href="edit_profile.php">Edit Profile</a></li>
                    <li class="md:px-4 md:py-2 hover:text-indigo-400"><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <button class="bg-indigo-600 mt-4 ml-6 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition duration-300">
        <a href="../home.php">Back to Home</a>
    </button>
    <section class="py-10 my-auto dark:bg-gray-900">
        <div class="lg:w-[80%] md:w-[90%] xs:w-[96%] m-6 flex gap-4">
            <div class="lg:w-[88%] md:w-[80%] sm:w-[88%] xs:w-full mx-auto shadow-2xl p-4 rounded-xl h-fit self-center dark:bg-gray-800/40">
                <!--  -->
                <div class="">
                    <h1 class="lg:text-3xl md:text-2xl sm:text-xl xs:text-xl font-extrabold mb-2 dark:text-white">
                        Profile
                    </h1>
                    <h2 class="text-sm mb-4 dark:text-gray-400">Edit Profile</h2>
                    <form>
                        <div class="flex lg:flex-row md:flex-col items-center sm:flex-col xs:flex-col gap-2 justify-center w-full">
                            <div class="w-full  mb-4 lg:mt-6">
                                <label for="" class="mb-2 dark:text-gray-300">Username</label>
                                <input class="mt-2 p-4 w-full border-2 rounded-lg dark:text-gray-200 dark:border-gray-600 dark:bg-gray-800" type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br>
                            </div>
                            <div class="w-full  mb-4 lg:mt-6">
                                <label for="" class=" dark:text-gray-300">Email</label>
                                <input class="mt-2 p-4 w-full border-2 rounded-lg dark:text-gray-200 dark:border-gray-600 dark:bg-gray-800" type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
                            </div>
                        </div>
                        <div class="flex lg:flex-row md:flex-col sm:flex-col xs:flex-col gap-2 justify-center w-full">
                            <div class="w-full">
                                <div class="w-full  mb-4 lg:mt-6">
                                    <label for="" class=" dark:text-gray-300">Password</label>
                                    <input class="mt-2 p-4 w-full border-2 rounded-lg dark:text-gray-200 dark:border-gray-600 dark:bg-gray-800" type="password" name="password" ><br>
                                </div>
                            </div>
                        </div>
                        <div class="w-full rounded-lg bg-blue-500 mt-4 text-white text-lg font-semibold">
                            <button type="submit" class="w-full p-4">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
