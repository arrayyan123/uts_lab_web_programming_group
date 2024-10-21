<?php
// login.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '../../../controllers/auth_controller.php';

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login($email, $password)) {
        header("Location: home.php");
        exit();
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../../css/output.css">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <div class="bg-green-500">
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
    </div>
    <div class="flex flex-col">
    <a href="register.php">Register</a>
    <a href="forgot_password.php">Forgot Password?</a>
    </div>
</body>
</html>
