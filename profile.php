<?php
require 'db.php'; // your database connection
session_start();

// Check if user is logged in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: SignIn.php");
    exit();
}

$user = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Fetch user profile details
$sql = "SELECT username, created_at, country FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chefs Unite Website</title>
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
        <p class="profileTitle"><?= htmlspecialchars($user['username']); ?>'s Profile<br></p>
        <p class="actDetails">
            Joined: <?= date("F j, Y", strtotime($user['created_at'])); ?><br>
            Country: <?= htmlspecialchars($user['country'] ?? "Not set"); ?>
        </p>
        <nav>
            <ul>
                <li><a href="Followers.php">Followers Count</a></li>
                <li><a href="Following.php">Following Count</a></li>
                <li><a href="UploadRecipe.php">Upload Recipe</a></li>
                <li class="yourRecipesTab"><a href="YourRecipes.php">Your Recipes</a></li>
                <li><a href="SavedRecipes.php">Saved Recipes</a></li>
            </ul>
        </nav>
    </div>

    <!-- Add Recipe Button -->
    <div class="buttons">
        <button class="addRecipeBtn" onclick="window.location.href='AddRecipe.php'">+</button>
    </div>

    <div class="gallery2">
        <!-- Empty here, recipes are on YourRecipes.php -->
    </div>
</body>
</html>
