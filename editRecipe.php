<?php
// edit_recipe.php
require 'session_guard.php'; // Ensure user is logged in
require 'db.php';

// Get recipe ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid recipe ID.");
}

$recipe_id = intval($_GET['id']);
$user_id = $_SESSION['user']['id']; // Assuming session stores user ID

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
$sql = "SELECT ingredient FROM recipe_ingredients WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$ing_result = $stmt->get_result();
while ($row = $ing_result->fetch_assoc()) {
    $ingredients[] = $row['ingredient'];
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $instructions = trim($_POST['instructions']);
    $cuisine = trim($_POST['cuisine']);
    $new_ingredients = array_map('trim', $_POST['ingredients'] ?? []);

    // Start transaction
    $conn->begin_transaction();
    try {
        // Update recipe
        $sql = "UPDATE recipes SET title = ?, description = ?, instructions = ?, cuisine = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $title, $description, $instructions, $cuisine, $recipe_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Delete old ingredients
        $stmt = $conn->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?");
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $stmt->close();

        // Insert new ingredients
        if (!empty($new_ingredients)) {
            $stmt = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient) VALUES (?, ?)");
            foreach ($new_ingredients as $ingredient) {
                if (!empty($ingredient)) {
                    $stmt->bind_param("is", $recipe_id, $ingredient);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        $conn->commit();
        echo "<p style='color: green;'>Recipe updated successfully!</p>";
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
    <title>Edit Recipe</title>
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
                <input type="text" name="ingredients[]" value="${value}" placeholder="Ingredient" required>
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
        <label>Description:
            <textarea name="description" required><?= htmlspecialchars($recipe['description']) ?></textarea>
        </label>
        <label>Instructions:
            <textarea name="instructions" required><?= htmlspecialchars($recipe['instructions']) ?></textarea>
        </label>
        <label>Cuisine:
            <input type="text" name="cuisine" value="<?= htmlspecialchars($recipe['cuisine']) ?>" required>
        </label>
        <label>Ingredients:</label>
        <div id="ingredients-container"></div>
        <button type="button" onclick="addIngredientField()">Add Ingredient</button>
        <br><br>
        <button type="submit">Update Recipe</button>
    </form>
</body>
</html>
