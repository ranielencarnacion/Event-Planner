<?php
include('_db.php');
header('Content-Type: application/json');

session_start();
$userId = $_SESSION['user_id']; // Assuming session stores the user ID.
$current_time = date('Y-m-d H:i:s'); // Current server time

// Query to fetch reminders that are due and not yet triggered
$sql = "SELECT id, title, reminder_time FROM reminders WHERE user_id = ? AND reminder_time <= ? AND is_triggered = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userId, $current_time);
$stmt->execute();

$result = $stmt->get_result();
$alarms = [];

while ($row = $result->fetch_assoc()) {
    $alarms[] = $row;
}

// Mark the reminders as triggered
if (!empty($alarms)) {
    $update_sql = "UPDATE reminders SET is_triggered = 1 WHERE id IN (" . implode(',', array_column($alarms, 'id')) . ")";
    $conn->query($update_sql);
}

echo json_encode($alarms); // Send the alarms as JSON response.

$stmt->close();
$conn->close();
?>
