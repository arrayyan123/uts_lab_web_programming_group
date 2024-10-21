// reset_password.php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../../../vendor/autoload.php';
require '../../../models/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
$token = $_GET['token'] ?? '';

if (!$token) {
    die('Token not provided.');
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Invalid token.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'];

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt->execute([$hashedPassword, $user['email']]);

    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);

    echo 'Password has been reset successfully.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form action="" method="post">
        <label for="password">New Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
