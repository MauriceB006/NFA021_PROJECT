<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'project';
$username = 'root';
$password = '';

$error = '';
$success = '';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../sign_in.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Validate inputs
        if (empty($_POST['password']) || empty($_POST['address'])) {
            throw new Exception("Password and address are required.");
        }

        $input_password = $_POST['password'];
        $address = htmlspecialchars(trim($_POST['address']));

        // Connect to database
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get hashed password
        $stmt = $conn->prepare("SELECT password1 FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($input_password, $row['password1'])) {
            throw new Exception("Invalid password for this user ID.");
        }

        // Get full user info
        $userInfo = $conn->prepare("SELECT full_name, email, phone FROM users WHERE user_id = :user_id");
        $userInfo->execute([':user_id' => $user_id]);
        $data = $userInfo->fetch(PDO::FETCH_ASSOC);

        // Insert into bus_card_applications
        $stmt = $conn->prepare("INSERT INTO bus_card_applications 
            (user_id, name, email, phone, address) 
            VALUES (:user_id, :name, :email, :phone, :address)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':name'    => $data['full_name'],
            ':email'   => $data['email'],
            ':phone'   => $data['phone'],
            ':address' => $address
        ]);

        header("Location: ../success.html");
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Apply for a Bus Card</title>
  <style>
    :root {
      --green-blue-crayola: hsl(244, 59%, 30%);
      --prussian-blue: hsl(202, 72%, 15%);
      --indigo-dye: hsl(202, 64%, 26%);
      --dark-orange: hsl(244, 89%, 17%);
    }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 40px;
      background: linear-gradient(135deg, var(--green-blue-crayola), var(--prussian-blue), var(--indigo-dye), var(--dark-orange));
      color: #ffffff;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      max-width: 600px;
      width: 100%;
      padding: 30px;
      background: linear-gradient(135deg, var(--dark-orange), var(--indigo-dye), var(--prussian-blue));
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      animation: fadeIn 1s ease-in-out;
    }

    h1 {
      text-align: center;
      color: #ffffff;
      margin-bottom: 30px;
      animation: slideIn 0.7s ease-out;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #ffffff;
    }

    input,
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 5px;
      font-size: 14px;
      background: rgba(255, 255, 255, 0.1);
      color: #ffffff;
      transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    input:hover,
    textarea:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    input:focus,
    textarea:focus {
      background: rgba(255, 255, 255, 0.3);
      box-shadow: 0 0 8px var(--dark-orange);
      outline: none;
    }

    .btn {
      width: 100%;
      padding: 12px;
      background-color: var(--green-blue-crayola);
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .btn:hover {
      background-color: var(--dark-orange);
      transform: scale(1.05);
    }

    .error-box {
      background: rgba(255, 0, 0, 0.1);
      color: #ffcccc;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ff9999;
      border-radius: 5px;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes slideIn {
      from {
        transform: translateX(-100%);
      }
      to {
        transform: translateX(0);
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Apply for a Bus Card</h1>

    <?php if (!empty($error)): ?>
      <div class="error-box">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label>Your User ID:</label>
        <input type="text" value="<?= htmlspecialchars($user_id) ?>" readonly />
        <small style="color:#ddd;">(Automatically assigned, cannot be changed)</small>
      </div>

      <div class="form-group">
        <label for="password">Your Password:</label>
        <input type="password" name="password" id="password" required />
      </div>

      <div class="form-group">
        <label for="address">Your Address:</label>
        <textarea name="address" id="address" rows="4" required></textarea>
      </div>

      <button type="submit" class="btn">Submit Application</button>
    </form>
  </div>
</body>
</html>
