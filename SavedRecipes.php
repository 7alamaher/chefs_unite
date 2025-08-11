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
    
    <nav class="navbar">
        <ul>
            <li><a href="Home.php"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
            <li class="text1">Chefs Unite</li>
            <li class="sign">
                <?php if (isset($_SESSION['username'])): ?>
                    <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="SignIn.php">Logout</a> | <a href="UploadRecipe.php">Add Recipe</a> | <a href="profile.php">Profile</a></span>
                <?php else: ?>
                    <a href="SignIn.php">Sign up or Log in!</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
    
    <h1>My Saved Recipes</h1>
    <nav class="navbar">
        <ul>
            <li><a href="Followers.php">Followers Count</a></li>
            <li><a href="Following.php">Following Count</a></li>
            <li><a href="UploadRecipe.php">Upload Recipe</a></li>
            <li class="yourRecipesTab"><a href="YourRecipes.php">Your Recipes</a></li>
            <li><a href="SavedRecipes.php">Saved Recipes</a></li>
        </ul>
    </nav>

    <?php if ($result->num_rows > 0): ?>
        <div class="recipe-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="recipe-card">
                    <img
                        src="<?php echo htmlspecialchars($row['image_url'] ?: 'default-placeholder.png'); ?>"
                        alt="<?php echo htmlspecialchars($row['title']); ?>"
                        width="200"
                        height="150"
                    />
                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                    <p><strong>By:</strong> <?php echo htmlspecialchars($row['username']); ?></p>
                    <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($row['cuisine']); ?></p>
                    <a href="viewRecipe.php?id=<?php echo $row['id']; ?>">View Recipe</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>You have not saved any recipes yet.</p>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();

