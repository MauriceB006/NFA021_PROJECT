<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "ratroot";
$dbname = "project";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Handle login
    if (isset($_POST['login'])) {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        
        // Changed to password1 to match database
        $stmt = $conn->prepare("SELECT user_id, full_name, password1 FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password1'])) {
                // Successful login
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['full_name'];
                
                // Remember me functionality
                if (isset($_POST['remember'])) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + 86400 * 30, "/");
                    
                    $updateStmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE user_id = ?");
                    $updateStmt->bind_param("si", $token, $user['user_id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
                
                header("Location: indexV51.php");
                exit();
            }
        }
        
        // If login fails
        $_SESSION['login_error'] = "Incorrect email or password";
        header("Location: sign_in.php");
        exit();
    }

    // Handle registration
    if (isset($_POST['register'])) {
        $_SESSION['errors'] = [];
        
        //handles validation for everything in register/sign in form
        $full_name = trim($_POST['full_name'] ?? '');
        if(strlen($full_name) < 2){
            $_SESSION['errors']['full_name'] = "Name must contain at least 2 characters.";
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match($emailPattern, $email)) {
            $_SESSION['errors']['email'] = "Please enter a valid email address.";
        } else {
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $_SESSION['errors']['email'] = "This email is already registered.";
            }
        }

        $password = $_POST['password'] ?? '';
        if (!isValidPassword($password)) {
            $_SESSION['errors']['password'] = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
        }

        $confirm_password = $_POST['confirm_password'] ?? '';
        if ($password !== $confirm_password) {
            $_SESSION['errors']['confirm_password'] = "Passwords do not match.";
        }

        $phone = $_POST['phone'] ?? '';
        if (!empty($phone) && !check_phone($phone)) {
            $_SESSION['errors']['phone'] = "Phone number must begin with a prefix followed by 6 digits (no spaces).";
        }
        

        // If no errors, register user
        if (empty($_SESSION['errors'])) {
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $phone = !empty($phone) ? $phone : null;
            
            // This is correct for password1 column
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password1) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("ssss", $full_name, $email, $phone, $hashedPassword);
            
            if ($stmt->execute()) {
                // Success - log in user
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $full_name;
                // Right before the redirect, add:
                // error_log("Debug: Login successful, redirecting to indexV51.php");
                // var_dump($_SESSION);
                // exit();  //this is for debugging while developping
                header("Location: indexV51.php");
                exit();
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        } else {
            $_SESSION['form_data'] = $_POST;
            header("Location: sign_in.php");
            exit();
        }
    }

} catch (Exception $e) {
    $_SESSION['errors'][] = "System error: " . $e->getMessage();
    header("Location: sign_in.php");
    exit();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

function isValidPassword($password) {
    $passLength = strlen($password) >= 8;
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    return $passLength && $uppercase && $lowercase && $number && $specialChars;
}

function check_phone($phone) {
    $patternNumb = '/^(03|3|70|71|76|81)\d{6}$/';
    return preg_match($patternNumb, $phone);
}
?>
