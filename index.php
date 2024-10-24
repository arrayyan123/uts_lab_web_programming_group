<?php
    $requestUri = isset($_SERVER['REQUEST_URI']) ? filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL) : '/';
    if ($requestUri === '/' || $requestUri === '') {
        header('Location: src/views/home.php');
        exit();
    }
    $requestUri = trim($requestUri, '/');
    if (strpos($requestUri, '..') !== false) {
        include 'src/views/404.php';
        exit();
    }
    if (file_exists("src/views/$requestUri.php")) {
        include "src/views/$requestUri.php";
    } elseif (file_exists("src/views/user/$requestUri.php")) {
        include "src/views/user/$requestUri.php";
    } else {
        include 'src/views/404.php';
    }
    exit();
?>
