<?php
session_start();
require 'db.php';

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: SignIn.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch recipes using the correct column name for the image
$sql = "SELECT id, title, image_url FROM recipes WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Recipes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .recipe-card {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .recipe-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .recipe-actions i {
            cursor: pointer;
            margin-left: 10px;
        }
        .confirm-box {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.3);
            z-index: 1000;
        }
        .confirm-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
    </style>
</head>
<body>

<h1>Your Recipes</h1>

    <nav class="navbar">
        <ul>
            <li><a href="Followers.php">Followers </a></li>
            <li><a href="Following.php">Following </a></li>
            <li><a href="UploadRecipe.php">Upload Recipe</a></li>
            <li class="yourRecipesTab"><a href="YourRecipes.php">Your Recipes</a></li>
            <li><a href="SavedRecipes.php">Saved Recipes</a></li>
        </ul>
    </nav>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="recipe-card" <?php echo $row['id']; ?>>
        <div class="recipe-info">
            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Recipe Image" width="60" height="60">
            <span><?php echo htmlspecialchars($row['title']); ?></span>
        </div>
        <div class="recipe-actions">
            <a href="editRecipe.php?recipe_id=<?php echo $row['id']; ?>"><i class="fa fa-edit" title="Edit"></i></a>
            <i class="fa fa-trash" style="color:red;" 
               onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>')"></i>
        </div>
    </div>
<?php endwhile; ?>

<!-- Overlay -->
<div class="confirm-overlay" id="confirmOverlay"></div>

<!-- Confirm Box -->
<div class="confirm-box" id="confirmBox">
    <p id="confirmText"></p>
    <button onclick="deleteRecipeConfirmed()">Yes</button>
    <button onclick="closeConfirm()">Cancel</button>
</div>

<script>
let recipeToDelete = null;

function confirmDelete(recipe_id, title) {
    recipeToDelete = recipe_id;
    document.getElementById('confirmText').innerText = 
        `Are you sure you want to delete "${title}" recipe?`;
    document.getElementById('confirmOverlay').style.display = 'block';
    document.getElementById('confirmBox').style.display = 'block';
}

function closeConfirm() {
    recipeToDelete = null;
    document.getElementById('confirmOverlay').style.display = 'none';
    document.getElementById('confirmBox').style.display = 'none';
}

function deleteRecipeConfirmed() {
    if (!recipeToDelete) return;

    fetch('deleteRecipe.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'recipe_id=' + encodeURIComponent(recipeToDelete)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const recipeCard = document.getElementById('recipe-' + recipeToDelete);
            if (recipeCard) {
                recipeCard.remove(); // Remove recipe card instantly
            }
        } else {
            alert(data.message || "Error deleting recipe.");
        }
        closeConfirm();
    })
    .catch(err => {
        console.error(err);
        alert("Error deleting recipe.");
        closeConfirm();
    });
}
</script>

</body>
</html>
