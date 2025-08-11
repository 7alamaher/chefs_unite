<?php
session_start();
require_once 'db.php';

// Must be logged in to like
if (!isset($_SESSION['username'])) {
    die("You must be logged in to like a recipe.");
}

// Get recipe_id from POST
$recipeId = $_POST['recipe_id'] ?? null;
$action   = $_POST['action'] ?? null;

if (!$recipeId) {
    die("Recipe ID missing.");
}

// Get current user's ID
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

if (!$userId) {
    die("User not found.");
}

if ($action === 'like') {
    // Add like
    $stmt = $conn->prepare("INSERT IGNORE INTO likes (recipe_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $recipeId, $userId);
    $stmt->execute();
    $stmt->close();
} elseif ($action === 'unlike') {
    // Remove like
    $stmt = $conn->prepare("DELETE FROM likes WHERE recipe_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $recipeId, $userId);
    $stmt->execute();
    $stmt->close();
} else {
    die("Invalid action.");
}

// Redirect back to the referring page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
