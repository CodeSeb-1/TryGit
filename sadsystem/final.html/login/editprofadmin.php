<?php
session_start(); // Start the session

// Include the database connection
include('connect.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: sign-in.php"); // Redirect to login if not logged in
    exit();
}

// Get the logged-in user's email
$user_email = $_SESSION['email'];

// Update the user's data if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = $_POST['full-name'];
    $contactno = str_replace("-", "", $_POST['contact']); // Remove dashes from contact number

    // Prepare the SQL query to update the user's data in the admin table
    $stmt = $con->prepare("UPDATE admin SET fullname=?, contactno=? WHERE email=?");
    if ($stmt) {
        $stmt->bind_param("sss", $fullname, $contactno, $user_email);
        if ($stmt->execute()) {
            echo "<script>alert('Profile updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating profile. Please try again.');</script>";
        }
        $stmt->close();
    } else {
        echo "Error: " . $con->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Admin</title>
    <link rel="stylesheet" href="profilestyle.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
</head>
<body>
    <button class="hamburger-menu" onclick="toggleSidebar()">
        <i class="ri-menu-line"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="admin_dashboard.php" class="menu-item">
            <i class="ri-dashboard-fill"></i>
            <span>Dashboard</span>
        </a>
        <a href="admin_manage_users.php" class="menu-item">
            <i class="ri-user-settings-fill"></i>
            <span>Manage Users</span>
        </a>
        <a href="admin_reports.php" class="menu-item">
            <i class="ri-file-list-fill"></i>
            <span>Reports</span>
        </a>
        <a href="admin_appointments.php" class="menu-item">
            <i class="ri-calendar-check-fill"></i>
            <span>Appointments</span>
        </a>
        <a href="admin_notifications.php" class="menu-item">
            <i class="ri-notification-3-fill"></i>
            <span>Notifications</span>
        </a>
        <a href="accountsettadmin.php" class="menu-item">
            <i class="ri-account-circle-fill"></i>
            <span>Account Settings</span>
        </a>
        <a href="logout.php" class="menu-item">
            <i class="ri-logout-box-r-fill"></i>
            <span>Logout</span>
        </a>
    </div>

    <!-- Main Container -->
    <div class="main-container-editprof">
        <div class="profile-section">
            <div class="profile-picture">
                <i class="ri-camera-fill"></i>
            </div>
            <button class="upload-button">UPLOAD PHOTO</button>
        </div>
        <div class="form-section">
            <form method="POST" action="editprofadmin.php">
                <label for="full-name">Full Name:</label>
                <input type="text" id="full-name" name="full-name" required>

                <label for="contact">Contact Number:</label>
                <input type="text" id="contact" name="contact" required>

                <button type="submit" class="save-button">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("visible");
        }
    </script>
</body>
</html>