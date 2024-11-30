<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: sign-in.php");
    exit();
}

// Include database connection and PHPMailer
include('connect.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

// Initialize messages
$update_error = "";
$update_success = "";
$verification_code_sent = isset($_SESSION['verification_code']); // Check if verification code exists in session

// Initialize form data variables
$new_password = "";
$confirm_password = "";
$code = "";

// Handle Send Code functionality
if (isset($_POST['send_code'])) {
    // Retain values in form fields
    $new_password = $_POST['new_password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";
    $code = $_POST['code'] ?? "";

    try {
        $email = $_SESSION['email'];  // Send verification code to current email
        $fullname = "Admin"; // Replace with actual name if needed
        $verification_code = rand(100000, 999999);  // Generate a 6-digit random code

        // PHPMailer setup using details from the other code
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vanessadentalclinic@gmail.com'; // Your email
        $mail->Password = 'ckdk fpcr ovrd wdyj'; // Your email app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('vanessadentalclinic@gmail.com', 'Vanessa Dental Clinic');
        $mail->addAddress($email, $fullname);  // Send to the logged-in admin's email

        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = 'Dear ' . htmlspecialchars($fullname) . ',<br><br>' . 
            'Your verification code is: <strong>' . $verification_code . '</strong><br><br>' . 
            'Please use this code to confirm your password update.<br><br>' . 
            'Thank you!';
        $mail->AltBody = 'Your verification code is: ' . $verification_code;

        $mail->send();
        $_SESSION['verification_code'] = $verification_code;  // Store the code in the session
        $verification_code_sent = true;
        $update_success = "Verification code sent to your email!";
    } catch (Exception $e) {
        $update_error = "Could not send verification code. Error: {$mail->ErrorInfo}";
    }
}

// Handle password update
if (isset($_POST['confirm_update'])) {
    $email = $_SESSION['email'];  // Current logged-in email
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $code = $_POST['code'];  // Verification code

    // Validate the code
    if (isset($_SESSION['verification_code']) && $code == $_SESSION['verification_code']) {
        if ($new_password === $confirm_password) {
            $password = $new_password;
            
            // Prepare the SQL statement to update only the password
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
                        
                        // Clear POST data for password fields only after successful update
                        unset($_POST['new_password']);
                        unset($_POST['confirm_password']);
                        unset($_POST['code']);
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
    } else {
        $update_error = "Invalid verification code!";
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
                    <input type="password" id="admin-change-password" name="new_password" value="<?php echo htmlspecialchars($new_password); ?>" placeholder="Change Password" required>
                    <input type="password" id="admin-confirm-password" name="confirm_password" value="<?php echo htmlspecialchars($confirm_password); ?>" placeholder="Confirm Password" required>

                    <!-- CODE field (used for verification) -->
                    <input type="text" id="admin-code" name="code" value="<?php echo htmlspecialchars($code); ?>" placeholder="Enter verification code">

                    <!-- SEND Button -->
                    <div class="button-container">
                        <button class="send-btn" type="submit" name="send_code">Send Code</button><br><br>

                        <!-- CONFIRM Button (Only appear if code was sent) -->
                        <?php if ($verification_code_sent): ?>
                            <button class="send-btn" type="submit" name="confirm_update">Confirm Update</button>
                        <?php endif; ?>
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