<?php
session_start();

// Destroy the session
$_SESSION = [];
session_destroy();

// Redirect to SignIn.php
header("Location: SignIn.php");
exit;
?>
