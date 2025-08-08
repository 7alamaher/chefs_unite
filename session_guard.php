<?php
// session_guard.php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// If the user is not logged in or missing user_id, block access
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode([
        "success" => false,
        "message" => "Access denied. User is not properly authenticated."
    ]);
    exit;
}
?>
