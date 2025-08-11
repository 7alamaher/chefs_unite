<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: SignIn.php");
    exit();
}

$currentUsername = $_SESSION['username'];

// Get recipe ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid recipe ID.");
}
$recipe_id = intval($_GET['id']);

// Fetch recipe details with dynamic like/bookmark counts
$sql = "
SELECT r.id, r.title, r.steps, r.cuisine, r.image_url, r.created_at,
       r.recipe_type, r.recipe_difficulty, r.serves, r.Time_to_make,
       u.username AS author_name, u.user_id AS author_id,
       COUNT(DISTINCT l.id) AS likes_count,
       COUNT(DISTINCT b.id) AS bookmark_count
FROM recipes r
JOIN users u ON r.user_id = u.user_id
LEFT JOIN likes l ON r.id = l.recipe_id
LEFT JOIN bookmarks b ON r.id = b.recipe_id
WHERE r.id = ?
GROUP BY r.id
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();

if (!$recipe) {
    die("Recipe not found.");
}

// Fetch ingredients
$ingredient_sql = "SELECT name, quantity, unit FROM recipe_ingredients WHERE recipe_id = ?";
$ingredient_stmt = $conn->prepare($ingredient_sql);
$ingredient_stmt->bind_param("i", $recipe_id);
$ingredient_stmt->execute();
$ingredients_result = $ingredient_stmt->get_result();
$ingredients = [];
while ($row = $ingredients_result->fetch_assoc()) {
    $ingredients[] = $row;
}

// Get current user ID
$user_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$user_stmt->bind_param("s", $currentUsername);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$currentUser = $user_result->fetch_assoc();
$currentUserId = $currentUser['user_id'];

// Check if user has liked
$liked_stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND recipe_id = ?");
$liked_stmt->bind_param("ii", $currentUserId, $recipe_id);
$liked_stmt->execute();
$userLiked = $liked_stmt->get_result()->num_rows > 0;

// Check if user has bookmarked
$bookmarked_stmt = $conn->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND recipe_id = ?");
$bookmarked_stmt->bind_param("ii", $currentUserId, $recipe_id);
$bookmarked_stmt->execute();
$userBookmarked = $bookmarked_stmt->get_result()->num_rows > 0;

// Check if user follows author
$follow_stmt = $conn->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
$follow_stmt->bind_param("ii", $currentUserId, $recipe['author_id']);
$follow_stmt->execute();
$userFollows = $follow_stmt->get_result()->num_rows > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - Chef Unite</title>
    <link rel="stylesheet" href="CU.css">
    <link href="https://api.fontshare.com/v2/css?f[]=chillax@400&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <ul>
        <li><a href="Home.php"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
        <li class="text1">Chefs Unite</li>
        <li class="icon1"><a href="Profile.php"><img src="Images/profile icon.png" alt="Profile"></a></li>
        <li class="icon2"><a href="Home.php"><img src="Images/home icon.png" alt="Home Page"></a></li>
    </ul>
</nav>

<div class="addBox">
    <div class="sec1">
        <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
        <p>
            By <a href="viewProfile.php?id=<?php echo $recipe['author_id']; ?>">
                <?php echo htmlspecialchars($recipe['author_name']); ?>
            </a>
            | Posted on <?php echo date("Y-m-d", strtotime($recipe['created_at'])); ?>
        </p>
        <?php if (!empty($recipe['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($recipe['image_url']); ?>" alt="Recipe Image" style="max-width:300px; border-radius:20px; box-shadow:0 2px 2px black;">
        <?php endif; ?>
    </div>

    <div class="sec2" style="margin-top:20px;">
        <p><strong>Prep Time:</strong> <?php echo htmlspecialchars($recipe['Time_to_make']); ?></p>
        <p><strong>Difficulty:</strong> <?php echo htmlspecialchars($recipe['recipe_difficulty']); ?></p>
        <p><strong>Serves:</strong> <?php echo htmlspecialchars($recipe['serves']); ?></p>
        <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($recipe['cuisine']); ?></p>
    </div>

    <div class="sec3" style="margin-top:20px;">
        <h3>Ingredients:</h3>
        <ul>
            <?php foreach ($ingredients as $ing): ?>
                <li><?php echo htmlspecialchars($ing['name']); ?>
                    <?php if (!empty($ing['quantity'])) echo " - " . htmlspecialchars($ing['quantity']); ?>
                    <?php if (!empty($ing['unit'])) echo " " . htmlspecialchars($ing['unit']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="sec4" style="margin-top:20px;">
        <h3>Instructions:</h3>
        <p><?php echo nl2br(htmlspecialchars($recipe['steps'])); ?></p>
    </div>

<div style="margin-top:20px;">

    <!-- Like toggle form -->
    <form action="like_toggle.php" method="POST" style="display:inline-block; margin-right: 10px;">
        <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">
        <input type="hidden" name="action" value="<?= $userLiked ? 'unlike' : 'like' ?>">
        <button type="submit" style="cursor:pointer;">
            <?= $userLiked ? '💔 Unlike' : '❤️ Like' ?> (<?= $recipe['likes_count'] ?>)
        </button>
    </form>

    <!-- Bookmark toggle form -->
    <?php if ($userBookmarked): ?>
        <form action="unbookmark.php" method="POST" style="display:inline-block; margin-right: 10px;">
            <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">
            <button type="submit" style="cursor:pointer;">❌ Remove Bookmark</button>
        </form>
    <?php else: ?>
        <form action="bookmark.php" method="POST" style="display:inline-block; margin-right: 10px;">
            <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">
            <button type="submit" style="cursor:pointer;">📌 Bookmark</button>
        </form>
    <?php endif; ?>

    <!-- Follow toggle form -->
    <?php if ($userFollows): ?>
        <form action="unfollow.php" method="GET" style="display:inline-block;">
            <input type="hidden" name="user_id" value="<?= $recipe['author_id'] ?>">
            <button type="submit" style="cursor:pointer;">➖ Unfollow Author</button>
        </form>
    <?php else: ?>
        <form action="follow.php" method="GET" style="display:inline-block;">
            <input type="hidden" name="user_id" value="<?= $recipe['author_id'] ?>">
            <button type="submit" style="cursor:pointer;">➕ Follow Author</button>
        </form>
    <?php endif; ?>

</div>

</div>

</body>
</html>
