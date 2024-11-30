<?php
include('connect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: sign-in.php");
    exit();
}

// Fetch the fullname from the session
$fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Guest';

// Get the current month and year (or modify this to get selected month/year dynamically)
if (isset($_GET['month']) && isset($_GET['year'])) {
    $selectedMonth = (int)$_GET['month'];
    $selectedYear = (int)$_GET['year'];
} else {
    // Fallback to current month/year
    $selectedMonth = date('m');
    $selectedYear = date('Y');
}

// Fetch all dates and their checker counts for the selected month and year
$sql = "SELECT date, COUNT(*) as checker_count FROM bookappointment WHERE MONTH(date) = ? AND YEAR(date) = ? GROUP BY date";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $selectedMonth, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

// Initialize unavailable count
$unavailableCount = 0;

// Loop through the result to count unavailable dates
while ($row = $result->fetch_assoc()) {
    $checkerCount = $row['checker_count']; // Number of times a specific date is booked

    // If the checker count is exactly 12, mark this date as unavailable
    if ($checkerCount == 12) {
        $unavailableCount++;
    }
}

// Calculate total days in the selected month
$totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);

// Calculate available days
$availableCount = $totalDaysInMonth - $unavailableCount;

// Calculate percentages
$availablePercentage = $totalDaysInMonth > 0 ? round(($availableCount / $totalDaysInMonth) * 100, 2) : 0;
$unavailablePercentage = 100 - $availablePercentage;

// Close the database connection
$con->close();

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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // Pass the PHP variables to JavaScript
            var availableCount = <?php echo $availableCount; ?>;
            var unavailableCount = <?php echo $unavailableCount; ?>;

            var data = google.visualization.arrayToDataTable([
            ['Task', 'Availability'],
            ['Available', availableCount],
            ['Unavailable', unavailableCount]
            ]);

            var options = {
            title: '',
            backgroundColor: 'transparent',  // Set the background color to transparent
            slices: {
                0: {offset: 0.1}, // Optionally offset the available slice
                1: {offset: 0.1}  // Optionally offset the unavailable slice
            }
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
        }

    </script>
</head>

