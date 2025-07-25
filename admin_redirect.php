<?php
session_start();

// Dummy admin credentials
$admin_email = 'admin@example.com';
$admin_password = 'securepass123';

$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['role'] = 'admin';
        header("Location: db-visualizer.php");
        exit();
    } else {
        $error = "Invalid credentials. Access denied.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, hsl(244, 59%, 30%), hsl(202, 72%, 15%));
      color: white;
      min-height: 100vh;
      margin: 0;
      padding: 0;
    } 
        .main-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px;
}
    .login-box {
      background: rgba(255, 255, 255, 0.1);
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      width: 300px;
    }
    h2 {
      margin-bottom: 20px;
    }
    input {
      width: 90%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      background: rgba(255, 255, 255, 0.2);
      color: white;
    }
    input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }
    .btn {
      width: 100%;
      padding: 10px;
      background: hsl(244, 89%, 17%);
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
    }
    .btn:hover {
      background: hsl(202, 64%, 26%);
    }
    .error {
      color: red;
      margin-bottom: 10px;
    }
            
        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 0;
            box-shadow: var(--shadow);
            position: relative;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .back-button {
            background-color: transparent;
            color: white;
            border: 1px solid white;
            padding: 6px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }
        
        .back-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
  </style>
</head>
<body>
    <header class="header">
    <div class="header-content">
        <a href="indexV51.php" class="back-button">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h1>
            <img src="assets/images_v5/ACTC LOGO -02 SMALL.png" class="logo" alt="ACTC Public Transport" style="height: 50px;">
        </h1>
        <div style="width: 80px;"></div>
    </div>
</header>
<div class="main-wrapper">
  <div class="login-box">
    <h2>Admin Access</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="admin_redirect.php">
      <input type="email" name="email" placeholder="Admin Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn">Login</button>
    </form>
  </div>
</div>
</body>
</html>
