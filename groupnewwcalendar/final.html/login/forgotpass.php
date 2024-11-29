<?php
// Include database connection
include('connect.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password length
    if (strlen($new_password) < 8) {
        echo "<script>alert('Password must be at least 8 characters long.');</script>";
    } elseif ($new_password !== $confirm_password) {
        // Check if passwords match
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    } else {
        // Check if email exists in the database
        $stmt = $con->prepare("SELECT * FROM patient WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the password in the database (plain text)
            $update_stmt = $con->prepare("UPDATE patient SET password = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $new_password, $email);
            if ($update_stmt->execute()) {
                echo "<script>alert('Password updated successfully.');</script>";
                header('refresh: 2; url=sign-in.html');
                exit();
            } else {
                echo "<script>alert('Failed to update password. Please try again later.');</script>";
            }
        } else {
            echo "<script>alert('Email does not exist in the system. Please try again.');</script>";
        }

        // Close the prepared statement
        $stmt->close();
    }
}

// Close the database connection
mysqli_close($con);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgotpass.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="left-side"></div>

        <div class="right-side">
            <div class="logo">
                <img src="logo.png" alt="Vanessa Nicolas Dental Clinic Logo">
            </div>

            <h2>Create a New Password</h2>
            <p class="subtitle">Your new password must not be the same as your previous one.</p>

            <form action="" method="post">
                <label for="email">Current Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>

                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new_password" placeholder="Enter new password" required>
                <p class="password-note">Choose a password with at least 8 characters.</p>

                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-enter new password" required>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
