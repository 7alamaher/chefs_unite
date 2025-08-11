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

if ($follower_id === $following_id) {
    die("You can't follow yourself.");
}

// Only follow if not already followed
$check = $conn->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
$check->bind_param("ii", $follower_id, $followed_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    $add = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
    $add->bind_param("ii", $follower_id, $followed_id);
    $add->execute();
    $add->close();
}
$check->close();

header("Location: viewProfile.php?id=" . $followed_id);
exit();
?>
