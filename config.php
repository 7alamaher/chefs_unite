// looks like we need to delete this file 
<?php
$host = 'localhost';
$dbname = 'chief_unite'; // or your actual DB name
$username = 'root';
$password = ''; // empty for XAMPP by default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

