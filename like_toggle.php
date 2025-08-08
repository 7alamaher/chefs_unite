<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user']) || !isset($_POST['recipe_id'], $_POST['action'])) {
    header("Location: login.php");
    exit;
}

$currentUsername = $_SESSION['user'];
$recipeId = intval($_POST['recipe_id']);
$action = $_POST['action'];

// Get current user ID
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

if (!$userId) {
    // Could not find user
    header("Location: login.php");
    exit;
}

// Perform like action
if ($action === 'like') {
    $stmt = $conn->prepare("INSERT IGNORE INTO likes (user_id, recipe_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $recipeId);
    $stmt->execute();
    $stmt->close();
} elseif ($action === 'unlike') {
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->bind_param("ii", $userId, $recipeId);
    $stmt->execute();
    $stmt->close();
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
