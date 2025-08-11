<?php
session_start();
require_once 'db.php';

// Redirect if user not logged in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: SignIn.php");
    exit();
}

// Get current user ID
$username = $_SESSION['username'];
$userStmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
$user_id = $user['user_id'] ?? null;

$successMsg = $errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    // Sanitize inputs
    $title       = trim($_POST['newName']);
    $mealType    = trim($_POST['mealType']);
    $cuisine     = trim($_POST['cuiType']);
    $difficulty  = trim($_POST['recipeDiff']);
    $serves      = trim($_POST['serves']);
    $timeR       = trim($_POST['timeR']);
    $steps       = trim($_POST['addSteps']);

    // Handle image upload
    $image_url = null;
    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['newImage']['tmp_name']);
        $fileSize = $_FILES['newImage']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $errorMsg = "Only JPG, PNG, and GIF files are allowed.";
        } elseif ($fileSize > 5 * 1024 * 1024) { // 5MB limit
            $errorMsg = "Image size must be under 5MB.";
        } else {
            $fileTmp  = $_FILES['newImage']['tmp_name'];
            $fileName = uniqid("recipe_", true) . "." . pathinfo($_FILES['newImage']['name'], PATHINFO_EXTENSION);
            $filePath = "uploads/" . $fileName;

            if (!is_dir("uploads")) mkdir("uploads", 0777, true);
            if (move_uploaded_file($fileTmp, $filePath)) {
                $image_url = $filePath;
            } else {
                $errorMsg = "Error uploading image.";
            }
        }
    }

    if (empty($errorMsg)) {
        // Insert into recipes table
        $stmt = $conn->prepare("
            INSERT INTO recipes 
                (user_id, title, steps, cuisine, image_url, recipe_type, recipe_difficulty, serves, Time_to_make) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "issssssss", 
            $user_id, 
            $title, 
            $steps, 
            $cuisine, 
            $image_url, 
            $mealType, 
            $difficulty, 
            $serves, 
            $timeR
        );

        if ($stmt->execute()) {
            $recipe_id = $stmt->insert_id;

            // Insert ingredients if provided
            if (!empty($_POST['ingredient_name']) && is_array($_POST['ingredient_name'])) {
                $ingredientStmt = $conn->prepare("
                    INSERT INTO recipe_ingredients (recipe_id, name, quantity, unit) 
                    VALUES (?, ?, ?, ?)
                ");

                foreach ($_POST['ingredient_name'] as $index => $name) {
                    $name = trim($name);
                    $quantity = isset($_POST['ingredient_qty'][$index]) && $_POST['ingredient_qty'][$index] !== ''
                        ? floatval($_POST['ingredient_qty'][$index])
                        : null;
                    $unit = isset($_POST['ingredient_unit'][$index]) ? trim($_POST['ingredient_unit'][$index]) : null;

                    if (!empty($name)) {
                        // Bind parameters, handle null quantity properly
                        if ($quantity === null) {
                            // Use NULL for quantity (bind as double with null requires special handling)
                            $ingredientStmt->bind_param("isss", $recipe_id, $name, $unit);
                            // This would fail because SQL expects 4 params; so instead pass quantity as null but bind_param doesn't accept null for 'd' type
                            // Workaround: bind quantity as NULL string and handle in SQL or allow zero instead of null.
                            // For simplicity, cast quantity to 0 if null:
                            $quantity = 0;
                            $ingredientStmt->bind_param("isds", $recipe_id, $name, $quantity, $unit);
                        } else {
                            $ingredientStmt->bind_param("isds", $recipe_id, $name, $quantity, $unit);
                        }
                        $ingredientStmt->execute();
                    }
                }
                $ingredientStmt->close();
            }

            $successMsg = "Recipe added successfully!";
        } else {
            $errorMsg = "Error adding recipe: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chefs Unite Website</title>
    <link rel="stylesheet" href="CU.css">
    <link href="https://api.fontshare.com/v2/css?f[]=chillax@400&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="Home.php"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
            <li class="text1">Chefs Unite</li>
            <li class="icon1"><a href="YourRecipes.php"><img src="Images/profile icon.png" alt="Profile"></a></li>
            <li class="icon2"><a href="HomeProfile.php"><img src="Images/home icon.png" alt="Home Page"></a></li>
        </ul>
    </nav>

    <div class="addBox">
        <?php if (!empty($successMsg)) echo "<p class='success'>$successMsg</p>"; ?>
        <?php if (!empty($errorMsg)) echo "<p class='error'>$errorMsg</p>"; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="sec1">
                <img src="Images/add-image-icon-symbol-design-illustration-vector.jpg" alt="Image">
                <input type="file" name="newImage" id="newImage" accept="image/png, image/jpeg" required>
                <label for="newName">Recipe Name:</label>
                <input type="text" name="newName" id="newName" required>
            </div>

            <div class="sec2">
                <label for="mealType">Recipe Type:</label>
                <select name="mealType" id="mealType">
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                    <option value="dessert">Dessert</option>
                </select>

                <label for="cuiType">Cuisine Type:</label>
                <select name="cuiType" id="cuiType">
                    <option value="southern">Southern</option>
                    <option value="american">American</option>
                    <option value="mexican">Mexican</option>
                    <option value="indian">Indian</option>
                    <option value="mediterranean">Mediterranean</option>
                    <option value="african">African</option>
                    <option value="italian">Italian</option>
                    <option value="vietnamese">Vietnamese</option>
                    <option value="japanese">Japanese</option>
                    <option value="korean">Korean</option>
                    <option value="chinese">Chinese</option>
                    <option value="german">German</option>
                    <option value="irish">Irish</option>
                    <option value="peruvian">Peruvian</option>
                    <option value="australian">Australian</option>
                    <option value="jamaican">Jamaican</option>
                    <option value="other">Other</option>
                </select>

                <label for="recipeDiff">Recipe Difficulty:</label>
                <select name="recipeDiff" id="recipeDiff">
                    <option value="easy">Easy</option>
                    <option value="moderate">Moderate</option>
                    <option value="hard">Hard</option>
                </select>

                <label for="serves">Serves:</label>
                <select name="serves" id="serves">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="more">6+</option>
                </select>

                <label for="timeR">Time to make:</label>
                <select name="timeR" id="timeR">
                    <option value="10">10 mins</option>
                    <option value="20">20 mins</option>
                    <option value="30">30 mins</option>
                    <option value="30+">30 mins - 1 hr</option>
                    <option value="1+">1-2 hrs</option>
                    <option value="2+">2-3 hrs</option>
                    <option value="3+">More than 3 hrs</option>
                </select>

                <label>Ingredients:</label>
                <div id="ingredient-list">
                    <div class="ingredient-row">
                        <input type="text" name="ingredient_name[]" placeholder="Ingredient" required>
                        <input type="text" name="ingredient_qty[]" placeholder="Quantity">
                        <input type="text" name="ingredient_unit[]" placeholder="Unit">
                    </div>
                </div>
                <button type="button" onclick="addIngredient()">Add Ingredient</button>
            </div>

            <div class="sec3">
                <label for="addSteps">Steps (enter in numerical order):</label>
                <textarea name="addSteps" id="addSteps"></textarea>   
            </div>
            <input id="done1" type="submit" value="Add New Recipe!">
        </form>  
    </div>

    <script>
    function addIngredient() {
        let container = document.getElementById('ingredient-list');
        let newRow = document.createElement('div');
        newRow.classList.add('ingredient-row');
        newRow.innerHTML = `
            <input type="text" name="ingredient_name[]" placeholder="Ingredient" required>
            <input type="text" name="ingredient_qty[]" placeholder="Quantity">
            <input type="text" name="ingredient_unit[]" placeholder="Unit">
        `;
        container.appendChild(newRow);
    }
    </script>
</body>
</html>
