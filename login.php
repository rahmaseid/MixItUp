<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email']);
$password = $data['password'];

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

try {
    //prepare and bind
    $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        //Save user session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];

        echo json_encode(['success' => true, 'message' => 'Login successful.', 'name' => $user['name']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}
