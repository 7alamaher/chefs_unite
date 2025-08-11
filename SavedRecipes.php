<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: SignIn.php");
    exit();
}

$userId = intval($_SESSION['user_id']);

$sql = "
    SELECT r.id, r.title, r.image_url, r.cuisine, r.created_at, u.username
    FROM bookmarks b
    INNER JOIN recipes r ON b.recipe_id = r.id
    INNER JOIN users u ON r.user_id = u.user_id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Get logged-in username
$currentUsername = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Chefs Unite - Saved Recipes</title>
    <link rel="stylesheet" href="CU.css">
    <link href="https://api.fontshare.com/v2/css?f[]=chillax@400&display=swap" rel="stylesheet">
</head>
<body>
    
 <!-- Top Navbar -->
    <nav class="navbar">
        <ul>
            <li><a href="Home.php"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
            <li class="text1">Chefs Unite</li>
            <li class="icon1"><a href="profile.php"><img src="Images/profile icon.png" alt="Profile"></a></li>
            <li class="icon2"><a href="Home.php"><img src="Images/home icon.png" alt="Home Page"></a></li>
        </ul>
    </nav>

    <!-- Page Title -->
    <h1 class="profileTitle">My Saved Recipes</h1>

    <!-- Sub Navbar for Profile Options -->
    <nav class="profile">
        <ul>
            <li><a href="Followers.php">Followers Count</a></li>
            <li><a href="Following.php">Following Count</a></li>
            <li><a href="UploadRecipe.php">Upload Recipe</a></li>
            <li><a href="YourRecipes.php">Your Recipes</a></li>
            <li class="savedTab"><a href="SavedRecipes.php">Saved Recipes</a></li>
        </ul>
    </nav>

    <!-- Saved Recipes Grid -->
    <?php if ($result->num_rows > 0): ?>
        <div class="gallery2">
            <?php while ($row = $result->fetch_assoc()): ?>
                <figure>
                    <a href="viewRecipe.php?id=<?php echo $row['id']; ?>">
                        <img src="<?php echo htmlspecialchars($row['image_url'] ?: 'default-placeholder.png'); ?>" 
                             alt="<?php echo htmlspecialchars($row['title']); ?>">
                    </a>
                    <figcaption>
                        <?php echo htmlspecialchars($row['title']); ?><br>
                        <small>By: <?php echo htmlspecialchars($row['username']); ?></small><br>
                        <small>Cuisine: <?php echo htmlspecialchars($row['cuisine']); ?></small>
                    </figcaption>
                </figure>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align:center;">You have not saved any recipes yet.</p>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>