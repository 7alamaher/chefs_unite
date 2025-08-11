<?php
session_start();
require_once 'db.php';

// Redirect to login if not signed in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: SignIn.php");
    exit();
}

// Get logged-in username
$currentUsername = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Search Results - Chef Unite</title>
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

<?php
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $searchTerm = "%" . trim($_GET['q']) . "%";

    $sql = "
        SELECT DISTINCT r.id, r.title, r.steps, r.image_url
        FROM recipes r
        LEFT JOIN recipe_ingredients ri ON r.id = ri.recipe_id
        WHERE r.title LIKE ? OR r.cuisine LIKE ? OR ri.name LIKE ?
        ORDER BY r.title ASC
        LIMIT 50
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("<p>Prepare failed: " . htmlspecialchars($conn->error) . "</p>");
    }

    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Search Results for \"" . htmlspecialchars($_GET['q']) . "\":</h2>";

    if ($result->num_rows > 0) {
        while ($recipe = $result->fetch_assoc()) {
            echo "<div class='recipe'>";
            echo "<h3><a href='viewRecipe.php?id=" . urlencode($recipe['id']) . "'>" . htmlspecialchars($recipe['title']) . "</a></h3>";
            echo "<p>" . htmlspecialchars(substr($recipe['steps'], 0, 150)) . "...</p>";
            if ($recipe['image_url']) {
                echo "<img src='" . htmlspecialchars($recipe['image_url']) . "' alt='Recipe Image' width='200'>";
            }
            echo "</div><hr>";
        }
    } else {
        echo "<p>No recipes found.</p>";
    }
} else {
    echo "<p>Please enter a search term.</p>";
}
?>

</body>
</html>
