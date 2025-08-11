<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: SignIn.php");
    exit();
}

if (!isset($_GET['user_id'])) {
    die("User ID missing.");
}

$followed_id = intval($_GET['user_id']);

// Get current user ID
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($follower_id);
$stmt->fetch();
$stmt->close();

// Unfollow
$del = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
$del->bind_param("ii", $follower_id, $followed_id);
$del->execute();
$del->close();

header("Location: viewProfile.php?id=" . $followed_id);
exit();
?>
