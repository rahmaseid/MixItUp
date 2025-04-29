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

    $email = $conn->real_escape_string($data['email']);
    $password_input = $data['password'];

    // Check user
    $sql = "SELECT id, name, password FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if($result->num_rows === 1) 
    {
        $user = $result->fetch_assoc();
        if(password_verify($password_input, $user['password'])) 
        {
            echo json_encode(['success' => true, 'message' => 'Login successful.', 'name' => $user['name']]);
        } 
        else 
        {
            echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
        }
    } 
    else 
    {
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
    }

    $conn->close();
?>