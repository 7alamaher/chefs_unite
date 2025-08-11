<?php
session_start();
require_once 'db.php';

// Redirect to login if not signed in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: SignIn.php");
    exit();
}

$currentUsername = $_SESSION['username'];

$sql = "SELECT user_id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("User not found.");
}
$user_id = $result->fetch_assoc()['user_id'];

$sql = "SELECT u.username, u.profile_image
        FROM follows f
        JOIN users u ON f.follower_id = u.user_id
        WHERE f.following_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$followersResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chefs Unite - Followers</title>
    <link rel="stylesheet" href="CU.css">
    <link href="https://api.fontshare.com/v2/css?f[]=chillax@400&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="Home.php"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
            <li class="text1">Chefs Unite</li>
            <li class="icon1"><a href="profile.php"><img src="Images/profile icon.png" alt="Profile"></a></li>
            <li class="icon2"><a href="Home.php"><img src="Images/home icon.png" alt="Home Page"></a></li>
        </ul>
    </nav>
    <div class="profile">
        <p class="profileTitle">Your Profile<br></p>

        <nav>
            <ul>
                <li class="followersTab"><a href="Followers.php">Followers</a></li>
                <li><a href="Following.php">Following</a></li>
                <li><a href="YourRecipes.php">Your Recipes</a></li>
                <li><a href="SavedRecipes.php">Saved Recipes</a></li>
            </ul>
        </nav>
    </div>
    <div class="followingBox">
        <ul>
            <?php if ($followersResult->num_rows > 0): ?>
                <?php while ($row = $followersResult->fetch_assoc()): ?>
                    <li>
                        <?php if (!empty($row['profile_image'])): ?>
                            <img src="<?= htmlspecialchars($row['profile_image']); ?>" alt="<?= htmlspecialchars($row['username']); ?>" width="40" height="40">
                        <?php endif; ?>
                        <?= htmlspecialchars($row['username']); ?>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No one is following you yet.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
