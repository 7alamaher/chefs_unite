<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Destroy the session
$_SESSION = [];            // Clear session variables
session_destroy();         // Destroy the session

echo json_encode([
    "message" => "Logout successful."
]);
