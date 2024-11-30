<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: sign-in.php"); // Redirect to the login page if not logged in
    exit();
}

// Include database connection
include('connect.php');

// Fetch the logged-in user's email
$email = $_SESSION['email']; // Assuming the user's email is stored in the session

// Query to verify that the email exists in the 'patient' table
$email_query = "SELECT email FROM patient WHERE email = '$email'";
$email_result = mysqli_query($con, $email_query);

if (mysqli_num_rows($email_result) > 0) {
    // Query to fetch the appointments for the logged-in user from the 'bookappointment' table
    $query = "SELECT treatment, age, dentistname, duration , time , date , status FROM bookappointment 
              WHERE email = '$email'"; // Match email directly with the 'email' field in 'bookappointment'
    $result = mysqli_query($con, $query);
    
    // Further processing for displaying appointments
} else {
    // If email not found in the patient table, handle the error (redirect or show a message)
    echo "<p>User not found or unauthorized access.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointment History</title>

  <link rel="stylesheet" href="profilestyle.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
  <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet'>
  <style>
    .notification-appoint {
        max-height: 400px; /* Adjust height as needed */
        overflow-y: auto;  /* Enables vertical scrolling */
        border: 1px solid #ccc; /* Optional: Adds a border around the container */
    }
  </style>
</head>

<body>
  <button class="hamburger-menu" onclick="toggleSidebar()">
    <i class="ri-menu-line"></i>
  </button>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <a href="homepagepatient.html" class="menu-item">
      <i class="ri-home-heart-fill"></i>
      <span>Home</span>
    </a>
    <a href="appointmenthispatient.html" class="menu-item">
      <i class="ri-history-line"></i>
      <span>Appointment History</span>
    </a>
    <a href="notifpatient.html" class="menu-item">
      <i class="ri-notification-fill"></i>
      <span>Notification</span>
    </a>
    <a href="accoutnsettpatient.html" class="menu-item">
      <i class="ri-account-circle-fill"></i>
      <span>Account Settings</span>
    </a>
  </div>

 <!-- Main Content -->
<div class="main-container-appointhiss">
    <div class="notification-box-appoint">
        <div class="header3">
            <i class="ri-history-line"></i>
            <h3>Appointment History</h3>
        </div>
        <div class="notification-appoint">
            <?php
            // Check if there are any results
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <div class="appointment-details">
                <?php $formattedDate = date('F j, Y', strtotime($row['date'])); ?>
                    <div class="appointment-item">
                        <p><strong>Date of Appointment: </strong><?php echo $formattedDate ?> </p>
                    </div>
                    <div class="appointment-item">
                        <p><strong>Type of Treatment:</strong> <?php echo htmlspecialchars($row['treatment']); ?></p>
                    </div>
                    <div class="appointment-item">
                        <p><strong>Age:</strong> <?php echo htmlspecialchars($row['age']); ?></p>
                    </div>
                    <div class="appointment-item">
                        <p><strong>Dentist Name:</strong> <?php echo htmlspecialchars($row['dentistname']); ?></p>
                    </div>
                    <div class="appointment-item">
                        <p><strong>Treatment Duration:</strong> <?php echo htmlspecialchars($row['duration']); ?></p>
                    </div>
                    <div class="appointment-item">
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']);?></p>
                    </div>
                </div>
            <?php 
                }
            } else {
                echo "<p>No appointments found.</p>";
            }
            ?>
        </div>
    </div>
</div>
        
      </div>
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
