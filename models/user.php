<?php
// user.php
include_once 'db.php';

function getUserById($userId) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT * FROM users WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserByEmail($email) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUserProfile($userId, $username, $email, $password) {
    $db = new Database();
    $conn = $db->getConnection();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':id', $userId);

    return $stmt->execute();
}
?>
