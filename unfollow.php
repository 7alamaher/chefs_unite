<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || !isset($_POST['followed_id'])) {
    header("Location: home.php");
    exit;
}

$currentUsername = $_SESSION['user'];
$followingId = intval($_POST['followed_id']); // user to be unfollowed

// Get current user's user_id
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$stmt->bind_result($followerId);
$stmt->fetch();
$stmt->close();

if (!$followerId || $followerId == $followingId) {
    // Invalid or self-unfollow attempt
    header("Location: home.php");
    exit;
}

// Delete the follow relationship
$stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
$stmt->bind_param("ii", $followerId, $followingId);
$stmt->execute();
$stmt->close();

header("Location: home.php");
exit;
?>

