<?php
session_start();
require 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to edit recipes.");
}

// Get recipe ID from query string
if (!isset($_GET['recipe_id']) || !is_numeric($_GET['recipe_id'])) {
    die("Invalid recipe ID.");
}

$recipe_id = intval($_GET['recipe_id']);
$user_id = intval($_SESSION['user_id']);

// Fetch recipe data for this user
$sql = "SELECT * FROM recipes WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Recipe not found or you don't have permission to edit it.");
}

$recipe = $result->fetch_assoc();
$stmt->close();

// Fetch ingredients
$ingredients = [];
$sql = "SELECT name FROM recipe_ingredients WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$ing_result = $stmt->get_result();
while ($row = $ing_result->fetch_assoc()) {
    $ingredients[] = $row['name'];
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $cuisine = trim($_POST['cuisine']);
    $recipe_type = trim($_POST['recipe_type'] ?? '');
    $recipe_difficulty = trim($_POST['recipe_difficulty'] ?? '');
    $serves = trim($_POST['serves'] ?? '');
    $time_to_make = trim($_POST['time_to_make'] ?? '');
    $new_ingredients = array_map('trim', $_POST['ingredients'] ?? []);

    // Start transaction
    $conn->begin_transaction();
    try {
        // Update recipe
        $sql = "UPDATE recipes SET title = ?, cuisine = ?, recipe_type = ?, recipe_difficulty = ?, serves = ?, Time_to_make = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssii", $title, $cuisine, $recipe_type, $recipe_difficulty, $serves, $time_to_make, $recipe_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Delete old ingredients
        $stmt = $conn->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?");
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $stmt->close();

        // Insert new ingredients
        if (!empty($new_ingredients)) {
            $stmt = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, name) VALUES (?, ?)");
            foreach ($new_ingredients as $ingredient) {
                if (!empty($ingredient)) {
                    $stmt->bind_param("is", $recipe_id, $ingredient);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        $conn->commit();

        // Redirect after successful update
        header("Location: YourRecipes.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color: red;'>Error updating recipe: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chefs Unite - Edit Recipe</title>
    <link rel="stylesheet" href="CU.css">
    <link href="https://api.fontshare.com/v2/css?f[]=chillax@400&display=swap" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: auto; }
        label { display: block; margin-top: 10px; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; }
        .ingredient-group { display: flex; margin-top: 5px; }
        .ingredient-group input { flex: 1; }
        .ingredient-group button { margin-left: 5px; }
        button { padding: 8px 12px; margin-top: 10px; }
    </style>
    <script>
        function addIngredientField(value = '') {
            const container = document.getElementById('ingredients-container');
            const div = document.createElement('div');
            div.classList.add('ingredient-group');
            div.innerHTML = `
                <input type="text" name="ingredients[]" value="${value.replace(/"/g, '&quot;')}" placeholder="Ingredient" required>
                <button type="button" onclick="removeIngredientField(this)">Remove</button>
            `;
            container.appendChild(div);
        }
        function removeIngredientField(btn) {
            btn.parentElement.remove();
        }
        window.onload = function() {
            // Pre-fill existing ingredients
            <?php foreach ($ingredients as $ing): ?>
                addIngredientField("<?= htmlspecialchars($ing) ?>");
            <?php endforeach; ?>
        }
    </script>
</head>
<body>
    <h1>Edit Recipe</h1>
    <form method="POST">
        <label>Title:
            <input type="text" name="title" value="<?= htmlspecialchars($recipe['title']) ?>" required>
        </label>
        <label>Cuisine:
            <input type="text" name="cuisine" value="<?= htmlspecialchars($recipe['cuisine']) ?>">
        </label>
        <label>Recipe Type:
            <input type="text" name="recipe_type" value="<?= htmlspecialchars($recipe['recipe_type']) ?>">
        </label>
        <label>Recipe Difficulty:
            <input type="text" name="recipe_difficulty" value="<?= htmlspecialchars($recipe['recipe_difficulty']) ?>">
        </label>
        <label>Serves:
            <input type="text" name="serves" value="<?= htmlspecialchars($recipe['serves']) ?>">
        </label>
        <label>Time to Make:
            <input type="text" name="time_to_make" value="<?= htmlspecialchars($recipe['Time_to_make']) ?>">
        </label>
        <label>Ingredients:</label>
        <div id="ingredients-container"></div>
        <button type="button" onclick="addIngredientField()">Add Ingredient</button>
        <br><br>
        <button type="submit">Update Recipe</button>
    </form>
</body>
</html>
