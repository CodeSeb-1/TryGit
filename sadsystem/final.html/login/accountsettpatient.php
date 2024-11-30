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

// Handle Send Code functionality
if (isset($_POST['send_code'])) {
    try {
        $email = $_SESSION['email'];
        $fullname = "Patient"; // Replace with actual patient's name if available
        $verification_code = rand(100000, 999999);  // Generate a 6-digit random code

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vanessadentalclinic@gmail.com';
        $mail->Password = 'ckdk fpcr ovrd wdyj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('vanessadentalclinic@gmail.com', 'Vanessa Dental Clinic');
        $mail->addAddress($email, $fullname);  // Send to the logged-in patient's email

        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = 'Dear ' . htmlspecialchars($fullname) . ',<br><br>' . 
            'Your verification code is: <strong>' . $verification_code . '</strong><br><br>' . 
            'Please use this code to confirm your email and password update.<br><br>' . 
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

// Handle password and email update
if (isset($_POST['confirm_update'])) {
    $email = $_SESSION['email'];  // Current logged-in email
    $new_email = $_POST['new_email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $code = $_POST['code'];  // Verification code

    // Validate the code
    if (isset($_SESSION['verification_code']) && $code == $_SESSION['verification_code']) {
        if ($new_password === $confirm_password) {
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
                        $_SESSION['email'] = $new_email;  // Update session with new email
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

    // After successful update, clear POST data for email and password fields
    if (!empty($update_success)) {
        // Clear POST data for email and password fields
        unset($_POST['new_email']);
        unset($_POST['new_password']);
        unset($_POST['confirm_password']);
        unset($_POST['code']);
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

                    <!-- Change Email (Preserve input if already set) -->
                    <input type="email" id="change-Email" name="new_email" placeholder="Change Email" value="<?php echo isset($_POST['new_email']) ? $_POST['new_email'] : ''; ?>" required>

                    <!-- Change Password Fields (Preserve input if already set) -->
                    <input type="password" id="change-password" name="new_password" placeholder="Change Password" value="<?php echo isset($_POST['new_password']) ? $_POST['new_password'] : ''; ?>" required>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" value="<?php echo isset($_POST['confirm_password']) ? $_POST['confirm_password'] : ''; ?>" required>

                    <!-- Verification Code Field (Display if Send Code was clicked) -->
                    <?php if ($verification_code_sent): ?>
                        <input type="text" id="code" name="code" placeholder="Enter verification code" value="<?php echo isset($_POST['code']) ? $_POST['code'] : ''; ?>">
                    <?php endif; ?>

                    <!-- SEND Code Button -->
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