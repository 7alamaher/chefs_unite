<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Include DB connection
require_once 'db.php';


if (!isset($_POST['username'], $_POST['email'], $_POST['password'] , $_POST['confpass'])) {
  echo json_encode(["success" => false,
        "message" => "Missing required fields."]);
  exit;
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confpass'];
$profile_image = null; // or set default if needed

// Check if email already exists
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, 
    "message" => "Email already exists."]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();
// Check password match
if ($password !== $confirm_password) {
    echo json_encode([
        "success" => false,
        "message" => "Passwords do not match."]);
    exit;
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, profile_image) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $password_hash, $profile_image);

if ($stmt->execute()) {
    $user_id = $conn->insert_id;
    echo json_encode([
        "success" => true,
        "message" => "User registered successfully.",
        "user" => [
            "user_id" => $user_id,
            "username" => $username,
            "email" => $email,
            "profile_image" => $profile_image
        ]
    ]);
} else {
    echo json_encode(["success" => false,"message" => "Server error: " . $stmt->error]);
}

$stmt->close();
$conn->close();

?>
