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
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chefs Unite</title>
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

    <div>
        <p class="aboutText">
            Chefs Unite is an online cookbook made by the community. Find recipes that can level up your cooking or that you love! Share recipes that you cherish with others! Discover more food for a better mood!
        </p>
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
                <figcaption><a href="#">Beignets by Sweet_n_Simple</a></figcaption>
            </figure>
            <figure>
                <img src="Images/Finnish-Salmon-Soup-12-1.jpg" alt="Lohikeitto">
                <figcaption><a href="#">Lohikeitto by itsZeke</a></figcaption>
            </figure>
            <figure>
                <img src="Images/margherita-pizza-close.jpg" alt="Margherita Pizza">
                <figcaption><a href="#">Maargherita Pizza by cheEZy</a></figcaption>
            </figure>
            <figure>
                <img src="Images/Starbucks-Cheese-Danish-Photo.jpg" alt="Cheese Danish">
                <figcaption><a href="#">Cheese Danish by paniko</a></figcaption>
            </figure>
            <figure>
                <img src="Images/steak-sandwich-recipe-snippet-2.jpg" alt="Steak Sandwich">
                <figcaption><a href="#">Steak Sandwich by steackem.official</a></figcaption>
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
					<li> <a href="indian.php">Indian</a></li>
					<li><a href="mediterranean.php">Mediterranean</a></li>
				</ul>
			</nav>
		</div>

       <div class="gallery2">
    <?php
    require 'db.php';

    $sql = "SELECT r.id, r.title, r.image_url, u.username
            FROM recipes r
            JOIN users u ON r.user_id = u.user_id
            ORDER BY r.created_at DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $title = htmlspecialchars($row['title']);
            $image = htmlspecialchars($row['image_url']); 
            $author = htmlspecialchars($row['username']);

            echo "
            <figure>
                <img src='$image' alt='$title'>
                <figcaption>
                    <a href='viewRecipe.php?id={$row['id']}'>$title by $author</a>
                </figcaption>
            </figure>";
        }
    } else {
        echo "<p>No recipes found.</p>";
    }
    ?>
</div>


</body>
</html>
