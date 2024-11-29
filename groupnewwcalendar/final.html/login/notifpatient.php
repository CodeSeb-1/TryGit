<?php
// Start the session to access session variables
session_start();

// Include database connection
include('connect.php');

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: sign-in.php"); // Redirect to login if the user is not logged in
    exit();
}

// Fetch the logged-in user's email from the session
$email = $_SESSION['email']; // Assuming the user's email is stored in the session

// Query to fetch the appointment history for the logged-in user
$query = "SELECT duration, treatment, status FROM bookappointment 
          WHERE (status = 'Approve' OR status = 'Cancelled') 
          AND email = '$email'"; // Match email directly with the stored email

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notification History</title>

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
    <a href="homepagepatient.html" class="menu-item">
      <i class="ri-home-heart-fill"></i>
      <span>Home</span>
    </a>
    <a href="appointmenthispatient.php" class="menu-item">
      <i class="ri-history-line"></i>
      <span>Appointment History</span>
    </a>
    <a href="notificationhis.html" class="menu-item">
      <i class="ri-notification-fill"></i>
      <span>Notification History</span>
    </a>
    <a href="accoutnsettpatient.html" class="menu-item">
      <i class="ri-account-circle-fill"></i>
      <span>Account Settings</span>
    </a>
  </div>

  <!-- Main Content -->
  <div class="main-container-notificationhis">
    <div class="notification-box">
      <div class="header2">
        <i class="ri-notification-fill"></i>
        <h2>Notification History</h2>
      </div>
      <div class="notifications">
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="notification success">
              <p>Successful Appointment</p><br>
              <p><strong>Date:</strong> //wala padate
              <p><strong>Time:</strong> <?php echo $row['duration']; ?></p>//duration muna gang ala pa tas dapat makikita nya lang ung mga history nung sarili nya
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="notification">
            <p>No notifications available.</p>
          </div>
        <?php endif; ?>
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
