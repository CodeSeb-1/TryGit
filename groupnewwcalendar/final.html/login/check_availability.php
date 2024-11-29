<?php
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['date'])) {
    $date = $_GET['date'];

    // Array of all time slots
    $timeSlots = ['09:00-10:30AM', '10:30-12:00PM', '01:00-02:30PM', '02:30-04:00PM'];
    $availability = [];

    foreach ($timeSlots as $time) {
        // Query to count existing appointments for the date and time
        $stmt = $con->prepare("SELECT COUNT(*) as count FROM bookappointment WHERE date = ? AND time = ?");
        $stmt->bind_param("ss", $date, $time);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        // Check if appointments for this slot are less than 3
        $availability[$time] = intval($row['count']) < 3;

        $stmt->close();
    }

    echo json_encode($availability);
    exit();
}
?>
