<?php
// Include database connection
include('connect.php');

// Initialize error and success messages
$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from the form
    $fullname = $_POST['fullname'];
    $contactno = $_POST['contactno'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate form inputs
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if the email already exists
        $stmt = $con->prepare("SELECT email FROM patient WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Email is already registered. Please use a different email.";
        } else {
            // Hash the password (recommended for security)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $con->prepare("INSERT INTO patient (fullname, contactno, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $contactno, $email, $hashed_password);

            if ($stmt->execute()) {
                $success_message = "Account created successfully! <a href='sign-in.php'>Sign in here</a>";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the connection
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>
    <link rel="stylesheet" href="sign-up.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="signup-form">
            <img src="logo.png" alt="Clinic Logo" class="logo"> 
            <h2>Sign Up</h2>
            <p>Already have an account? <a href="sign-in.php">Sign in Here</a></p>

            <?php
            // Display success or error message
            if (isset($success_message)) {
                echo "<p style='color: green;'>$success_message</p>";
            }
            if (isset($error_message)) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            ?>

            <!-- Submit the form data to the same file (sign-up.php) -->
            <form action="sign-up.php" method="POST">
                <input type="text" name="fullname" placeholder="Full Name" required value="<?php echo isset($fullname) ? $fullname : ''; ?>">
                <input type="text" name="contactno" placeholder="Contact Number" required value="<?php echo isset($contactno) ? $contactno : ''; ?>">
                <input type="email" name="email" placeholder="Email" required value="<?php echo isset($email) ? $email : ''; ?>">
                <input type="password" name="password" placeholder="Enter your Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                
                <div style="margin-top: 10px;">
                    <input type="checkbox" id="agree-checkbox" required>
                    <label for="agree-checkbox">I agree to the <a href="#" id="terms-link">Terms and Conditions</a></label>
                </div>
                
                <button type="submit">Sign Up</button>
            </form>
        </div>

        <div class="clinic-image">
            <a href="coverpage.html" class="back-button">Back to Home →</a>
        </div>
    </div>

    <script src="sign-up.js"></script>
</body>
</html>
