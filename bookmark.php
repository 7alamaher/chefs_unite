<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || !isset($_POST['recipe_id'])) {
    header("Location: home.php");
    exit;
}

$currentUsername = $_SESSION['user'];
$recipeId = intval($_POST['recipe_id']);

// Get current user's ID
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

// Insert bookmark if not already bookmarked
$stmt = $conn->prepare("INSERT IGNORE INTO bookmarks (user_id, recipe_id) VALUES (?, ?)");
$stmt->bind_param("ii", $userId, $recipeId);
$stmt->execute();
$stmt->close();

header("Location: recipe.php?id=" . $recipeId);
exit;
?>

