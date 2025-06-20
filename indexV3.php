<?php
session_start();

// If not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: sign_in.php");
    exit();
}

// Your existing HTML content
?>