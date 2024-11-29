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
    $new_email = $_POST['new_email']; 
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
            $stmt = $con->prepare("UPDATE patient SET email=?, password=? WHERE email=?");

            if ($stmt === false) {
                $update_error = "Error preparing query: " . $con->error;
            } else {
                // Bind the parameters
                $stmt->bind_param("sss", $new_email, $password, $email);
                
                // Execute the query
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $update_success = "Account updated successfully!";
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
    <title>Account Settings</title>
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

    <!-- Main Content -->
    <div class="main-container-accountsett">
        <div class="settings-box">
            <div class="header">
                <i class="ri-account-circle-fill"></i>
                <h1>Account Settings</h1>
            </div>

            <!-- Display success or error message -->
            <?php if (!empty($update_success)): ?>
                <p style="color: green;"><?php echo $update_success; ?></p>
            <?php elseif (!empty($update_error)): ?>
                <p style="color: red;"><?php echo $update_error; ?></p>
            <?php endif; ?>

            <div class="form-sec1">
                <!-- The Form -->
                <form id="accountUpdateForm" action="accountsettpatient.php" method="POST">
                    <!-- Displaying Current Email - Read-only -->
                    <input type="email" id="current-Email" name="current_email" value="<?php echo $_SESSION['email']; ?>" placeholder="Current Email" readonly required>
                    
                    <!-- Change Email -->
                    <input type="email" id="change-Email" name="new_email" placeholder="Change Email" required>
                    
                    <!-- Change Password Fields -->
                    <input type="password" id="change-password" name="new_password" placeholder="Change Password" required>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>

                    <!-- CODE field (used for verification) -->
                    <input type="text" id="code" name="code" placeholder="Enter verification code" required>

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
