<?php
$dsn = 'mysql:host=localhost;dbname=acme_artist';
$username = 'root'; // Replace with your MySQL username
$password = ''; // Replace with your MySQL password
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO('mysql:host=localhost;dbname=acme_Gallery', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>