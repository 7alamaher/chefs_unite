<?php
	include 'db.php';

	if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
		$searchTerm = "%" . $conn->real_escape_string($_GET['q']) . "%";

		$stmt = $conn->prepare("SELECT title, description, image_url FROM recipes WHERE title LIKE ? OR ingredients LIKE ?");
		$stmt->bind_param("ss", $searchTerm, $searchTerm);
		$stmt->execute();
		$result = $stmt->get_result();

		echo "<h2>Search Results for \"" . htmlspecialchars($_GET['q']) . "\":</h2>";

		if ($result->num_rows > 0) {
			while ($recipe = $result->fetch_assoc()) {
				echo "<div class='recipe'>";
				echo "<h3>" . htmlspecialchars($recipe['title']) . "</h3>";
				echo "<p>" . htmlspecialchars($recipe['description']) . "</p>";
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