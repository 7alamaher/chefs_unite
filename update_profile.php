//allow users to update their username,  password, or profile image.
//email change disallowed for security and clarity

<?php
require 'db.php';
require_once 'session_guard.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "Unauthorized. Please log in."]);
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

// Disallow email change
if (isset($data['email'])) {
    echo json_encode(["message" => "Changing email is not allowed. Please contact support."]);
    exit;
}

// Update allowed fields
$fields = ['username', 'password', 'profile_image'];
$updates = [];
$params = [];

foreach ($fields as $field) {
    if (isset($data[$field])) {
        if ($field == 'password') {
            $updates[] = "$field = ?";
            $params[] = password_hash($data[$field], PASSWORD_DEFAULT);
        } else {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
}

if (empty($updates)) {
    echo json_encode(["message" => "No valid fields to update."]);
    exit;
}

$params[] = $user_id;
$sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt->execute($params)) {
    echo json_encode(["message" => "Profile updated successfully."]);
} else {
    echo json_encode(["message" => "Failed to update profile."]);
}
?>


