<?php
// Include the database connection file
include('connect.php');
session_start();

// Initialize the login error variable
$login_error = "";

// Check if the form is submitted
if (isset($_POST['email']) && isset($_POST['password'])) {
    // Get the email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // First, check the `patient` table for the user
    $stmt = $con->prepare("SELECT * FROM patient WHERE email=?");
    if (!$stmt) {
        die("Query preparation failed: " . $con->error);
    }

    // Bind the email parameter and execute the query
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists in the `patient` table
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Compare the provided password with the stored password
        if ($password === $user['password']) {
            // Password matches, start the session for patient
            session_start();
            $_SESSION['email'] = $email; // Store email in session
            $_SESSION['fullname'] = $user['fullname']; // Store fullname in session
            header("Location: homepagepatient.php"); // Redirect to the patient homepage
            exit();
        } else {
            // Password is incorrect
            $login_error = "Invalid email or password.";
        }
    } else {
        // No user found in the `patient` table, check the `admin` table
        $stmt = $con->prepare("SELECT * FROM admin WHERE email=?");
        if (!$stmt) {
            die("Query preparation failed: " . $con->error);
        }

        // Bind the email parameter and execute the query
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the admin exists in the `admin` table
        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            // Compare the provided password with the stored password
            if ($password === $admin['password']) {
                // Password matches, start the session for admin
                session_start();
                $_SESSION['email'] = $email; // Store email in session
                $_SESSION['adminid'] = $admin['adminid']; // Store admin ID in session
                $_SESSION['fullname'] = $admin['fullname']; // Store admin's fullname
                header("Location: homepageadmin.php"); // Redirect to the admin homepage
                exit();
            } else {
                // Password is incorrect
                $login_error = "Invalid email or password.";
            }
        } else {
            // No user found with the given email in both tables
            $login_error = "Invalid email or password.";
        }
    }

    // Close the prepared statement and the database connection
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-in Page</title>
    <link rel="stylesheet" href="sign-in.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="left-side"></div>
        <div class="right-side">
            <div class="logo">
                <img src="logo.png" alt="Vanessa Nicolas Dental Clinic Logo">
            </div>

            <h2>Welcome Back!</h2>
            <p class="subtitle">Please login to your account.</p>

            <?php
            // Display login error if it exists
            if (!empty($login_error)) {
                echo "<p style='color: red;'>$login_error</p>";
            }
            ?>

            <!-- Login form -->
            <form action="sign-in.php" method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required value="<?php echo isset($email) ? $email : ''; ?>">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required value="<?php echo isset($password) ? $password : ''; ?>">

                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>

                <div class="forgot-password">
                    <a href="forgotpass.php">Forgot your Password?</a>
                </div>

                <button type="submit">Login</button>
            </form>
            <div class="register-link">
                Don’t have an Account? <a href="sign-up.php">Register Here</a>
            </div>
        </div>
    </div>
</body>
</html>
