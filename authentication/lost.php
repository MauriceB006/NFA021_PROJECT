<?php
// Debug first
session_start();
echo "<pre>POST Data: ";
print_r($_POST);
echo "</pre>";

// Database connection
$conn = new mysqli('localhost', 'root', '', 'project');
if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// Get form data
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$name = $_POST['user_name'];
$contact = $_POST['contact_info'];
$line = $_POST['route_line']; // Will be "1", "2", or "3"
$date = $_POST['lost_date'];
$description = $_POST['item_description'];

// Convert to integer
$line_id = (int)$line;

// Insert into database
$stmt = $conn->prepare("INSERT INTO lostitems 
    (user_id,user_name, line_id, item_description, lost_date, contact_info) 
    VALUES (?,?, ?, ?, ?, ?)");

// Bind parameters - note "i" for integer
$stmt->bind_param("isisss",$user_id, $name, $line_id, $description, $date, $contact);

if ($stmt->execute()) {
    header("Location: ../success.html");
    exit();
} else {
    echo "Error: " . $stmt->error;
}


$stmt->close();
$conn->close();
?>
