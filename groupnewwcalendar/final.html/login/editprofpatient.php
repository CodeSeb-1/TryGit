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

    // Prepare the SQL query to update the user's data
    $stmt = $con->prepare("UPDATE patient SET fullname=?, contactno=? WHERE email=?");
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
    <title>Edit Profile</title>
    <link rel="stylesheet" href="profilestyle.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet'>
</head>
<body>
    <button class="hamburger-menu" onclick="toggleSidebar()">
        <i class="ri-menu-line"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="homepagepatient.php" class="menu-item">
            <i class="ri-home-heart-fill"></i>
            <span>Home</span>
        </a>
        <a href="appointmenthispatient.php" class="menu-item">
            <i class="ri-history-line"></i>
            <span>Appointment History</span>
        </a>
        <a href="notifpatient.php" class="menu-item">
            <i class="ri-notification-fill"></i>
            <span>Notification</span>
        </a>
        <a href="accountsettpatient.php" class="menu-item">
            <i class="ri-account-circle-fill"></i>
            <span>Account Settings</span>
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
            <form method="POST" action="editprofpatient.php">
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