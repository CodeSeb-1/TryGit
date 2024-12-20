<?php
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

$date = $_POST['date'];
$time = $_POST['time'];
// Unified form handler
if (isset($_POST['appointNow'])) {
    // Common form inputs
    $fullname = $_SESSION['fullname'];
    $email = $_SESSION['email'];
   

    // Specific inputs for `appointNow`
    $age = isset($_POST['age']) ? $_POST['age'] : null;
    $treatment = isset($_POST['treatment']) ? $_POST['treatment'] : null;
    $duration = isset($_POST['duration']) ? $_POST['duration'] : "1 hour and 30 minutes";
    $dentistname = isset($_POST['dentistname']) ? $_POST['dentistname'] : null;
    $status = "Approved"; // Default status for new appointments
    $checker = "check";  // Default value for checker

    // Validate mandatory fields
    if (empty($date) || empty($time)) {
        $error_message = "Date and Time are required.";
    } elseif ((isset($_POST['appointNow']) && (empty($age) || empty($treatment) || empty($dentistname)))) {
        $error_message = "All fields are required for appointment.";
    } else {
        // Insert into `bookappointment` table
        $stmt = $con->prepare("INSERT INTO bookappointment 
            (name, age, treatment, duration, dentistname, email, status, date, time, checker) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param(
                "sissssssss",
                $fullname,
                $age,
                $treatment,
                $duration,
                $dentistname,
                $email,
                $status,
                $date,
                $time,
                $checker
            );
            if ($stmt->execute()) {
                try {
                    // PHPMailer configuration for email confirmation
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'vanessadentalclinic@gmail.com';
                    $mail->Password = 'ckdk fpcr ovrd wdyj';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('vanessadentalclinic@gmail.com', 'Vanessa Dental Clinic');
                    $mail->addAddress($email, $fullname);

                    $mail->isHTML(true);
                    $mail->Subject = 'Appointment Confirmation';
                    $mail->Body = "Dear $fullname,<br><br>Your appointment has been successfully scheduled.<br>" .
                        "<strong>Date:</strong> $date<br>" .
                        "<strong>Time:</strong> $time<br>" .
                        "Thank you for choosing Vanessa Dental Clinic!";
                    $mail->send();

                    $success_message = "Appointment successfully scheduled! Confirmation email sent.";
                } catch (Exception $e) {
                    $error_message = "Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $error_message = "Error booking appointment: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Query preparation failed: " . $con->error;
        }
        echo"<script> alert('Appointment successfully scheduled! Confirmation email sent.'); window.location.href='homepagepatient.php';</script>";
    }
}
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

              $formattedDate = date('F j, Y', strtotime($date));?>
            <input type="hidden" name="date" value="<?php echo $date; ?>">
            <input type="hidden" name="time" value="<?php echo $time; ?>">
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
   
          <button type="button" class="cancel-btn" onclick="window.location.href='homepagepatient.php';">Cancel</button>
          <button type="submit" name="appointNow" class="appoint-btn">Appoint Now</button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>
