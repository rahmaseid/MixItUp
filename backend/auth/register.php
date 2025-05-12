<?php

// Set Headers & Error Reporting
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session at the top
session_start();

// Pulls in database connection ($conn) from db.php
require_once '../includes/db.php';

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
    exit;
}

// Validate required fields
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

// Step 1: Check if the email already exists
$checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email is already registered."
    ]);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// Step 2: Insert the new user
$insertStmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

if (!$insertStmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $conn->error
    ]);
    exit;
}

$insertStmt->bind_param("sss", $name, $email, $password);

try {
    $insertStmt->execute();

    // Store the user ID in session
    $_SESSION["user_id"] = $insertStmt->insert_id;

    echo json_encode([
        "status" => "success",
        "message" => "Registered successfully!"
    ]);
    $insertStmt->close();
    exit;

} catch (mysqli_sql_exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
    $insertStmt->close();
    exit;
}