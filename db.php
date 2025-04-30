<?php
// db.php - Database connection

$host = '127.0.0.1:3308';
$db   = 'mixtape_db';        // Replace with your database name
$user = 'root';              // Replace with your DB username
$pass = '';                  // Replace with your DB password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Set PDO error mode and fetch mode
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Show generic error in production, detailed in dev
    die("Database connection failed: " . $e->getMessage());
}
