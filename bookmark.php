
<?php
session_start();
require 'db.php';

$currentUsername = $_SESSION['username'] ?? null;
if (!$currentUsername) {
    header("Location: SignIn.php");
    exit();
}

// Make sure recipe_id is provided
if (!isset($_REQUEST['recipe_id']) || !is_numeric($_REQUEST['recipe_id'])) {
    die("Recipe ID missing or invalid.");
}

$recipeId = intval($_REQUEST['recipe_id']);

// Get current user's ID
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

// Check if this recipe belongs to the current user
$stmt = $conn->prepare("SELECT user_id FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$stmt->bind_result($recipeOwnerId);
$stmt->fetch();
$stmt->close();

if ($recipeOwnerId == $userId) {
    die("You cannot bookmark your own recipe.");
}

// Insert bookmark if not already bookmarked
$stmt = $conn->prepare("INSERT IGNORE INTO bookmarks (user_id, recipe_id) VALUES (?, ?)");
$stmt->bind_param("ii", $userId, $recipeId);
$stmt->execute();
$stmt->close();

// Redirect back
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
