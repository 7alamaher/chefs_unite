// for frontend checks (e.g. checking if user is logged in when page loads)
<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (isset($_SESSION['user'])) {
    echo json_encode([
        "logged_in" => true,
        "user" => $_SESSION['user']
    ]);
} else {
    echo json_encode([
        "logged_in" => false,
        "message" => "No user is currently logged in."
    ]);
}
