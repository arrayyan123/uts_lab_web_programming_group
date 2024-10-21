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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            overflow: hidden; /* Prevent scrolling */
            background: linear-gradient(-45deg, #4c6ef5, #6f2c91, #4c6ef5, #6f2c91);
            background-size: 400% 400%; /* Extend background for smoother animation */
            animation: gradientAnimation 15s ease infinite; /* Smooth infinite loop */
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
    </style>
</head>
<body>
    <div class="flex justify-center items-center h-screen">
        <div class="w-full max-w-md p-8 bg-white shadow-lg rounded-lg card">
            <h1 class="text-4xl font-semibold text-center text-gray-800 mb-6">
                <i class="fa-solid fa-user"></i> Login
            </h1>
            <hr class="mb-6">
            <?php if (isset($message)) echo "<p class='text-red-600 text-center mb-4'>$message</p>"; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="input-field block w-full mt-1 px-4 py-2 border rounded-md shadow-sm text-gray-700" placeholder="Enter Email..." required>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" class="input-field block w-full mt-1 px-4 py-2 border rounded-md shadow-sm text-gray-700" placeholder="Enter Password..." required>
                </div>
                <div class="mt-3 text-right">
                    <a href="forgot_password.php" class="text-indigo-600 font-semibold hover:underline transition duration-200">Forgot Password?</a>
                </div>
                <div>
                    <button type="submit" class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-md shadow hover:bg-indigo-500 focus:outline-none transition duration-200">
                        <i class="fa-solid fa-right-to-bracket"></i>&nbsp;&nbsp;Login
                    </button>
                </div>
            </form>
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Don't have an account? 
                    <a href="register.php" class="text-indigo-600 hover:underline transition duration-200">Register</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>