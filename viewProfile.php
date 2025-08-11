<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: SignIn.php");
    exit();
}

// Make sure user_id is in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("No profile specified.");
}

$userId = (int)$_GET['id']; // The profile owner’s ID from URL

// Fetch the user’s details
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
$profileUsername = $user['username'];

// Followers count
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM follows WHERE following_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$followersCount = $stmt->get_result()->fetch_assoc()['count'];

// Following count
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM follows WHERE follower_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$followingCount = $stmt->get_result()->fetch_assoc()['count'];

// Uploaded recipes
$recipesQuery = $conn->prepare("SELECT id, title, image_url FROM recipes WHERE user_id = ?");
$recipesQuery->bind_param("i", $userId);
$recipesQuery->execute();
$recipesResult = $recipesQuery->get_result();

// Check if current user is following this profile
$isFollowing = false;
if ($profileUsername !== $_SESSION['username']) {
    $checkFollow = $conn->prepare("SELECT 1 FROM follows WHERE follower_id = (SELECT user_id FROM users WHERE username = ?) AND following_id = ?");
    $checkFollow->bind_param("si", $_SESSION['username'], $userId);
    $checkFollow->execute();
    $isFollowing = $checkFollow->get_result()->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Chefs Unite - <?php echo htmlspecialchars($profileUsername); ?>'s Profile</title>
    <link rel="stylesheet" href="CU.css">
    <link href="https://api.fontshare.com/v2/css?f[]=chillax@400&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <ul>
        <li><a href="Home.php"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
        <li class="text1">Chefs Unite</li>

        <!-- Profile and Home icons in top right -->
        <li class="icon1"><a href="Profile.php?id=<?php echo urlencode($_SESSION['user_id']); ?>"><img src="Images/profile icon.png" alt="Profile"></a></li>
        <li class="icon2"><a href="Home.php"><img src="Images/home icon.png" alt="Home Page"></a></li>
    </ul>
</nav>

<!-- Profile Header -->
<h1><?php echo htmlspecialchars($profileUsername); ?>'s Profile</h1>


<!-- Follow/Unfollow button -->
<?php if ($profileUsername !== $_SESSION['username']): ?>
    <form action="unfollow.php" method="GET" style="margin-top:10px;">
        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
        <button type="submit">
            <?php echo $isFollowing ? 'Unfollow' : 'Follow'; ?>
        </button>
    </form>
<?php endif; ?>


<!-- Uploaded Recipes --><!-- Followers / Following links -->
<nav class="navbar">
    <ul>
        <li><a>Followers: <?php echo $followersCount; ?></a></li>
        <li><a>Following: <?php echo $followingCount; ?></a></li>
    </ul>
</nav>
<h2>Uploaded Recipes</h2>
<?php if ($recipesResult->num_rows > 0): ?>
    <div class="recipes-grid">
        <?php while ($recipe = $recipesResult->fetch_assoc()): ?>
            <div class="recipe-card">
                <a href="viewRecipe.php?id=<?php echo $recipe['id']; ?>">
                    <img src="<?php echo htmlspecialchars($recipe['image_url']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                    <p><?php echo htmlspecialchars($recipe['title']); ?></p>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>No recipes uploaded yet.</p>
<?php endif; ?>

</body>
</html>
