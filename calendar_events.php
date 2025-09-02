<?php

require_once '_db.php';

// Start the session to get the logged-in user's ID
session_start();
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, return an empty array
    echo json_encode([]);
    exit;
}

// .events.load() passes start and end as query string parameters by default
$start = $_GET["start"];
$end = $_GET["end"];
$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Modify the query to include filtering by user_id
$stmt = $db->prepare('SELECT * FROM events WHERE user_id = :user_id AND NOT ((end <= :start) OR (start >= :end))');

$stmt->bindParam(':start', $start);
$stmt->bindParam(':end', $end);
$stmt->bindParam(':user_id', $user_id); // Bind the user_id parameter

$stmt->execute();
$result = $stmt->fetchAll();

class Event {
  public $id;
  public $text;
  public $start;
  public $end;
  public $backColor;
}
$events = array();

foreach($result as $row) {
  $e = new Event();
  $e->id = $row['id'];
  $e->text = $row['name'];
  $e->start = $row['start'];
  $e->end = $row['end'];
  $e->backColor = $row['color'];
  $events[] = $e;
}

header('Content-Type: application/json');
echo json_encode($events);

