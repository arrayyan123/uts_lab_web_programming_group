<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once '/Applications/XAMPP/xamppfiles/htdocs/uts_lab_web_programming_group/models/user.php';

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
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" value="Update">
    </form>
    <a href="home.php">Back to Home</a>
</body>
</html>
