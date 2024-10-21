<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../../../vendor/autoload.php'; // Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include_once '../../../models/db.php';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $expires_at = date("Y-m-d H:i:s", strtotime('+1 hour')); 

        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires_at]);
        $reset_link = "http://localhost:81/uts_lab_web_programming_group/src/views/user/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ryan.art326@gmail.com';
            $mail->Password   = 'gxjy xuau exme ryzu'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->SMTPDebug = 0; 

            $mail->setFrom('ryan.art326@gmail.com', 'ToDo List Reset Password');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click this link to reset your password: <a href='$reset_link'>$reset_link</a>";
            $mail->AltBody = "Click this link to reset your password: $reset_link";
            // Send email
            $mail->send();
            header("Location: ../src/views/forgot_password.php?message=Check your email for the password reset link.");
            exit();
        } catch (Exception $e) {
            header("Location: ../src/views/forgot_password.php?message=Email sending failed: {$mail->ErrorInfo}");
            exit();
        }
    } else {
        header("Location: ../src/views/forgot_password.php?message=Email address not found.");
        exit(); 
    }
}
?>
