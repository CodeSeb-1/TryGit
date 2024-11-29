<?php
// Database connection
include('connect.php');

// Handle Update (CRUD - Update operation)
if (isset($_POST['status']) && isset($_POST['appointmentid'])) {
    // Get the status and appointment ID from the form
    $status = $_POST['status']; // New status value (Approve, Cancel, Reschedule)
    $appointmentid = $_POST['appointmentid']; // Appointment ID to update

    // Update query
    $query = "UPDATE newappointment SET status = ? WHERE appointmentid = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $appointmentid);  // Bind status and appointment ID

    // Execute the query and handle success/error
    if (mysqli_stmt_execute($stmt)) {
        header("Location: pendingappointadmin.php");  // Redirect back to reflect changes
        exit();
    } else {
        echo "Error updating status: " . mysqli_error($con);
    }
}

// Read - Fetch all pending appointments (CRUD - Read operation)
$result = mysqli_query($con, "SELECT * FROM newappointment WHERE status = 'Approve'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointment Notification</title>
  <link rel="stylesheet" href="pendingappointadmin.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

</head>
<body>

    <button class="hamburger-menu" onclick="toggleSidebar()">
        <i class="ri-menu-line"></i>
    </button>
        <div class="sidebar" id="sidebar">
              <a href="homepageadmin.php" div class="menu-item">
              <i class="ri-home-heart-fill"></i>
        <span>Home</span>
        </a>

      <a href="apphiss.html" div class="menu-item">
          <i class="ri-history-line"></i>
          <span>Appointment History</span>
      </a>

     <a href="notif.html" div class="menu-item">
          <i class="ri-notification-fill"></i>
          <span>Notification History</span>
      </a>

      <a href="accoutnsett.html" div class="menu-item">
          <i class="ri-account-circle-fill"></i>
           <span>Account Settings</span>
      </a>
    </div>

    <div class="logo-container">
       <img src="logo.png" alt="Logo" class="logo">
   </div>
  
    
   <div class="container">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="notification-card">
                <div class="header">
                    <h2>Pending Appointment</h2>
                </div>
                <div class="notification-content">
                    <h3>New Appointment Notice</h3>
                    <p>You have received a new notification.</p>

                    <div class="details">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($row['name']); ?></p>
                        <p><strong>Appointment Date:</strong>//ala pa date</p>
                        <p><strong>Type of Treatment:</strong> <?php echo htmlspecialchars($row['treatment']); ?></p>
                        <p><strong>Dentist Name:</strong> <?php echo htmlspecialchars($row['dentistname']); ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($row['duration']); ?></p> // duratin muna hanggang wala pa ung sa aclendar

                    </div>

                    <div class="buttons">
                        <!-- Approve button -->
                        <form method="POST" action="">
                            <input type="hidden" name="status" value="Complete">
                            <input type="hidden" name="appointmentid" value="<?php echo $row['appointmentid']; ?>">
                            <button type="submit" class="approve">Approve</button>
                        </form>
                        <!-- Cancel button -->
                        <form method="POST" action="">
                            <input type="hidden" name="status" value="Cancel">
                            <input type="hidden" name="appointmentid" value="<?php echo $row['appointmentid']; ?>">
                            <button type="submit" class="cancel">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No pending appointments.</p>
    <?php endif; ?>
</div>
      <script>
        function toggleSidebar() {
          const sidebar = document.getElementById("sidebar");
          sidebar.classList.toggle("visible");
        }
      </script>

</body>
</html>
