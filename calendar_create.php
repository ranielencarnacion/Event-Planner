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
    !isset($params->text) || 
    !isset($params->start) || 
    !isset($params->end)
) {
    header('Content-Type: application/json');
    echo json_encode(['result' => 'Error', 'message' => 'Missing parameters']);
    exit;
}

// Insert the event into the database
$insertQuery = "INSERT INTO events (name, start, end, user_id) VALUES (:name, :start, :end, :user_id)";

try {
    $stmt = $db->prepare($insertQuery);

    // Bind the parameters safely
    $stmt->bindParam(':start', $params->start);
    $stmt->bindParam(':end', $params->end);
    $stmt->bindParam(':name', $params->text);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);

    $stmt->execute();

    // Prepare the response
    class Result {
        public $id;
        public $result;
        public $message;
    }

    $response = new Result();
    $response->result = 'OK';
    $response->id = $db->lastInsertId();
    $response->message = 'Created with ID: ' . $db->lastInsertId();

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    // Handle database exceptions gracefully
    header('Content-Type: application/json');
    echo json_encode(['result' => 'Error', 'message' => $e->getMessage()]);
    exit;
}
