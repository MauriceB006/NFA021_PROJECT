<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'project';
$username = 'root';
$password = '';

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Verify all required fields
        if (empty($_POST['password']) || empty($_POST['address'])) {
            throw new Exception("Password and address are required");
        }

        $input_password = $_POST['password'];
        $address = htmlspecialchars(trim($_POST['address']));

        // Connect to database
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 1. Verify password matches user_id
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = :user_id AND password1 = :password");
        $stmt->execute([
            ':user_id' => $user_id,
            ':password' => $input_password // Note: In production, use password_verify() with hashed passwords
        ]);

        if (!$stmt->fetch()) {
            throw new Exception("Invalid password for this user ID");
        }

        // 2. Insert application with verified user_id
        $stmt = $conn->prepare("INSERT INTO bus_card_applications 
                              (user_id, address) 
                              VALUES (:user_id, :address)");
        
        $stmt->execute([
            ':user_id' => $user_id,
            ':address' => $address
        ]);

        // Success - redirect to confirmation
        header("Location: application_success.php");
        exit();

    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bus Card Application</title>
</head>
<body>
    <h1>Apply for Bus Card</h1>
    
    <?php if ($error): ?>
        <div style="color:red; padding:10px; margin:10px 0; border:1px solid red;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <div>
            <label>Your User ID:</label>
            <input type="text" value="<?= htmlspecialchars($user_id) ?>" readonly>
            <small>(Automatically assigned, cannot be changed)</small>
        </div>
        
        <div>
            <label for="password">Your Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        
        <div>
            <label for="address">Your Address:</label>
            <textarea name="address" id="address" rows="4" required></textarea>
        </div>
        
        <button type="submit">Submit Application</button>
    </form>
</body>
</html>