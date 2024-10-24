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

$successMessage = '';

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

    $successMessage = 'Password has been reset successfully.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../../css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            overflow: hidden; /* Prevent scrolling */
            background: linear-gradient(-45deg, #4c6ef5, #6f2c91, #4c6ef5, #6f2c91);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .input-field:focus {
            border-color: #4c6ef5;
            box-shadow: 0 0 5px rgba(76, 110, 245, 0.5);
            outline: none;
        }

        button {
            transition: transform 0.2s, box-shadow 0.2s, background-color 0.2s;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            background-color: #5a8be3;
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        h1 {
            animation: fadeIn 0.5s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            h1 {
                font-size: 2.5rem;
            }
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="flex justify-center items-center h-screen">
        <div class="w-full max-w-md p-8 bg-white shadow-lg rounded-lg card">
            <h1 class="text-4xl font-semibold text-center text-gray-800 mb-6">
                <i class="fa-solid fa-key"></i> Reset Password
            </h1>
            <hr class="mb-6">

            <!-- Success Message -->
            <?php if (!empty($successMessage)) { ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
                <div class="flex flex-col justify-center mt-4">
                    <a href="login.php" class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-md shadow hover:bg-indigo-500 focus:outline-none transition duration-200 text-center">
                        <i class="fa-solid fa-arrow-left"></i>&nbsp;&nbsp;Back to login
                    </a>
                </div>
            <?php } else { ?>
                <form action="" method="post" class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password:</label>
                        <input type="password" name="password" id="password" class="input-field block w-full mt-1 px-4 py-2 border rounded-md shadow-sm text-gray-700" required>
                    </div>
                    <div>
                        <button type="submit" class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-md shadow hover:bg-indigo-500 focus:outline-none transition duration-200">
                            <i class="fa-solid fa-lock"></i>&nbsp;&nbsp;Reset Password
                        </button>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>