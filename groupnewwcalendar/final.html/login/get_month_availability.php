<?php
include('connect.php');

// Get selected month and year from the AJAX request
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Fetch unavailable days count
$sql = "SELECT date, COUNT(*) as bookappointment FROM booking WHERE MONTH(date) = ? AND YEAR(date) = ? GROUP BY date";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $selectedMonth, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

$unavailableCount = 0;
while ($row = $result->fetch_assoc()) {
    if ($row['checker_count'] == 12) {
        $unavailableCount++;
    }
}

$totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
$availableCount = $totalDaysInMonth - $unavailableCount;

$response = [
    'available' => $availableCount,
    'unavailable' => $unavailableCount,
];

header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$con->close();
?>
