<?php
session_start();
require 'db_connect.php';

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

// Delete the bookmark
$stmt = $conn->prepare("DELETE FROM bookmarks WHERE user_id = ? AND recipe_id = ?");
$stmt->bind_param("ii", $userId, $recipeId);
$stmt->execute();
$stmt->close();

header("Location: recipe.php?id=" . $recipeId);
exit;
?>
