<?php
session_start();
require_once 'db_connect.php';

// Get logged-in username if available
$currentUsername = $_SESSION['username'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chefs Unite</title>
        <link rel="stylesheet" href="CU.css">
        <link href="https://api.fontshare.com/v2/css?f[]=chillax@400&display=swap" rel="stylesheet">
    </head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="home.php"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
            <li class="text1">Chefs Unite</li>
            <li class="sign">
                <?php if (isset($_SESSION['username'])): ?>
                    <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Logout</a></span>
                <?php else: ?>
                    <a href="login.php">Sign up or Log in!</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
	
    <div>
        <p class="aboutText">Chefs Unite is an online cookbook made by the community. Find recipes that can level up your cooking or that you love! Share recipes that you cherish with others! Discover more food for a better mood!</p>
    </div>
	
	<form action="search.php" method="GET" class="searchC">
	  <label class="searchT" for="siteSearch">Got an Idea?</label>
	  <input class="search" type="search" id="siteSearch" name="q" placeholder="Type here..." required>
	  <button class="searchB" type="submit">Search</button>
	</form>

    <div class="bg1">
        <div class="gallery">
            <div>Featured <br> Recipes!</div>
            <figure>
                <img src="Images/beignets.jpg" alt="Beignets">
                <figcaption><a href="">Beignets by Sweet_n_Simple</a></figcaption>
            </figure>
            <figure>
                <img src="Images/Finnish-Salmon-Soup-12-1.jpg" alt="Lohikeitto">
                <figcaption><a href="">Lohikeitto by itsZeke</a></figcaption>
            </figure>
            <figure>
                <img src="Images/margherita-pizza-close.jpg" alt="Margherita Pizza">
                <figcaption><a href="">Maargherita Pizza by cheEZy</a></figcaption>
            </figure>
            <figure>
                <img src="Images/Starbucks-Cheese-Danish-Photo.jpg" alt="Cheese Danish">
                <figcaption><a href="">Cheese Danish by paniko</a></figcaption>
            </figure>
            <figure>
                <img src="Images/steak-sandwich-recipe-snippet-2.jpg" alt="Steak Sandwich">
                <figcaption><a href="">Steak Sandwich by steackem.official</a></figcaption>
            </figure>
        </div>
		
        <div class="text2">Need an Idea?</div>
			<nav class="categories">
				<ul>
					<li><a href="southern.php">Southern</a></li>
					<li><a href="mexican.php">Mexican</a></li>
					<li><a href="african.php">African</a></li>
					<li><a href="italian.php">Italian</a></li>
					<li><a href="vietnamese.php">Vietnamese</a></li>
					<li id="indian"><a href="indian.php">Indian</a></li>
					<li><a href="mediterranean.php">Mediterranean</a></li>
				</ul>
			</nav>
		</div>
	
    <div class="gallery2">
    <?php
    $cuisine = 'Indian';

    if ($currentUsername) {
        // Logged-in user: check like and bookmark status
        $stmt = $conn->prepare("
            SELECT r.id AS recipe_id, r.title, r.image_url, u.username,
                   (SELECT COUNT(*) FROM likes l WHERE l.recipe_id = r.id) AS like_count,
                   EXISTS (
                       SELECT 1 FROM likes l
                       JOIN users u2 ON u2.user_id = l.user_id
                       WHERE l.recipe_id = r.id AND u2.username = ?
                   ) AS user_liked,
                   EXISTS (
                       SELECT 1 FROM bookmarks b
                       JOIN users u3 ON u3.user_id = b.user_id
                       WHERE b.recipe_id = r.id AND u3.username = ?
                   ) AS user_bookmarked
            FROM recipes r
            JOIN users u ON r.user_id = u.user_id
            WHERE r.cuisine = ?
        ");
        $stmt->bind_param("sss", $currentUsername, $currentUsername, $cuisine);
    } else {
        // Guest user: only get like count
        $stmt = $conn->prepare("
            SELECT r.id AS recipe_id, r.title, r.image_url, u.username,
                   (SELECT COUNT(*) FROM likes l WHERE l.recipe_id = r.id) AS like_count
            FROM recipes r
            JOIN users u ON r.user_id = u.user_id
            WHERE r.cuisine = ?
        ");
        $stmt->bind_param("s", $cuisine);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()):
        $recipeId = (int)$row['recipe_id'];
        $title = htmlspecialchars($row['title']);
        $username = htmlspecialchars($row['username']);
        $image = htmlspecialchars($row['image_url'] ?? 'Images/Untitled_Artwork.jpg');
        $likeCount = (int)$row['like_count'];
        $userLiked = isset($row['user_liked']) ? (bool)$row['user_liked'] : false;
        $userBookmarked = isset($row['user_bookmarked']) ? (bool)$row['user_bookmarked'] : false;

        $likeAction = $userLiked ? 'unlike' : 'like';
        $likeBtnText = $userLiked ? 'Unlike' : 'Like';

        $bookmarkAction = $userBookmarked ? 'unbookmark.php' : 'bookmark.php';
        $bookmarkBtnText = $userBookmarked ? 'Remove Bookmark' : 'Bookmark';
    ?>
        <figure>
            <img src="<?= $image ?>" alt="<?= $title ?>">
            <figcaption><a href="#"><?= $title ?> by <?= $username ?></a></figcaption>

            <?php if ($currentUsername): ?>
                <form action="like_toggle.php" method="POST" style="margin-top: 8px;">
                    <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
                    <input type="hidden" name="action" value="<?= $likeAction ?>">
                    <button type="submit"><?= $likeBtnText ?> (<?= $likeCount ?>)</button>
                </form>

                <form action="<?= $bookmarkAction ?>" method="POST" style="margin-top: 4px;">
                    <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
                    <button type="submit"><?= $bookmarkBtnText ?></button>
                </form>
            <?php else: ?>
                <p style="margin-top: 8px;">Likes: <?= $likeCount ?> | <a href="login.php">Login to like</a></p>
                <p><a href="login.php">Login to bookmark</a></p>
            <?php endif; ?>
        </figure>
    <?php endwhile;
    $stmt->close();
    ?>
</div>

</body>
</html>
