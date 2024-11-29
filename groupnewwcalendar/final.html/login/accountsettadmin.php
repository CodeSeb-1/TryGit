<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: sign-in.php");
    exit();
}

// Include database connection file
include('connect.php');

// Initialize message variables
$update_error = "";
$update_success = "";

// Example static code for verification (ideally, this should be dynamic or sent via email/SMS)
$expected_code = "123"; // This can be replaced with a dynamic code generation method

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted form data
    $email = $_SESSION['email']; // Current logged-in email
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $code = $_POST['code']; // Verification code

    // Validate the code
    if ($code !== $expected_code) {
        $update_error = "Invalid verification code!";
    } else {
        // Check if new password and confirm password match
        if ($new_password === $confirm_password) {
            // DO NOT hash the password (storing in plain text, not recommended for security)
            $password = $new_password;
            
            // Prepare the SQL statement
            $stmt = $con->prepare("UPDATE admin SET password=? WHERE email=?");

            if ($stmt === false) {
                $update_error = "Error preparing query: " . $con->error;
            } else {
                // Bind the parameters
                $stmt->bind_param("ss", $password, $email);
                
                // Execute the query
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $update_success = "Password updated successfully!";
                    } else {
                        $update_error = "No changes made. Please check your input.";
                    }
                } else {
                    $update_error = "Error executing query: " . $stmt->error;
                }

                // Close the prepared statement
                $stmt->close();
            }
        } else {
            $update_error = "Passwords do not match!";
        }
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Settings</title>
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
        <a href="accountsettadmin.php" class="menu-item">
            <i class="ri-account-circle-fill"></i>
            <span>Account Settings</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-container-accountsett">
        <div class="settings-box">
            <div class="header">
                <i class="ri-account-circle-fill"></i>
                <h1>Admin Account Settings</h1>
            </div>

            <!-- Display success or error message -->
            <?php if (!empty($update_success)): ?>
                <p style="color: green;"><?php echo $update_success; ?></p>
            <?php elseif (!empty($update_error)): ?>
                <p style="color: red;"><?php echo $update_error; ?></p>
            <?php endif; ?>

            <div class="form-sec1">
                <!-- The Form -->
                <form id="adminUpdateForm" action="accountsettadmin.php" method="POST">
                    <!-- Displaying Current Email - Read-only -->
                    <input type="email" id="admin-Email" name="admin_email" value="<?php echo $_SESSION['email']; ?>" placeholder="Current Email" readonly required>
                    
                    <!-- Change Password Fields -->
                    <input type="password" id="admin-change-password" name="new_password" placeholder="Change Password" required>
                    <input type="password" id="admin-confirm-password" name="confirm_password" placeholder="Confirm Password" required>

                    <!-- CODE field (used for verification) -->
                    <input type="text" id="admin-code" name="code" placeholder="Enter verification code" required>

                    <!-- SEND Button -->
                    <div class="button-container">
                        <button class="send-btn" type="submit">CONFIRM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("visible");
        }
    </script>
</body>

</html>
