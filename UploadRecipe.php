<?php
// Protect the page so only logged-in users can access it
require 'session_guard.php';
require 'db.php';

// Initialize message variable
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id']; // Assuming session stores user array with 'id'

    // Get form data
    $title = trim($_POST['newName']);
    $recipe_type = $_POST['mealType'];
    $cuisine = $_POST['cuiType'];
    $recipe_difficulty = $_POST['recipeDiff'];
    $serves = $_POST['serves'];
    $time_to_make = $_POST['timeR'];
    $steps = trim($_POST['addSteps']);
    $description = ""; // If you plan to add a description field later

    // File upload handling
    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmpPath = $_FILES['newImage']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['newImage']['name']);
        $destPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $image_url = $destPath;

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO Recipes (user_id, title, description, steps, cuisine, image_url, created_at, recipe_type, recipe_difficulty, serves, Time_to_make) 
                                    VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)");
            $stmt->bind_param("isssssssis", $user_id, $title, $description, $steps, $cuisine, $image_url, $recipe_type, $recipe_difficulty, $serves, $time_to_make);

            if ($stmt->execute()) {
                $message = "Recipe uploaded successfully!";
            } else {
                $message = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error moving uploaded file.";
        }
    } else {
        $message = "Image upload failed. Please try again.";
    }
}
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
            <li><a href="Home.html"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
            <li class="text1">Chefs Unite</li>
            <li class="icon1"><a href=""><img src="Images/profile icon.png" alt="Profile"></a></li>
            <li class="icon2"><a href="HomeProfile.html"><img src="Images/home icon.png" alt="Home Page"></a></li>
        </ul>
    </nav>

    <?php if (!empty($message)): ?>
        <p style="text-align:center; color:green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="addBox">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="sec1">
                <img src="Images/add-image-icon-symbol-design-illustration-vector.jpg" alt="Image">
                <input type="file" name="newImage" id="newImage" accept="image/png, image/jpeg" required>
                <label for="newName">Recipe Name:</label>
                <input type="text" name="newName" id="newName" required>
            </div>
            <div class="sec2">
                <label for="mealType">Meal Type:</label>
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
                    <option value="hawaiian">Hawaiian</option>
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
            </div>
            <div class="sec3">
                <label for="addSteps">Steps (enter in numerical order):</label>
                <textarea name="addSteps" id="addSteps"></textarea>
            </div>
            <input id="done1" type="submit" value="Add New Recipe!">
        </form>
    </div>
</body>
</html>