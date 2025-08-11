<?php
include 'db.php';

if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $searchTerm = "%" . trim($_GET['q']) . "%";

    // Prepare SQL with LEFT JOIN to recipe_ingredients for ingredient name search
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
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Search Results for \"" . htmlspecialchars($_GET['q']) . "\":</h2>";

    if ($result->num_rows > 0) {
        while ($recipe = $result->fetch_assoc()) {
            echo "<div class='recipe'>";
            echo "<h3>" . htmlspecialchars($recipe['title']) . "</h3>";
            // Description is not in your schema. If you want to show steps snippet or something, you can do that:
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
