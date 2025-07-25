<?php
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $table = $_POST['table'];
    $id = $_POST['id'];
    $status = $_POST['status'];
    $id_field = $_POST['id_field'];
    
    $stmt = $conn->prepare("UPDATE $table SET status = ? WHERE $id_field = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $success_message = "Status updated successfully!";
    } else {
        $error_message = "Failed to update status.";
    }
    $stmt->close();
    
    // Redirect to avoid form resubmission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus System Database Viewer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --warning: #f72585;
            --danger: #ef233c;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
        }
        .back-button {
  position: absolute;
  top: 30px;
  left: 30px;
  background-color: transparent;
  color: black;
  border: 2px solid black;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 14px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  z-index: 10;
}

.back-button:hover {
  background-color: rgba(255, 255, 255, 0.1);
}
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 1px solid #e1e5eb;
        }
        h1 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        .description {
            color: #6c757d;
            max-width: 800px;
            margin: 0 auto;
        }
        .database-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .section-header {
            background-color: var(--primary);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        .section-header h2 {
            font-size: 1.2rem;
            font-weight: 500;
        }
        .section-header i {
            transition: transform 0.3s ease;
        }
        .section-content {
            padding: 20px;
            display: none;
        }
        .section-content.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e1e5eb;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 500;
            color: var(--dark);
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .empty-message {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }
        .status-form {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .status-select {
            padding: 3px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .status-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 3px 8px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .status-btn:hover {
            background-color: var(--secondary);
        }
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: inline-block;
        }
        .alert-success {
            background-color: var(--success);
            color: white;
        }
        .alert-error {
            background-color: var(--danger);
            color: white;
        }
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
        }
         .status-display {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-right: 5px;
        }
        .status-updating {
            opacity: 0.7;
            position: relative;
        }
        .status-updating::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 16px;
            height: 16px;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        .status-form {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .status-select {
            padding: 3px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .status-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 3px 8px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .status-btn:hover {
            background-color: var(--secondary);
        }
    </style>
</head>
<body>
        <a href="indexV51.php" class="back-button">
            <i class="fas fa-chevron-left"></i> Back
        </a>
    <div class="container">
        <header>
            <h1>Bus System Database Visualizer</h1>
            <p class="description">A simple interface to view and manage all data stored in the bus management system database</p>
        </header>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-error"><?= $error_message ?></div>
        <?php endif; ?>
        
        <?php
        $res = $conn->query("SELECT * FROM users");
        ?>
        <div class="database-section">
            <div class="section-header" onclick="toggleSection('users')">
                <h2><i class="fas fa-users"></i> Registered Users</h2>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="section-content" id="users-content">
                <?php if ($res->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Joined On</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['user_id'] ?></td>
                                <td><?= $row['full_name'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['phone'] ?></td>
                                <td><?= $row['created_at'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="empty-message">No users found.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        $res = $conn->query("SELECT * FROM bus_card_applications");
        ?>
        <div class="database-section">
            <div class="section-header" onclick="toggleSection('applications')">
                <h2><i class="fas fa-id-card"></i> Bus Card Applications</h2>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="section-content" id="applications-content">
                <?php if ($res->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Status</th><th>Applied On</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['phone'] ?></td>
                                <td><?= $row['address'] ?></td>
                                <td>
                                    <form class="status-form" method="post">
                                        <input type="hidden" name="table" value="bus_card_applications">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="id_field" value="id">
                                        <select name="status" class="status-select">
                                            <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Approved" <?= $row['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                            <option value="Rejected" <?= $row['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                        <button type="submit" name="update_status" class="status-btn">Update</button>
                                    </form>
                                </td>
                                <td><?= $row['application_date'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="empty-message">No applications found.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        $res = $conn->query("SELECT * FROM reports");
        ?>
        <div class="database-section">
            <div class="section-header" onclick="toggleSection('reports')">
                <h2><i class="fas fa-exclamation-circle"></i> Incident Reports</h2>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="section-content" id="reports-content">
                <?php if ($res->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr><th>ID</th><th>User ID</th><th>Line</th><th>Type</th><th>Description</th><th>Date/Time</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['report_id'] ?></td>
                                <td><?= $row['user_id'] ?></td>
                                <td><?= $row['line_id'] ?></td>
                                <td><?= $row['report_type'] ?></td>
                                <td><?= $row['prob_desc'] ?></td>
                                <td><?= $row['incident_date'] ?></td>
                                <td>
                                    <form class="status-form" method="post">
                                        <input type="hidden" name="table" value="reports">
                                        <input type="hidden" name="id" value="<?= $row['report_id'] ?>">
                                        <input type="hidden" name="id_field" value="report_id">
                                        <select name="status" class="status-select">
                                            <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Approved" <?= $row['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                            <option value="Rejected" <?= $row['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                            <option value="Resolved" <?= $row['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                        <button type="submit" name="update_status" class="status-btn">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="empty-message">No reports found.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
       $res = $conn->query("SELECT * FROM bus_reviews");
        ?>
        <div class="database-section">
            <div class="section-header" onclick="toggleSection('reviews')">
                <h2><i class="fas fa-star"></i> Bus Line Reviews</h2>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="section-content" id="reviews-content">
                <?php if ($res->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr><th>ID</th><th>User ID</th><th>Line</th><th>Rating</th><th>Comment</th><th>Travel Date</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['review_id'] ?></td>
                                <td><?= $row['user_id'] ?></td>
                                <td><?= $row['line_id'] ?></td>
                                <td><?= str_repeat("★", $row['rating']) ?></td>
                                <td><?= $row['comment'] ?></td>
                                <td><?= $row['travel_date'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="empty-message">No reviews found.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        $res = $conn->query("SELECT * FROM lostitems");
        ?>
        <div class="database-section">
            <div class="section-header" onclick="toggleSection('lostitems')">
                <h2><i class="fas fa-search"></i> Lost Items</h2>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="section-content" id="lostitems-content">
                <?php if ($res->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr><th>ID</th><th>User ID</th><th>Item</th><th>Item Description</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            <?php while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['item_id'] ?></td>
                                    <td><?= isset($row['user_id']) && $row['user_id'] !== null ? $row['user_id'] : '—' ?></td>
                                    <td><?= $row['line_id'] ?></td>
                                    <td><?= $row['item_description'] ?></td>
                                    <td><?= $row['lost_date'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-message"><i class="fas fa-box-open fa-2x" style="margin-bottom: 10px;"></i><p>No lost items have been reported yet</p></div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        $res = $conn->query("SELECT * FROM contactmessages");
        ?>
        <div class="database-section">
            <div class="section-header" onclick="toggleSection('messages')">
                <h2><i class="fas fa-envelope"></i> Contact Messages</h2>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="section-content" id="messages-content">
                <?php if ($res->num_rows > 0): ?>
                    <table>
                       <thead>
                            <tr><th>ID</th><th>User ID</th><th>Subject</th><th>Message</th><th>status</th><th>Received On</th></tr>
                        </thead>
                        <tbody>
                            <?php while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['message_id'] ?></td>
                                    <td><?= isset($row['user_id']) && $row['user_id'] !== null ? $row['user_id'] : '—' ?></td>

                                    <td><?= $row['subject'] ?></td>
                                    <td><?= $row['message'] ?></td>
                                    <td>
                                    <form class="status-form" method="post">
                                        <input type="hidden" name="table" value="reports">
                                        <input type="hidden" name="id" value="<?= $row['status'] ?>">
                                        <input type="hidden" name="id_field" value="status">
                                        <select name="status" class="status-select">
                                            <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Approved" <?= $row['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                            <option value="Rejected" <?= $row['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                            <option value="Resolved" <?= $row['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                        <button type="submit" name="update_status" class="status-btn">Update</button>
                                    </form>
                                </td>
                                    <td><?= $row['created_at'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-message"><i class="fas fa-inbox fa-2x" style="margin-bottom: 10px;"></i><p>No contact messages have been received yet</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleSection(sectionId) {
            const content = document.getElementById(`${sectionId}-content`);
            const icon = content.previousElementSibling.querySelector('i.fa-chevron-down');
            content.classList.toggle('active');
            icon.classList.toggle('fa-rotate-180');
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.section-header').click();
        });
    </script>
</body>
</html>
