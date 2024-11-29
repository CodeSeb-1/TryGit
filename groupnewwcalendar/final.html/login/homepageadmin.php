<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: sign-in.php"); // Redirect to the login page if not logged in
    exit();
}

// Fetch the fullname from the session
$fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="profilestyle.css">
    <link rel="stylesheet" href="calendar.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet'>
</head>
<body>
<style>
         /* Existing styles remain unchanged */
.calendar-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    margin-bottom: 30px; /* Adds space below the calendar */
    margin-right: 30px; /* Adds space below the calendar */
}

.table-condensed {
    font-size: 14px;
    width: 100%;
    border-collapse: collapse;
}

.table-condensed th, .table-condensed td {
    text-align: center;
    padding: 8px;
     color: #333;
}

.table-condensed th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.table-condensed td {
    border: 1px solid #dee2e6;
}

.table-condensed .muted {
    color: #6c757d;
}

.btn-group .btn {
    font-size: 14px;
    padding: 5px 10px;
}
.table-condensed td.selected {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
/* Adjustments to the time selection layout */
.time-selection-container {
    max-width: 600px;
    margin: 30px auto; /* Adds space above and below the time selection */
    text-align: center;
}

.time-slot-container {
    display: block; /* Stack time slots vertically */
    margin: 10px 0;
}

.time-slot {
    display: block; /* Stack time slot vertically */
    margin-bottom: 15px; /* Space between time slots */
    text-align: left; /* Align label to the left */ 
}

.circle-btn {
    width: 20px;
    height: 20px;
    border: 2px solid #007bff;
    border-radius: 50%;
    background-color: white;
    cursor: pointer;
    margin-right: 10px; /* Add space between button and label */
}

.circle-btn.selected {
    background-color: #007bff;
}

.time-slot label {
    font-size: 16px; /* Adjust label size */
    font-weight: 500; /* Make label a bit bolder */
}

.btn {
    margin-top: 20px;
    padding: 10px 20px;
    font-size: 16px;
}
.prev-month, .next-month {
            font-size: 24px;
            cursor: pointer;
            background-color: transparent;
            border: none;
            color: #3a3b3a;
            padding: 5px;
        }
    </style>
    <div class="body">
        <div class="header">
            <div class="profile-picture-container">
                <img src="https://via.placeholder.com/150" alt="Profile Picture" class="profile-picture">
                <!-- Display the fullname fetched from the session -->
                <div class="profile-name"><?php echo htmlspecialchars($fullname); ?></div>
                <a href="editprof.html">
                    <button class="edit-profile-btn">EDIT</button>
                </a>
            </div>
            <img src="logo.png" alt="Dental Clinic Logo" class="clinic-logo">
        </div>

        <div class="graph-container">
            <div class="graph-box">
                <img src="https://via.placeholder.com/230" alt="Graph" class="graph-image">
                <p class="graph-description">
                    Parang dito nakalagay yung graph kung saan makikita yung percentage nung availability pero hindi pa
                    ito yung kapag pinindot yung sa calendar or pwede na rin din.
                </p>
            </div>
        </div>

        <div class="main-content">
            <div class="navigation-buttons">
                <a href="apphistoryadmin.php"><button class="nav-btn">HISTORY</button></a>
                <a href="pendingappointadmin.php"><button class="nav-btn">NOTIFICATION</button></a>
                <a href="accountsettadmin.php"><button class="nav-btn">SETTINGS</button></a>
            </div>
        </div>

        <div class="appointment-container">
            <div class="appointment-schedule">
                <div class="schedule-title-outside">APPOINTMENT SCHEDULE</div>

                <div class="calendar-navigation">
                    <button class="prev-month" onclick="changeMonth(-1)">&#8249;</button>
                    <button class="next-month" onclick="changeMonth(1)">&#8250;</button>
                </div>

                <!-- Calendar Container -->
                <div id="calendar" class="calendar-container">
                    <table class="table-condensed table-bordered table-striped">
                        <thead>
                            <tr>
                                <th colspan="7" id="monthLabel"></th>
                            </tr>
                            <tr>
                                <th>Su</th>
                                <th>Mo</th>
                                <th>Tu</th>
                                <th>We</th>
                                <th>Th</th>
                                <th>Fr</th>
                                <th>Sa</th>
                            </tr>
                        </thead>
                        <tbody id="calendarDays"></tbody>
                    </table>
                </div>
                <!-- Time Selection -->
                <div class="time-selection-container">
                    <form method="POST" action="">
                        <h3>TIME AVAILABILITY</h3>
                        <div class="time-slot-container">
                            <div class="time-slot">
                                <button type="button" class="circle-btn" onclick="selectTime('09:00-10:30AM')"></button>
                                <label>9:00 - 10:30AM</label>
                            </div>
                            <div class="time-slot">
                                <button type="button" class="circle-btn" onclick="selectTime('10:30-12:00PM')"></button>
                                <label>10:30 - 12:00PM</label>
                            </div>
                            <div class="time-slot">
                                <button type="button" class="circle-btn" onclick="selectTime('01:00-02:30PM')"></button>
                                <label>1:00 - 2:30PM</label>
                            </div>
                            <div class="time-slot">
                                <button type="button" class="circle-btn" onclick="selectTime('02:30-04:00PM')"></button>
                                <label>2:30 - 4:00PM</label>
                            </div>
                        </div>
                        <input type="hidden" name="date" id="selectedDateInput">
                        <input type="hidden" name="time" id="selectedTimeInput">
                        <button type="submit" class="btn" id="bookAppointmentBtn" disabled>Book Appointment</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lgtbtn">
    <a href="logout.php">
        <button class="logout-btn">LOG OUT</button>
    </a>
</div>
    </div>

    <!-- Optional: Include a script for a basic clickable calendar -->
    <script>
        // Variables to keep track of current month and year
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();

        // Function to render the calendar for the current month
        function renderCalendar() {
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate(); // Total days in month
            const firstDay = new Date(currentYear, currentMonth, 1).getDay(); // First day of the month

            let calendarDays = '';
            // Empty cells before the first day of the month
            for (let i = 0; i < firstDay; i++) {
                calendarDays += '<td class="muted"></td>';
            }

            // Day cells
            for (let day = 1; day <= daysInMonth; day++) {
                if ((firstDay + day - 1) % 7 === 0) {
                    calendarDays += '<tr>';
                }
                calendarDays += `<td onclick="selectDate(this)">${day}</td>`;
                if ((firstDay + day) % 7 === 0) {
                    calendarDays += '</tr>';
                }
            }

            // Fill the calendar with the generated days
            document.getElementById('calendarDays').innerHTML = calendarDays;
            document.getElementById('monthLabel').textContent = `${getMonthName(currentMonth)} ${currentYear}`;
        }

        // Function to return the name of the month
        function getMonthName(monthIndex) {
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[monthIndex];
        }

        // Function to handle previous month button
        document.querySelector('.prev-month').addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });

        // Function to handle next month button
        document.querySelector('.next-month').addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });

        // Initialize the calendar when the page loads
        renderCalendar();

        // Handle date selection
        function selectDate(cell) {
            const cells = document.querySelectorAll('.table-condensed td');
            cells.forEach(cell => cell.classList.remove('selected'));
            cell.classList.add('selected');
            document.getElementById('selectedDateInput').value = cell.textContent;
            document.getElementById('bookAppointmentBtn').disabled = false;
        }

        // Handle time selection
        function selectTime(time) {
            const buttons = document.querySelectorAll('.circle-btn');
            buttons.forEach(btn => btn.classList.remove('selected'));
            event.target.classList.add('selected');
            document.getElementById('selectedTimeInput').value = time;
            document.getElementById('bookAppointmentBtn').disabled = false;
        }
    </script>
</body>

</html>
