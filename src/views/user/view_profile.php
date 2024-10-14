<?php
// view_profile.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '/Applications/XAMPP/xamppfiles/htdocs/uts_lab_web_programming_group/models/user.php';

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit;
}

// Get user data
$user = getUserById($_SESSION['user_id']);

// If user not found, show an error message
if (!$user) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
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
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="../home.php">Home</a>
        <a href="../dashboard.php">Dashboard</a>
        <a href="../user/edit_profile.php">Edit Profile</a>
        <a href="../user/logout.php">Logout</a>
    </div>

    <!-- User Profile -->
    <h1>Your Profile</h1>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
    <p><strong>Member Since:</strong> <?= date('F j, Y', strtotime($user['created_at'])); ?></p>

    <a href="edit_profile.php">Edit Profile</a>

</body>
</html>
