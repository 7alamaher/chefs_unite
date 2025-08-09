<?php
session_start();
echo "🚀 Chef Unite backend is live!";
if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    header("Location: home.php");
} else {
    header("Location: SignIn.php");
}
exit();
?>
