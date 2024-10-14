<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '/Applications/XAMPP/xamppfiles/htdocs/uts_lab_web_programming_group/models/db.php';
session_start();

function register($username, $email, $password) {
    $db = new Database();
    $conn = $db->getConnection();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    return $stmt->execute();
}

function login($email, $password) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header("Location: login.php");
}
?>
