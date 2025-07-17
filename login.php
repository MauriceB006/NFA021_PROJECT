<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACTC - Login / Signup </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css" />
</head>
<body>
        <a href="indexV51.php" class="back-button">
            <i class="fas fa-chevron-left"></i> Back
        </a>

    <div class="container" id="container">
        <!-- Registration Form -->
        <div class="form-container register-container">
            <form action="sign_in.php" method="post">
                <h1>Create an account</h1>
                <?php if (isset($_SESSION['errors']['general'])): ?>
                    <div class="error-message">
                        <p><?php echo htmlspecialchars($_SESSION['errors']['general']); ?></p>
                    </div>
                <?php endif; ?>
                
                <input type="text" name="full_name" placeholder="Full Name" required 
                       value="<?php echo htmlspecialchars($_SESSION['form_data']['full_name'] ?? ''); ?>">
                <?php if (isset($_SESSION['errors']['full_name'])): ?>
                    <div class="error-message field-error">
                        <p><?php echo htmlspecialchars($_SESSION['errors']['full_name']); ?></p>
                    </div>
                <?php endif; ?>
                
                <input type="email" name="email" placeholder="Email" required 
                       value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>">
                <?php if (isset($_SESSION['errors']['email'])): ?>
                    <div class="error-message field-error">
                        <p><?php echo htmlspecialchars($_SESSION['errors']['email']); ?></p>
                    </div>
                <?php endif; ?>
                
                <input type="tel" name="phone" placeholder="Phone Number" required 
                       value="<?php echo htmlspecialchars($_SESSION['form_data']['phone'] ?? ''); ?>">
                <?php if (isset($_SESSION['errors']['phone'])): ?>
                    <div class="error-message field-error">
                        <p><?php echo htmlspecialchars($_SESSION['errors']['phone']); ?></p>
                    </div>
                <?php endif; ?>
                
                <input type="password" name="password" id="registerPassword" placeholder="Password" required>
                <div class="input-container">
                    <span onclick="toggleRegisterPassword()" style="cursor: pointer;" class="icon-container">
                        <i class="fa fa-eye" id="registerEyeIcon"></i>
                    </span>
                </div>
                <?php if (isset($_SESSION['errors']['password'])): ?>
                    <div class="error-message field-error">
                        <p><?php echo htmlspecialchars($_SESSION['errors']['password']); ?></p>
                    </div>
                <?php endif; ?>
                
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <?php if (isset($_SESSION['errors']['confirm_password'])): ?>
                    <div class="error-message field-error">
                        <p><?php echo htmlspecialchars($_SESSION['errors']['confirm_password']); ?></p>
                    </div>
                <?php endif; ?>
                
                <button type="submit" name="register">Create</button>
                
                <?php 
                unset($_SESSION['errors']);
                unset($_SESSION['form_data']); 
                ?>
            </form>
        </div>

        <!-- Login Form -->
        <div class="form-container login-container">
            <form action="sign_in.php" method="post">
                <h1>Login here</h1>
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="error-message">
                        <p><?php echo htmlspecialchars($_SESSION['login_error']); ?></p>
                        <?php unset($_SESSION['login_error']); ?>
                    </div>
                <?php endif; ?>
                
                <input type="email" name="email" placeholder="Email" required>
                <div class="input-container">
                    <input type="password" name="password" id="loginPassword" placeholder="Password" required>
                    <span onclick="toggleLoginPassword()" class="icon-container" style="cursor: pointer;">
                        <i class="fa fa-eye" id="loginEyeIcon"></i>
                    </span>
                </div>
                <div class="content">
                    <div class="checkbox">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <div class="pass-link">
                        <a href="authentication/forgot_password.php">Forgot password?</a>
                    </div>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1 class="title">Welcome <br> Back!</h1>
                    <p>Already have an account? Login</p>
                    <button class="ghost" id="login">Login</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1 class="title">Join us <br> now!</h1>
                    <p>Don't have an account? Create one</p>
                    <button class="ghost" id="register">Register</button>
                </div>
            </div>
        </div>
    </div>
    <script src="login.js"></script>
</body>
</html>
