<?php
// Only run PHP processing if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");

    require_once 'db.php';

    if (!isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['confpass'])) {
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields."
        ]);
        exit;
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confpass'];
    $profile_image = null;

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Email already exists."
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Check password match
    if ($password !== $confirm_password) {
        echo json_encode([
            "success" => false,
            "message" => "Passwords do not match."
        ]);
        exit;
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, profile_image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password_hash, $profile_image);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        echo json_encode([
            "success" => true,
            "message" => "User registered successfully.",
            "user" => [
                "user_id" => $user_id,
                "username" => $username,
                "email" => $email,
                "profile_image" => $profile_image
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Server error: " . $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html class="login-html">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Chefs Unite Website</title>
	<link rel="stylesheet" href="CU.css">
</head>
<body class="login-page">
    <nav class="navbar">
        <ul>
            <li><a href="Home.html"><img src="Images/Untitled_Artwork.jpg" alt="Logo"></a></li>
            <li class="text1">Chefs Unite</li>
            <li class="sign"><a href="">Sign up or Log in!</a></li>
        </ul>
    </nav>

	<div class="login-container">
	<div class="loginimage">
		<div id="error-message" style="color:red; font-weight:bold; margin-bottom:10px;"></div>

		<form id="signup-form" class="loginboxes" method="POST" action="">
			<input type="text" placeholder="UserName" name="username" required class="search">
			<input type="text" placeholder="E-mail" name="email" required class="search">
			<input type="password" placeholder="Password" name="password" required class="search">
			<input type="password" placeholder="Confirm Password" name="confpass" required class="search">
			<button type="submit" class="sign">Sign Up</button>
		</form>
	</div>
 
	<div class="signupoffer">
		<h1>Already a member?</h1>
		<p>Access your favorite recipes and secret ingredients — log in now!</p>
		<a href="SignIn.php"><button class="sign">Sign In</button></a>
	</div>

	<script>
        document.getElementById('signup-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('', { // Same file
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success === false) {
                    document.getElementById('error-message').textContent = data.message;
                } else {
                    window.location.href = 'Home.php';
                }
            })
            .catch(err => {
                document.getElementById('error-message').textContent = 'Server error. Please try again.';
            });
        });
    </script>
</body>
</html>
