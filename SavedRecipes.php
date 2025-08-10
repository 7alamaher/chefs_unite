<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: SignIn.php");
    exit();
}

$userId = $_SESSION['user_id'];

$sql = "
    SELECT r.id, r.title, r.description, r.image_url, r.cuisine, r.created_at, u.username
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chefs Unite - Saved Recipes</title>
    <link rel="stylesheet" href="CU.css">
</head>
<body>
    <h1>My Saved Recipes</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="recipe-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="recipe-card">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                    <p><strong>By:</strong> <?php echo htmlspecialchars($row['username']); ?></p>
                    <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($row['cuisine']); ?></p>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <a href="view_recipe.php?id=<?php echo $row['id']; ?>">View Recipe</a>
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
?>
