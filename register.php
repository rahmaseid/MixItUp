<?php
    $host = 'localhost';
    $dbname = 'mixtape_db';
    $username = 'root';
    $password = '';

    // Connect to database
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if($conn->connect_error) 
    {
        die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
    }

    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $conn->real_escape_string($data['name']);
    $email = $conn->real_escape_string($data['email']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Check if email exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if($check->num_rows > 0) 
    {
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit;
    }

    // Insert user
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    if($conn->query($sql) === TRUE) 
    {
        echo json_encode(['success' => true, 'message' => 'Registration successful.']);
    } 
    else 
    {
        echo json_encode(['success' => false, 'message' => 'Registration failed.']);
    }

    $conn->close();
?>