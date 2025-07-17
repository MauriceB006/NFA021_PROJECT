<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: "Manrope", sans-serif;
      background: linear-gradient(135deg, #0a192f, #1e3a5f);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .forgot-container {
      background-color: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
      padding: 40px 30px;
      width: 100%;
      max-width: 400px;
      text-align: center;
      color: #ffffff;
    }

    h1 {
      font-size: 36px;
      margin-bottom: 10px;
      font-weight: 700;
      text-shadow: 0 0 10px rgba(75, 182, 183, 0.6);
    }

    p {
      font-size: 16px;
      color: #cbd5e1;
      margin-bottom: 25px;
    }

    form {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    input[type="email"] {
      padding: 12px 15px;
      border: none;
      border-radius: 10px;
      background-color: #e3dde8;
      width: 100%;
      margin-bottom: 20px;
      font-size: 15px;
    }

    button {
      background-color: #1a2093;
      color: #fff;
      border: none;
      border-radius: 20px;
      padding: 12px 30px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s ease-in-out;
    }

    button:hover {
      letter-spacing: 1px;
      background-color: #2430b5;
    }

    a {
      color: #4bb6b7;
      font-size: 14px;
      margin-top: 20px;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

  </style>
</head>
<body>

  <div class="forgot-container">
    <h1>Reset Password</h1>
    <p>Enter your email to receive a password reset link</p>

    <?php
    session_start();

    $error = $_SESSION['error'] ?? '';
    $success = $_SESSION['success'] ?? '';
    unset($_SESSION['error'], $_SESSION['success']);
    ?>

    <?php if ($error): ?>
        <div style="padding: 12px; border-radius: 8px; margin-bottom: 20px; color: red; font-weight: bold;font-size: 15px;">    
    <?= htmlspecialchars($error) ?>
        </div>
<?php endif; ?>

<?php if ($success): ?>
  <div style="padding: 12px; border-radius: 8px; margin-bottom: 20px; color: green; font-weight: bold; font-size: 15px;">
    <?= $success ?>
  </div>
<?php endif; ?>

    <form action="send_reset.php" method="POST">
      <input type="email" name="email" placeholder="Enter your email" required />
      <button type="submit">Send Reset Link</button>
    </form>
    <a href="sign_in.php">Back to login</a>
  </div>

</body> 
</html>
