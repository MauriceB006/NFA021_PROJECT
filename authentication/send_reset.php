<?php
session_start();
date_default_timezone_set('Asia/Beirut');


require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// DB Config
define('MYSQL_SERVERNAME', 'localhost');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', '');
define('MYSQL_DBNAME', 'project');

// DB connection function
function getMySQLiConnection() {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
    try {
        $conn = new mysqli(MYSQL_SERVERNAME, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DBNAME);
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Main handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getMySQLiConnection();

    $email = trim($_POST['email']);
    if (empty($email)) {
        $_SESSION['error'] = "Email is required.";
        header("Location: forgot_password.php");
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $_SESSION['error'] = "No user found with that email.";
        header("Location: forgot_password.php");
        exit;
    }

    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();


    // Generate token
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));


    // Delete old tokens
    $delete = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $delete->bind_param("i", $user_id);
    $delete->execute();

    // Insert new token
    $insert = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    if (!$insert) die("Insert prepare failed: " . $conn->error);
    $insert->bind_param("iss", $user_id, $token, $expires);
    if (!$insert->execute()) {
        die("Insert failed: " . $insert->error);
    }


    // Reset link
    $resetLink = "http://localhost/NFA021_PROJECT-main/authentication/reset_password.php?token=$token";

    // Setup PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'company@gmail.com'; //should be company email 
        $mail->Password   = ''; //pass from 2step verfic. password SMTP of the email
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('company@gmail.com', 'ACTC - Public Transport'); //should be company email
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';
        $mail->Body    = "Click the link below to reset your password:<br><br>
                          <a href='$resetLink'>Reset Your Password</a><br><br>
                          This link will expire in 1 hour.";

        $mail->send();
        $_SESSION['success'] = "A reset link was sent to <b>$email</b>.";
        header("Location: forgot_password.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Invalid request method.";
}
