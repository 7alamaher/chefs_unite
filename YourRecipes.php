<?php
require 'session_guard.php';
require 'db.php';

// Get logged-in user ID
$user_id = $_SESSION['username']['user_id'];

// Fetch all recipes uploaded by this user
$sql = "SELECT id, title, image FROM recipes WHERE user_id = ?";
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

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="recipe-card" id="recipe-<?php echo $row['recipe_id']; ?>">
        <div class="recipe-info">
            <img src="<?php echo $row['image']; ?>" alt="Recipe Image" width="60" height="60">
            <span><?php echo htmlspecialchars($row['title']); ?></span>
        </div>
        <div class="recipe-actions">
            <i class="fa fa-edit" title="Edit"></i>
            <i class="fa fa-trash" style="color:red;" 
               onclick="confirmDelete(<?php echo $row['recipe_id']; ?>, '<?php echo addslashes($row['title']); ?>')"></i>
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
    fetch('deleteRecipe.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'recipe_id=' + recipeToDelete
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('recipe-' + recipeToDelete).remove();
        } else {
            alert(data.message);
        }
        closeConfirm();
    })
    .catch(err => {
        alert("Error deleting recipe.");
        closeConfirm();
    });
}
</script>

</body>
</html>