<body>
    <style>
        /* Existing styles remain unchanged */
        .calendar-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            margin-bottom: 30px;
            /* Adds space below the calendar */
            margin-right: 30px;
            /* Adds space below the calendar */
        }

        .table-condensed {
            font-size: 14px;
            width: 100%;
            border-collapse: collapse;
        }

        .table-condensed th,
        .table-condensed td {
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
            margin: 30px auto;
            /* Adds space above and below the time selection */
            text-align: center;
        }

        .time-slot-container {
            display: block;
            /* Stack time slots vertically */
            margin: 10px 0;
        }

        .time-slot {
            display: block;
            /* Stack time slot vertically */
            margin-bottom: 15px;
            /* Space between time slots */
            text-align: left;
            /* Align label to the left */
        }

        .circle-btn {
            width: 20px;
            height: 20px;
            border: 2px solid #007bff;
            border-radius: 50%;
            background-color: white;
            cursor: pointer;
            margin-right: 10px;
            /* Add space between button and label */
        }

        .circle-btn.selected {
            background-color: #007bff;
        }

        .time-slot label {
            font-size: 16px;
            /* Adjust label size */
            font-weight: 500;
            /* Make label a bit bolder */
        }

        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }

        .prev-month,
        .next-month {
            font-size: 24px;
            cursor: pointer;
            background-color: transparent;
            border: none;
            color: #3a3b3a;
            padding: 5px;
        }

        .circle-btn.disabled {
            background-color: red; /* Red color when disabled */
            color: white; /* White text for contrast */
            cursor: not-allowed; /* Change cursor to indicate it's not clickable */
            border: 2px solid #ff0000; /* Red border for visual emphasis */
        }

    </style>
    <div class="body">
        <div class="header">
            <div class="profile-picture-container">
                <img src="https://via.placeholder.com/150" alt="Profile Picture" class="profile-picture">
                <!-- Display the fullname fetched from the session -->
                <div class="profile-name"><?php echo htmlspecialchars($fullname); ?></div>
                <a href="editprofpatient.php">
                    <button class="edit-profile-btn">EDIT</button>
                </a>
            </div>
            <img src="logo.png" alt="Dental Clinic Logo" class="clinic-logo">
        </div>

        <div class="graph-container">
            <div class="graph-box">
            <div id="piechart" style="width: 900px; height: 500px;"></div>
            </div>
        </div>

        <div class="main-content">
            <div class="navigation-buttons">
                <a href="appointmenthispatient.php"><button class="nav-btn">HISTORY</button></a>
                <a href="notifpatient.php"><button class="nav-btn">NOTIFICATION</button></a>
                <a href="accountsettpatient.php"><button class="nav-btn">SETTINGS</button></a>
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
                    <form id="appointmentForm" method="POST" action="forms.php" onsubmit="return bookAppointment();">
                        <h3>TIME AVAILABILITY</h3>
                        <div class="time-slot-container">
                    <div class="time-slot">
                        <button type="button" class="circle-btn" data-time="09:00-10:30AM" onclick="selectTime('09:00-10:30AM')"></button>
                        <label>9:00 - 10:30AM</label>
                    </div>
                    <div class="time-slot">
                        <button type="button" class="circle-btn" data-time="10:30-12:00PM" onclick="selectTime('10:30-12:00PM')"></button>
                        <label>10:30 - 12:00PM</label>
                    </div>
                    <div class="time-slot">
                        <button type="button" class="circle-btn" data-time="01:00-02:30PM" onclick="selectTime('01:00-02:30PM')"></button>
                        <label>1:00 - 2:30PM</label>
                    </div>
                    <div class="time-slot">
                        <button type="button" class="circle-btn" data-time="02:30-04:00PM" onclick="selectTime('02:30-04:00PM')"></button>
                        <label>2:30 - 4:00PM</label>
                    </div>
                </div>
                        <!-- Hidden inputs to pass data to forms.php -->
                        <input type="hidden" name="date" id="selectedDateInput">
                        <input type="hidden" name="time" id="selectedTimeInput">
                        <input type="hidden" name="monthName" id="monthNameInput">

                        <!-- Book Appointment Button -->
                        <button type="submit" name="bookApointment" class="btn" id="bookAppointmentBtn" disabled onclick="bookAppointment()">Book Appointment</button>

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
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    function updateChartData(month, year) {
        // Use AJAX to get the new data
        fetch(`homepagepatient.php?month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                // Assuming data contains availableCount and unavailableCount:
                drawChart(data.availableCount, data.unavailableCount); // Modify your chart drawing function
            })
            .catch(error => console.error('Error fetching data:', error));
    }

function drawChart(availableCount, unavailableCount) {
    var data = google.visualization.arrayToDataTable([
        ['Task', 'Availability'],
        ['Available', availableCount],
        ['Unavailable', unavailableCount]
    ]);

    var options = {
        title: '',
        backgroundColor: 'transparent',
        slices: {
            0: {offset: 0.1},
            1: {offset: 0.1}
        }
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
}

    // Render the calendar
    function renderCalendar() {
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();

        let calendarDays = '';

        // Add blank cells for days before the 1st of the month
        for (let i = 0; i < firstDay; i++) {
            calendarDays += '<td class="muted"></td>';
        }

        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            if ((firstDay + day - 1) % 7 === 0) calendarDays += '<tr>'; // Start a new row
            calendarDays += `<td onclick="selectDate(this)" data-day="${day}" id="day-${day}">${day}</td>`;
            if ((firstDay + day) % 7 === 0) calendarDays += '</tr>'; // Close the row
        }

        // Update the calendar display
        document.getElementById('calendarDays').innerHTML = calendarDays;
        document.getElementById('monthLabel').textContent = `${getMonthName(currentMonth)} ${currentYear}`;

        // Check availability for each date in the month
        checkMonthAvailability();
    }

    // Get the name of the month
    function getMonthName(monthIndex) {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        return monthNames[monthIndex];
    }

    // Select a date and fetch available time slots
    function selectDate(cell) {
        const day = cell.textContent.trim();
        const month = currentMonth + 1; // Month is 0-indexed
        const year = currentYear;

        const formattedDate = `${year}-${month.toString().padStart(2, '0')}-${day.padStart(2, '0')}`;
        document.getElementById('selectedDateInput').value = formattedDate;

        // Highlight the selected date
        const calendarCells = document.querySelectorAll('#calendarDays td');
        calendarCells.forEach(cell => cell.classList.remove('selected'));
        cell.classList.add('selected');

        
        const buttons = document.querySelectorAll('.circle-btn');
        buttons.forEach(button => button.classList.remove('selected'));
        // Fetch availability for the selected date
        fetchAvailability(formattedDate);
    }

    // Fetch available time slots from the server
    function fetchAvailability(date) {
        fetch(`check_availability.php?date=${date}`)
            .then(response => response.json())
            .then(data => updateTimeSlots(data, date))
            .catch(error => console.error('Error fetching availability:', error));
    }

    // Update time slot buttons based on availability
    function updateTimeSlots(availability, date) {
        const buttons = document.querySelectorAll('.circle-btn');
        let allSlotsUnavailable = true;

        buttons.forEach(button => {
            const time = button.getAttribute('data-time');
            
            // Check if the time slot is available in the availability data
            if (availability[time]) {
                button.disabled = false;  // Enable the time slot button
                button.classList.remove('disabled');  // Remove 'disabled' class for styling
                allSlotsUnavailable = false; // At least one slot is available
            } else {
                button.disabled = true;  // Disable the time slot button
                button.classList.add('disabled');  // Add 'disabled' class for styling
            }
        });

        // Reset selected time when availability changes
        document.getElementById('selectedTimeInput').value = '';
        document.getElementById('bookAppointmentBtn').disabled = allSlotsUnavailable; // Disable booking if all slots are unavailable

        // Change the background color of the date cell based on availability
        changeDateBackgroundColor(date, allSlotsUnavailable);
    }

    // Change the background color of the date cell to red if all slots are unavailable
    function changeDateBackgroundColor(date, allSlotsUnavailable) {
        const day = new Date(date).getDate();
        const dateCell = document.getElementById(`day-${day}`);

        if (allSlotsUnavailable) {
            dateCell.style.backgroundColor = 'red'; // Change background to red if fully booked
            dateCell.style.color = 'white'; // Optional: Change text color to white
        } else {
            dateCell.style.backgroundColor = ''; // Reset background if slots are available
            dateCell.style.color = ''; // Reset text color
        }
    }

    // Check availability for each date in the month
    function checkMonthAvailability() {
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        let availabilityPromises = [];

        // Create an array of promises for each day in the month
        for (let day = 1; day <= daysInMonth; day++) {
            const formattedDate = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;

            const availabilityPromise = fetch(`check_availability.php?date=${formattedDate}`)
                .then(response => response.json())
                .then(data => {
                    const isAvailable = Object.values(data).some(timeSlot => timeSlot); // Check if any time slot is available
                    return { day, isAvailable };
                })
                .catch(error => {
                    console.error('Error fetching availability:', error);
                    return { day, isAvailable: false }; // If there's an error, assume the day is fully booked
                });

            availabilityPromises.push(availabilityPromise);
        }

        // Wait for all the fetch requests to complete
        Promise.all(availabilityPromises)
            .then(results => {
                results.forEach(({ day, isAvailable }) => {
                    const dateCell = document.getElementById(`day-${day}`);
                    if (isAvailable) {
                        dateCell.style.backgroundColor = ''; // No appointment
                        dateCell.style.color = ''; // Reset text color
                    } else {
                        dateCell.style.backgroundColor = 'red'; // Fully booked
                        dateCell.style.color = 'white'; // Optional: Make the text white to improve contrast
                    }
                });
            });
    }

    // Initialize the calendar on page load
    renderCalendar();
    function updateChart(month, year) {
            fetch(`get_month_availability.php?month=${month}&year=${year}`)
                .then(response => response.json())
                .then(data => {
                    drawChart(data.available, data.unavailable);
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }

        function drawChart(available, unavailable) {
            var data = google.visualization.arrayToDataTable([
                ['Task', 'Availability'],
                ['Available', available],
                ['Unavailable', unavailable],
            ]);

            var options = {
                title: '',
                backgroundColor: 'transparent',
                slices: {
                    0: { offset: 0.1 },
                    1: { offset: 0.1 },
                },
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }

        // Call this when changing months
        document.querySelector('.prev-month').addEventListener('click', function () {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
            updateChart(currentMonth + 1, currentYear);
        });

        document.querySelector('.next-month').addEventListener('click', function () {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
            updateChart(currentMonth + 1, currentYear);
        });

        // Initial chart load for current month
        google.charts.setOnLoadCallback(() => {
            updateChart(currentMonth + 1, currentYear);
        });


    // Handle time slot selection
        function selectTime(time) {
        document.getElementById('selectedTimeInput').value = time;
        const buttons = document.querySelectorAll('.circle-btn');
        buttons.forEach(button => button.classList.remove('selected'));
        
        const selectedButton = [...buttons].find(button => button.getAttribute('data-time') === time);
        selectedButton.classList.add('selected');
    }
    function bookAppointment() {
    const selectedDate = document.getElementById('selectedDateInput').value;
    const selectedTime = document.getElementById('selectedTimeInput').value;

    if (!selectedDate) {
        alert("Please select a date.");
        return false; // Prevent form submission
    }

    if (!selectedTime) {
        alert("Please select a time.");
        return false; // Prevent form submission
    }

    // If both date and time are selected, allow form submission
    console.log(`Booking appointment for ${selectedDate} at ${selectedTime}`);
    return true; // Allow form submission
}
    </script>
</body>

</html>