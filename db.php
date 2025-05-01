<?php
// db.php - Database connection using mysqli

$host = '127.0.0.1';
$port = 3308;
$user = 'root';
$pass = '';
$db   = 'mixtape_db';

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Make sure we use utf8mb4 everywhere
$conn->set_charset('utf8mb4');
