<?php
session_start();
require_once 'db.php';

// Must be logged in
if (!isset($_SESSION['username'])) {
    die("You must be logged in to remove a bookmark.");
}

$recipeId = $_POST['recipe_id'] ?? null;

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

// Remove bookmark
$stmt = $conn->prepare("DELETE FROM bookmarks WHERE recipe_id = ? AND user_id = ?");
$stmt->bind_param("ii", $recipeId, $userId);
$stmt->execute();
$stmt->close();

// Redirect back
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
