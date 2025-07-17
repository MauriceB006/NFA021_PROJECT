<?php
session_start();
date_default_timezone_set('Asia/Beirut');

define('MYSQL_SERVERNAME', 'localhost');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', ''); 
define('MYSQL_DBNAME', 'project');

function getMySQLiConnection() {
    mysqli_report(MYSQLI_REPORT_OFF);
    try {
        $conn = new mysqli(MYSQL_SERVERNAME, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DBNAME);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    } catch (Exception $e) {
        $_SESSION['error'] = "Database connection error. Please try again later.";
        header("Location: reset_password.php?token=" . urlencode($_POST['token'] ?? ''));
        exit;
    }
}

$conn = getMySQLiConnection();

function isValidPassword($password) {
    $passLength = strlen($password) >= 8;
    $uppercase    = preg_match('@[A-Z]@', $password);
    $lowercase    = preg_match('@[a-z]@', $password);
    $number       = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    return $passLength && $uppercase && $lowercase && $number && $specialChars;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit;
    }

    if (!isValidPassword($newPassword)) {
        $_SESSION['error'] = "Password must be at least 8 characters long and should include an uppercase letter, lowercase letter, number, and special character.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit;
    }

    // Validate token and get user_id
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
    if (!$stmt) {
        $_SESSION['error'] = "Server error: failed to prepare query.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit;
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $resetRequest = $result->fetch_assoc();
    $stmt->close();

    if (!$resetRequest) {
        $_SESSION['error'] = "Invalid or expired token. Please try again requesting a new reset link.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit;
    }

    $userId = $resetRequest['user_id'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update user's password
    $stmt = $conn->prepare("UPDATE users SET password1 = ? WHERE user_id = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Server error: failed to prepare query.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit;
    }
    $stmt->bind_param("si", $hashedPassword, $userId);
    $stmt->execute();
    $stmt->close();

    // Delete token to prevent reuse
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Server error: failed to prepare query.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit;
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();

    // Success, redirect to success page
    header("Location: reset_success.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: reset_password.php");
    exit;
}
