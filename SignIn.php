<?php
session_start();
require 'db.php'; // DB connection

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    
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
<html>
<head>
    <title>Sign In</title>
    <style>
        .error { color: red; }
        .form-container {
            max-width: 300px;
            margin: auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Sign In</h2>

    <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>

    <form method="POST" action="">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Sign In</button>
    </form>
</div>

</body>
</html>
