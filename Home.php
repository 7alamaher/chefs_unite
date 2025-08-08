<?php
session_start();
require_once 'db.php';

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
            <li><a href="Home.php"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
            <li class="text1">Chefs Unite</li>
            <li class="sign">
                <?php if (isset($_SESSION['username'])): ?>
                    <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="Logout.php">Logout</a></span>
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
        <figure>
            <img src="Images/birria-tacos-recipe-5189284-hero-01-56190b7eb77b4370a0bf7f2341e94ee4.jpg" alt="Birria Tacos">
            <figcaption><a href="#">Birria Tacos by ketchup_in_the_kitchen</a></figcaption>
        </figure>
        <figure>
            <img src="Images/breakfast-burrito-lead-66a7e23ce81b0.jpg" alt="Breakfast Burrito">
            <figcaption><a href="#">CheEZy's Breakfast Burrito by cheEZy</a></figcaption>
        </figure>
        <figure>
            <img src="Images/chicken and brocccoli.jpg" alt="Chicken and Broccoli">
            <figcaption><a href="#">Chicken and Broccoli by QuickMeals</a></figcaption>
        </figure>
        <figure>
            <img src="Images/COPYCAT-IKEA-SWEDISH-MEATBALLS-FINALS-2-1-1.jpg" alt="Swedish Meatballs">
            <figcaption><a href="#">Ikea's Swedish Meatballs by copyKat95</a></figcaption>
        </figure>
        <figure>
            <img src="Images/French-Toast-Sticks-Camping-Breakfast-1400px-5.jpg" alt="French Toast Sticks">
            <figcaption><a href="#">French Toast Sticks by for_champs</a></figcaption>
        </figure>
        <figure>
            <img src="Images/Mac-and-cheese-photo.jpg" alt="Mac n Cheese">
            <figcaption><a href="#">cheEZy's Mac by cheEZy</a></figcaption>
        </figure>
        <figure>
            <img src="Images/pho.jpg" alt="Beef Pho">
            <figcaption><a href="#">Beef Pho by TheNguyeners</a></figcaption>
        </figure>
        <figure>
            <img src="Images/Spaghetti-Alfredo-FEAT-IMAGE.jpg" alt="Simple Alfredo">
            <figcaption><a href="#">Simple Alfredo by impasta</a></figcaption>
        </figure>
        <figure>
            <img src="Images/Spaghetti-Bolognese-Chicken.jpg" alt="Spaghetti">
            <figcaption><a href="#">Spaghetti by impasta</a></figcaption>
        </figure>
        <figure>
            <img src="Images/vegan-poke-bowl-recipe.jpg" alt="Poke Bowl">
            <figcaption><a href="#">Poke Bowl by BrandoTazen</a></figcaption>
        </figure>
    </div>
</body>
</html>