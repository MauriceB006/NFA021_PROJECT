<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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

    .reset-container {
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
      font-size: 32px;
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

    .input-container {
      position: relative;
      width: 100%;
    }

    .input-container input {
      width: 100%;
      padding: 12px 15px;
      padding-right: 40px;
      border: none;
      border-radius: 10px;
      background-color: #e3dde8;
      margin-bottom: 20px;
      font-size: 15px;
      transition: all 0.3s ease-in-out;
    }

    .input-container input:focus {
      outline: none;
      box-shadow: 0 0 8px rgba(75, 182, 183, 0.5);
    }

    .icon {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
      color: gray;
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

    .icon-container {
    position: absolute;
    top: 50%;
    right: 10px; 
    transform: translateY(-50%);
    cursor: pointer;
  }
  
  #newPassEyeIcon {
    color: gray; 
    font-size: 18px; 
    padding-bottom: 80px;
 }

   #confirmPassEyeIcon {
    color: gray; 
    font-size: 18px; 
    padding-bottom: 80px;
 }
  </style>
</head>
<body>

  <div class="reset-container">
    <h1>Create New Password</h1>
    <p>Enter and confirm your new password below</p>
<?php
    session_start();
    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['error']);
        if ($error):
?>
  <div style=" padding: 12px; border-radius: 8px; margin-bottom: 20px; color: red; font-weight: bold;font-size: 13px;">
    <?php echo htmlspecialchars($error); ?>
  </div>
<?php endif; ?>

    <form action="update_password.php" method="POST">
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
<div class="input-container">
    <input type="password" name="new_password" id="newPassword" placeholder="New Password" required>
        <div class="input-container">
            <span onclick="toggleNewPassword()" style="cursor: pointer;" class="icon-container">
                <i class="fa fa-eye" id="newPassEyeIcon"></i>
            </span>
        </div>
</div>
<div class="input-container">
    <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required>
        <div class="input-container">
            <span onclick="toggleConfirmPassword()" style="cursor: pointer;" class="icon-container">
                <i class="fa fa-eye" id="confirmPassEyeIcon"></i>
            </span>
        </div>
</div>
        <button type="submit">Reset Password</button>
    </form> 
    <a href="login.php">Back to login</a>
  </div>

  <script>

function toggleNewPassword() {
const passwordField = document.getElementById('newPassword');
const eyeIcon = document.getElementById('newPassEyeIcon');
  if (passwordField.type === 'password') {
    passwordField.type = 'text';
    eyeIcon.classList.remove('fa-eye');
    eyeIcon.classList.add('fa-eye-slash');
  }else{
    passwordField.type = 'password';
    eyeIcon.classList.remove('fa-eye-slash');
    eyeIcon.classList.add('fa-eye');
  }
}
function toggleConfirmPassword() {
const passwordField = document.getElementById('confirmPassword');
const eyeIcon = document.getElementById('confirmPassEyeIcon');
  if (passwordField.type === 'password') {
      passwordField.type = 'text';
      eyeIcon.classList.remove('fa-eye');
      eyeIcon.classList.add('fa-eye-slash');
  }else{
      passwordField.type = 'password';
      eyeIcon.classList.remove('fa-eye-slash');
      eyeIcon.classList.add('fa-eye');
  }
}
  </script>

</body>
</html>
