<?php
require_once '_db.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an error response
    header('Content-Type: application/json');
    echo json_encode(['result' => 'Error', 'message' => 'User not logged in']);
    exit;
}

// Read raw input JSON payload
$json = file_get_contents('php://input');
$params = json_decode($json);

// Validate the received data
if (
    !isset($params->id) || 
    !isset($params->text) || 
    !isset($params->start) || 
    !isset($params->end)
) {
    header('Content-Type: application/json');
    echo json_encode(['result' => 'Error', 'message' => 'Missing parameters']);
    exit;
}

// Update the event in the database
$updateQuery = "UPDATE events SET name = :name, start = :start, end = :end WHERE id = :id AND user_id = :user_id";

try {
    $stmt = $db->prepare($updateQuery);

    // Bind the parameters safely
    $stmt->bindParam(':id', $params->id);
    $stmt->bindParam(':start', $params->start);
    $stmt->bindParam(':end', $params->end);
    $stmt->bindParam(':name', $params->text);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);

    $stmt->execute();

    // Fetch the updated event to send it back
    $selectQuery = "SELECT id, name, start, end FROM events WHERE id = :id";
    $selectStmt = $db->prepare($selectQuery);
    $selectStmt->bindParam(':id', $params->id);
    $selectStmt->execute();
    $updatedEvent = $selectStmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode([
        'result' => 'OK',
        'message' => 'Event updated successfully',
        'event' => $updatedEvent,
    ]);

} catch (PDOException $e) {
    // Handle database exceptions gracefully
    header('Content-Type: application/json');
    echo json_encode(['result' => 'Error', 'message' => $e->getMessage()]);
    exit;
}
