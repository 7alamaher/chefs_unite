<?php
require 'config.php';

$name = 'Hala';
$email = 'hala@example.com';

$stmt = $pdo->prepare("INSERT INTO test_users (name, email) VALUES (?, ?)");
$stmt->execute([$name, $email]);

echo "✅ Test user inserted!";
?>
