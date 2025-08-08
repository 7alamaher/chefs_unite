//get user profile by ID
<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Include DB connection
require_once 'db.php';

// Read POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if user_id is provided
if (!isset($data['user_id'])) {
    echo json_encode(["message" => "User ID is required."]);
    exit;
}

$user_id = $data['user_id'];

try {
    $stmt = $pdo->prepare("SELECT user_id, username, email, profile_image FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            "message" => "User found.",
            "user" => $user
        ]);
    } else {
        echo json_encode(["message" => "User not found."]);
    }

} catch (PDOException $e) {
    echo json_encode(["message" => "Server error: " . $e->getMessage()]);
}
