//get user profile by ID
<?php

require_once session_guard.php; // Ensure user is logged in
require_once 'db.php';

$user_id = $_SESSION['user']['id'];

$sql = "SELECT username, created_at, country FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "username" => $row['username'],
        "joined" => date("F j, Y", strtotime($row['created_at'])),
        "country" => $row['country']
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "User not found"
    ]);
}