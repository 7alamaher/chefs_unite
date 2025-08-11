<?php
session_start();
require 'db.php'; // DB connection

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);  // matches input name="password"

    $stmt = $conn->prepare("SELECT user_id, username, email, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
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
            <li class="sign"><a href="SignUp.php">Sign up or Log in!</a></li>
        </ul>
    </nav>

	<div class="login-container">
	    <div class="loginimage">
	        <form class="loginboxes" method="POST" action="">
                <?php if (!empty($error)) : ?>
                    <p style="color:red; margin-bottom: 15px;"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                
		        <input type="email" placeholder="E-mail" name="email" required class="search" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
		        <input type="password" placeholder="Password" name="password" required class="search">
		        <button type="submit" class="sign">Login</button>
	        </form>
	    </div>

	    <div class="signupoffer">
		    <h1>New Here?</h1>
		    <p>Join our kitchen! Create an account and share your favorite meals.</p>
		    <button class="sign" onclick="window.location.href='SignUp.php'">Sign Up</button>
	    </div>
    </div>
</body>
</html>
