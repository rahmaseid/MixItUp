<?php

// Set Headers & Error Reporting
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pulls in database connection ($conn) from db.php
require_once '../includes/db.php';

// If request is not a POST, return a JSON error and exit
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
    exit;
}

// If feilds are missing, returns a JSON error and exit
if (!isset($_POST["name"], $_POST["email"], $_POST["password"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields."
    ]);
    exit;
}

$name = $_POST["name"];
$email = $_POST["email"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Check if email already exists
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email is already registered."
    ]);
    exit;
}
$stmt->close(); // Free up the statement

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

// If prepare() fails, it returns a database error in JSON format
if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

// Binds the values securely 
$stmt->bind_param("sss", $name, $email, $password);

try {
    // Insert the data into the database
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Registered successfully!"
    ]);
} catch (mysqli_sql_exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Email already registered or database error: " . $e->getMessage()
    ]);
}
