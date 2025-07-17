<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$dbname = 'project';
$username = 'root';
$password = '';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate inputs
        $required_fields = ['line', 'date', 'time', 'rating', 'comment'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required");
            }
        }

        // For demonstration, using a static user_id - in real app, get from session
        // Check if logged in
    
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../sign_in.php");
    exit();
}
$user_id = $_SESSION['user_id'];

        
        $line_id = htmlspecialchars(trim($_POST['line']));
        // After connecting to $conn
        $conn = new PDO("mysql:host=localhost;dbname=project", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$checkLine = $conn->prepare("SELECT COUNT(*) FROM buslines1 WHERE line_id = ?");
$checkLine->execute([$line_id]);
if ($checkLine->fetchColumn() == 0) {
    throw new Exception("🚫 The selected bus line does not exist.");
}



        $rating = intval($_POST['rating']);
        $comment = htmlspecialchars(trim($_POST['comment']));
        $travel_date = $_POST['date'];
        $travel_time = $_POST['time'];

        // Validate rating
        if ($rating < 1 || $rating > 5) {
            throw new Exception("Invalid rating value");
        }

        // Validate date
        if (!DateTime::createFromFormat('Y-m-d', $travel_date)) {
            throw new Exception("Invalid date format");
        }

        

        // Prepare and execute the insert statement
        $stmt = $conn->prepare("INSERT INTO bus_reviews 
                              (user_id, line_id, rating, comment, travel_date, travel_time) 
                              VALUES (:user_id, :line_id, :rating, :comment, :travel_date, :travel_time)");
        
        $stmt->execute([
            ':user_id' => $user_id,
            ':line_id' => $line_id,
            ':rating' => $rating,
            ':comment' => $comment,
            ':travel_date' => $travel_date,
            ':travel_time' => $travel_time
        ]);

        // Success - redirect to indexV3.1.php
        header("Location: ../success.html");
        exit();

    } catch(PDOException $e) {
        // Log the error and show a message
        error_log("Database error: " . $e->getMessage());
        die("An error occurred while submitting your review. Please try again later.");
    } catch(Exception $e) {
        // Log the error and show a message
        error_log("Error: " . $e->getMessage());
        die($e->getMessage());
    }
} else {
    // If someone tries to access this page directly, redirect them
    header("Location: ../indexV51.php");
    exit();
}
