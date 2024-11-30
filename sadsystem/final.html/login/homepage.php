<?php
// Database connection
include('connect.php'); // Ensure this file has the correct connection

// Handle form submission for booking an appointment
if (isset($_POST['date']) && isset($_POST['time']) && !empty($_POST['time'])) {
    $date = $_POST['date'];  // Get the selected date
    $time = $_POST['time'];  // Get the selected time

    // Check if the date and time already exist in the database
    $checkSql = "SELECT * FROM notification WHERE date = '$date' AND time = '$time'";
    $result = $con->query($checkSql);

    if ($result->num_rows > 0) {
        // Date and time already booked
        $message = "Appointment already booked for " . $date . " at " . $time;
        $status = "error";
    } else {
        // Insert the appointment
        $sql = "INSERT INTO notification (date, time) VALUES ('$date', '$time')";
        
        if ($con->query($sql) === TRUE) {
            $message = "Appointment booked successfully for " . $date . " at " . $time;
            $status = "success";
        } else {
            $message = "Error: " . $sql . "<br>" . $con->error;
            $status = "error";
        }
    }
} else {
    $message = "Please select both a date and time.";
    $status = "error";
}
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
    <div class="body">
        <div class="header">
            <div class="profile-picture-container">
                <img src="https://via.placeholder.com/150" alt="Profile Picture" class="profile-picture">
                <div class="profile-name">FULL NAME</div>
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
                <a href="#"><button class="nav-btn">HISTORY</button></a>
                <a href="#"><button class="nav-btn">APPOINTMENT</button></a>
                <a href="#"><button class="nav-btn">SETTINGS</button></a>
            </div>
        </div>

        <div class="appointment-container">
            <div class="appointment-schedule">
                <div class="schedule-title-outside">APPOINTMENT SCHEDULE</div>

                <!-- Calendar Navigation Buttons -->
                <div class="calendar-navigation">
                    <button class="prev-month" onclick="changeMonth(-1)">&#8249;</button>
                    <button class="next-month" onclick="changeMonth(1)">&#8250;</button>
                </div>

                <!-- Calendar Container -->
                <div id="calendar" class="calendar-container"></div>
            </div>

            <!-- Time and Button Section -->
            <div class="time-selection-container">
                <label for="timeSelect">Select Time:</label>
                <select id="timeSelect">
                    <option value="" disabled selected>Choose a time</option>
                    <option value="09:00">09:00 AM</option>
                    <option value="10:00">10:00 AM</option>
                    <option value="11:00">11:00 AM</option>
                    <option value="01:00">01:00 PM</option>
                    <option value="02:00">02:00 PM</option>
                    <option value="03:00">03:00 PM</option>
                </select>
                <form method="POST" action="">
                    <button type="submit" class="btn" id="bookAppointmentBtn" disabled>Book Appointment</button>
                    <input type="hidden" name="date" id="selectedDateInput">
                    <input type="hidden" name="time" id="selectedTimeInput">
                </form>
            </div>
        </div>

        <div class="lgtbtn">
            <a href="#">
                <button class="logout-btn">LOG OUT</button>
            </a>
        </div>
    </div>

    <script>
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();

        // Function to generate the calendar
        function generateCalendar() {
            const monthYear = document.getElementById('calendar');
            monthYear.innerHTML = `<h2>${monthNames[currentMonth]} ${currentYear}</h2>`;

            let calendarHtml = '<table class="calendar-table"><thead><tr>';
            for (let i = 0; i < daysOfWeek.length; i++) {
                calendarHtml += `<th>${daysOfWeek[i]}</th>`;
            }
            calendarHtml += '</tr></thead><tbody>';

            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            let day = 1;

            for (let i = 0; i < 6; i++) {
                let row = '<tr>';
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < firstDay) {
                        row += '<td></td>';
                    } else if (day <= daysInMonth) {
                        row += `<td class="calendar-day" data-day="${day}" onclick="selectDate(event)">${day}</td>`;
                        day++;
                    } else {
                        row += '<td></td>';
                    }
                }
                row += '</tr>';
                calendarHtml += row;
                if (day > daysInMonth) break;
            }
            calendarHtml += '</tbody></table>';

            monthYear.innerHTML += calendarHtml;
        }

        // Function to handle date selection
        function selectDate(event) {
            const selectedDate = event.target;
            const allDays = document.querySelectorAll('.calendar-day');
            allDays.forEach(day => day.classList.remove('selected'));

            selectedDate.classList.add('selected');
            document.getElementById('selectedDateInput').value = `${currentYear}-${currentMonth + 1}-${selectedDate.textContent}`;
            document.getElementById('bookAppointmentBtn').disabled = false; // Enable button after selecting date
        }

        // Function to confirm time selection
        function confirmTime() {
            const selectedTime = document.getElementById("timeSelect").value;
            if (selectedTime) {
                document.getElementById("selectedTimeInput").value = selectedTime;
                document.getElementById("bookAppointmentBtn").disabled = false; // Enable button if time is selected
            } else {
                alert("Please select a time.");
                document.getElementById("bookAppointmentBtn").disabled = true; // Disable button if no time selected
            }
        }

        // Attach the confirmTime function to the select element
        document.getElementById("timeSelect").addEventListener('change', confirmTime);

        // Validate before form submission
        document.getElementById("bookAppointmentBtn").addEventListener("click", function(event) {
            const selectedTime = document.getElementById("timeSelect").value;
            const selectedDate = document.getElementById("selectedDateInput").value;

            // Check if time and date are selected
            if (!selectedTime) {
                alert("Please select a time.");
                event.preventDefault();  // Prevent form submission
            } else if (!selectedDate) {
                alert("Please select a date.");
                event.preventDefault();  // Prevent form submission
            }
        });

        // Function to change the month
        function changeMonth(offset) {
            currentMonth += offset;

            // If the month is less than 0, go to the previous year (December)
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }

            // If the month is greater than 11, go to the next year (January)
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }

            generateCalendar();
        }

        // Generate initial calendar
        generateCalendar();
    </script>
</body>
</html>
