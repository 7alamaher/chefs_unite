<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || !isset($_POST['followed_id'])) {
    header("Location: home.php");
    exit;
}

$currentUsername = $_SESSION['user'];
$followingId = intval($_POST['followed_id']); // user to be followed

// Get current user's user_id
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$stmt->bind_result($followerId);
$stmt->fetch();
$stmt->close();

if (!$followerId || $followerId == $followingId) {
    // Prevent invalid or self-follow
    header("Location: home.php");
    exit;
}

// Insert follow relationship if not already followed
$stmt = $conn->prepare("INSERT IGNORE INTO follows (follower_id, following_id) VALUES (?, ?)");
$stmt->bind_param("ii", $followerId, $followingId);
$stmt->execute();
$stmt->close();

header("Location: home.php");
exit;
?>

