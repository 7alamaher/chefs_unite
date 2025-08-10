<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: SignIn.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch bookmarked recipes for user
$sql = "
    SELECT r.id, r.title, r.description, r.image_url, r.cuisine
    FROM bookmarks b
    INNER JOIN recipes r ON b.recipe_id = r.id
    WHERE b.user_id = ?
    ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$savedRecipes = [];
while ($row = $result->fetch_assoc()) {
    $savedRecipes[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Recipes - Chefs Unite</title>
    <link rel="stylesheet" href="CU.css">
</head>
<body>
    <header>
        <h1>My Saved Recipes</h1>
        <a href="index.php">Home</a>
    </header>

    <main>
        <?php if (empty($savedRecipes)): ?>
            <p>You haven't bookmarked any recipes yet.</p>
        <?php else: ?>
            <div class="recipe-grid">
                <?php foreach ($savedRecipes as $recipe): ?>
                    <div class="recipe-card">
                        <?php if (!empty($recipe['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($recipe['image_url']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                        <?php endif; ?>
                        <h2><?php echo htmlspecialchars($recipe['title']); ?></h2>
                        <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($recipe['cuisine']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
                        <a href="recipe.php?id=<?php echo $recipe['id']; ?>">View Recipe</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
