<?php
session_start();
require_once '../includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Please log in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

//Get data from POST request
$title = isset($_POST['title']) ? trim($_POST['title']) : 'Untitled Mixtape';
$songs = isset($_POST['songs']) ? $_POST['songs'] : [];

if (empty($songs) || !is_array($songs)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or empty song list."]);
    exit;
}

try {
    // Insert playlist
    $stmt = $conn->prepare("INSERT INTO playlists (user_id, title) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $title);
    $stmt->execute();
    $playlist_id = $conn->insert_id;
    $stmt->close();

    // Insert songs in order
    $stmt = $conn->prepare("INSERT INTO playlist_songs (playlist_id, song_id, position) VALUES (?, ?, ?)");
    $position = 1;
    foreach ($songs as $song_id) {
        $stmt->bind_param('iii', $playlist_id, $song_id, $position);
        $stmt->execute();
        $position++;
    }
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'playlist_id' => $playlist_id]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create playlist. ' . $e->getMessage()]);
}

$conn->close();