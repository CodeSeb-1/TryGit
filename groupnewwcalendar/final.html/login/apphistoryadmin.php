<?php
// Database connection
include('connect.php');

// Fetch all appointment history from the database
$query = "SELECT name, duration FROM newappointment WHERE status = 'Complete'";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointment History</title>
  <link rel="stylesheet" href="apphistoryadmin.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

  <button class="hamburger-menu" onclick="toggleSidebar()">
      <i class="ri-menu-line"></i>
  </button>

  <div class="sidebar" id="sidebar">
      <a href="homepageadmin.html" class="menu-item">
          <i class="ri-home-heart-fill"></i>
          <span>Home</span>
      </a>
      <a href="apphistoryadmin.html" class="menu-item">
          <i class="ri-history-line"></i>
          <span>Appointment History</span>
      </a>
      <a href="pendingappointadmin.html" class="menu-item">
          <i class="ri-notification-fill"></i>
          <span>Pending Notification</span>
      </a>
      <a href="accountsettadmin.html" class="menu-item">
          <i class="ri-account-circle-fill"></i>
          <span>Account Settings</span>
      </a>
  </div>

  <div class="logo-container">
      <img src="logo.png" alt="Clinic Logo" class="logo">
  </div>

  <div class="container">
      <h2>Appointment History</h2>
      <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <div class="history-card">
                  <div class="history-content">
                      <div class="history-item">
                          <h3>New appointment notice</h3>
                          <p>You have received a new notification.</p>
                          <div class="details">
                              <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
                              <p><strong>Date:</strong>//ala pa date</p>
                              <p><strong>Time:</strong> <?php echo $row['duration']; ?></p> //duration muna gang wala pa
                          </div>
                      </div>
                      <br>
                  </div>
              </div>
          <?php endwhile; ?>
          <?php else: ?>
        <!-- Default message when no appointment is found -->
        <div class="history-card">
            <div class="history-content">
                <div class="history-item">
                    <h3>No Appointment Notices</h3>
                    <p>No appointments are currently available.</p>
                    <div class="details">
                        <p><strong>Name:</strong> [Client Name]</p>
                        <p><strong>Appointment Date:</strong> [Date]</p>
                        <p><strong>Time:</strong> [Time]</p>
                    </div>
                </div>
            </div>
        </div>
      <?php endif; ?>

      <!-- Back Button -->
      <button class="back-button" onclick="window.location.href='coverpage.html';">Back</button>
  </div>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("visible");
    }
  </script>

</body>
</html>
