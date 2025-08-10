<?php
require 'session_guard.php'; // Protect backend
require 'db.php';

if (!isset($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "Recipe ID missing"]);
    exit;
}

$recipe_id = intval($_GET['id']);
$user_id = $_SESSION['user']['id']; // Logged-in user

// Check ownership
$sql = "SELECT id FROM recipes WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Unauthorized or recipe not found"]);
    exit;
}

// Delete recipe
$sql = "DELETE FROM recipes WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipe_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Recipe deleted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error deleting recipe"]);
}
?>
