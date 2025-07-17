<?php
session_start();
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
$host = 'localhost';
$dbname = 'project';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Required fields
    $required = ['fullname', 'email', 'phone', 'message'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("All fields must be filled.");
        }
    }
// Check if logged in
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    // Sanitize and validate
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);
    $message = htmlspecialchars(trim($_POST['message']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format.");
    }

    if (!preg_match('/^\d{2}-\d{3}-\d{3}$/', $phone)) {
        throw new Exception("Phone must be in format 00-000-000.");
    }

    // Assign default subject and status
    $subject = "Contact Form Message";
    $status = "pending";
   

    // Insert into contact_messages
    $stmt = $conn->prepare("
        INSERT INTO contactmessages (user_id, subject, message, status, created_at)
        VALUES (:user_id, :subject, :message, :status, NOW())
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':subject' => $subject,
        ':message' => $message,
        ':status' => $status
    ]);

    header("Location: success.html");
    exit();

} catch (Exception $e) {
    echo "<div style='color: red; font-family: Arial; padding: 20px;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
