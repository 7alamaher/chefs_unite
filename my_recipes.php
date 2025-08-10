<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: SignIn.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, title, image_url 
        FROM recipes 
        WHERE user_id = ?
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recipes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Recipes - Chefs Unite</title>
    <link rel="stylesheet" href="CU.css">
    <link href="https://api.fontshare.com/v2/css?f[]=chillax@400&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="Home.php"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
            <li class="text1">Chefs Unite</li>
            <li class="icon1"><a href="YourRecipes.php"><img src="Images/profile icon.png" alt="Profile"></a></li>
            <li class="icon2"><a href="HomeProfile.php"><img src="Images/home icon.png" alt="Home Page"></a></li>
        </ul>
    </nav>

    <div class="profile">
        <p class="profileTitle">Your Recipes</p>
    </div>

    <div class="buttons">
        <button class="but1" onclick="window.location.href='add_recipe.php'">Add Recipe!</button>
        <button class="but2" onclick="window.location.href='delete_recipe.php'">Delete Recipe!</button>
    </div>

    <div class="gallery2">
        <?php if ($recipes->num_rows > 0): ?>
            <?php while ($recipe = $recipes->fetch_assoc()): ?>
                <figure>
                    <a href="view_recipe.php?id=<?= $recipe['id']; ?>">
                        <img src="<?= htmlspecialchars($recipe['image_url']); ?>" alt="<?= htmlspecialchars($recipe['title']); ?>">
                    </a>
                    <figcaption><?= htmlspecialchars($recipe['title']); ?></figcaption>
                </figure>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't uploaded any recipes yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
