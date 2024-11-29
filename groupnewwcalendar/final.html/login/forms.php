<?php
// Database connection
include('connect.php');
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
$mail = new PHPMailer(true);

// Initialize success and error messages
$error_message = "";
$success_message = "";


if(isset($_POST['bookApointment'])) {
  $date = $_POST['date'];
  $time = $_POST['time'];
  $month = $_POST['monthName'];
  $fullname = $_SESSION['fullname']; // Retrieve the name from the session
  $email = $_SESSION['email']; // Retrieve the email from the session

  // Validate and insert into the database
  if (!empty($date) && !empty($time)) {
    
      $stmt = $con->prepare("INSERT INTO booking (date, time) VALUES (?, ?)");
      $stmt->bind_param("ss", $date, $time);
      if ($stmt->execute()) {
        try {
          // PHPMailer Configuration
          $mail->isSMTP();                                            // Send using SMTP
          $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
          $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
          $mail->Username   = 'vanessadentalclinic@gmail.com';        // SMTP username
          $mail->Password   = 'ckdk fpcr ovrd wdyj';                  // SMTP password
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
          $mail->Port       = 465;                                    // TCP port to connect to
          $mail->SMTPDebug  = 0;                                      // Set this to 3 or 4 for more verbose output
          $mail->Debugoutput = 'html';

          // Recipients
          $mail->setFrom('vanessadentalclinic@gmail.com', 'Vanessa Dental Clinic');
          $mail->addAddress($email, $fullname);                       // Add a recipient

          // Content
          $mail->isHTML(true);                                        // Set email format to HTML
          $mail->Subject = 'Appointment Confirmation';
          $mail->Body = 'Dear ' . htmlspecialchars($fullname) . ',<br><br>' . 
                        'Your appointment has been successfully scheduled. Below are the details:<br>' . 
                        '<strong>Your name:</strong> ' . htmlspecialchars($fullname) . '<br>' . 
                        '<strong>Date:</strong> ' . $date . '<br>' .
                        '<strong>Time:</strong> ' . $time . '<br>' .
                        'Thank you for choosing Vanessa Dental Clinic!';
          $mail->AltBody = 'Dear ' . $fullname . ', your appointment has been successfully scheduled. ' . 
                          ' Your name: ' . $fullname;

          // Send the email
          $mail->send();

          // Success message and redirection
          echo "<script>
                  alert('Appointment successfully scheduled! Confirmation email sent.');
                </script>";
        } catch (Exception $e) {
          $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
      } else {
          echo "<script>alert('Error booking appointment: " . $stmt->error . "');</script>";
      }
      $stmt->close();
  } else {
      echo "<script>alert('Date and Time are required.'); window.location.href='homepagepatient.php'; window.history.back();</script>";
  }
}

// Check if the form is submitted
if (isset($_POST['appointNow'])) {
  // Retrieve form data
  $fullname = $_SESSION['fullname']; // Retrieve the name from the session
  $age = $_POST['age'];
  $treatment = $_POST['treatment'];
  $duration = "1 hour and 30 minutes";  // Static value
  $dentistname = $_POST['dentistname'];
  $email = $_SESSION['email']; // Retrieve the email from the session


  // Validate inputs
  if (empty($age) || empty($treatment) || empty($dentistname)) {
    $error_message = "All fields are required.";
  } else {
    // Prepare and execute the query to insert data into the newappointment table
    $stmt = $con->prepare("INSERT INTO newappointment (name, age, treatment, duration, dentistname, email, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
      $status = "Approve";  // Default status
      $stmt->bind_param("sisssss", $fullname, $age, $treatment, $duration, $dentistname, $email, $status);
      if ($stmt->execute()) {
        $success_message = "Appointment successfully scheduled!";
      } else {
        $error_message = "Error scheduling appointment: " . $stmt->error;
      }
      $stmt->close();
    } else {
      $error_message = "Query preparation failed: " . $con->error;
    }
  }
}
// Close the database connection
$con->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Make An Appointment</title>

  <link rel="stylesheet" href="formstyle.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
  <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet'>
</head>

<body>
  <div class="form-container">
    <div class="form">
      <div class="header">
        <i class="ri-calendar-2-line"></i>
        <h1>MAKE AN APPOINTMENT</h1>
      </div>

      <!-- Display error or success messages -->
      <?php
      if (!empty($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
      }
      if (!empty($success_message)) {
        echo "<p style='color: green;'>$success_message</p>";
      }
      ?>

      <!-- Form that posts data -->
      <form method="POST" action="">
        <?php
              $data = $_POST['date'];
              $time = $_POST['time'];

              $formattedDate = date('F j, Y', strtotime($date));
              echo "<strong>Date:</strong> $formattedDate
                    <strong>Time:</strong> $time"; // Output: October 10, 2024
            ?>
        <br><br>
        <div class="form-grid">
          <div class="form-group">
            <label for="name">Name of Client:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['fullname']); ?>" readonly>
          </div>
          <div class="form-group">
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>
          </div>
          <div class="form-group">
            <label for="treatment">Type of Treatment:</label>
            <select id="treatment" name="treatment" required>
              <option value="" disabled selected>Select Option</option>
              <option value="Tooth Removal">Tooth Removal</option>
              <option value="Teeth Cleaning">Teeth Cleaning</option>
              <option value="Brace Adjustment">Brace Adjustment</option>
              <option value="Dental Implant">Dental Implant</option>
              <option value="Crown">Crown</option>
              <option value="Veneers">Veneers</option>
              <option value="Others">Others</option>
            </select>
          </div>
          <div class="form-group">
            <label for="duration">Time Duration:</label>
            <input type="text" id="duration" name="duration" value="1 hour and 30 minutes" readonly>
          </div>
          <div class="form-group">
            <label for="dentistname">Dentist Name:</label>
            <select id="dentistname" name="dentistname" required>
              <option value="" disabled selected>Select Option</option>
              <option value="Dr. Vanessa Nicolas">Dr. Vanessa Nicolas</option>
              <option value="Dr. Nestine Basilio">Dr. Nestine Basilio</option>
              <option value="Dr. Anne Sioson">Dr. Anne Sioson</option>
            </select>
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
          </div>
        </div>

        <div class="buttons">
          <a href="homepagepatient.php">Back</a>
          <button type="reset" class="cancel-btn">Cancel</button>
          <button type="submit" name="appointNow" class="appoint-btn">Appoint Now</button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>
