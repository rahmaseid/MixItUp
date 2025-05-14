<?php
header('Content-Type: application/json');
session_start();
require_once '../includes/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "Unauthorized. Please log in."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$title = isset($_POST['title']) ? trim($_POST['title']) : 'Untitled Mixtape';
$songs_json = isset($_POST['songs']) ? $_POST['songs'] : null;

// Validate input
if (!$songs_json) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing song list."]);
    exit;
}

$songs = json_decode($songs_json, true);
if (!is_array($songs) || empty($songs)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid or empty song list."]);
    exit;
}

try {
    $conn->begin_transaction();

    
    $stmt = $conn->prepare("INSERT INTO playlists (user_id, title) VALUES (?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('is', $user_id, $title);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $playlist_id = $conn->insert_id;
    $stmt->close();

    
    $stmt_lookup = $conn->prepare("SELECT song_id FROM songs WHERE video_id = ?");
    if (!$stmt_lookup) {
        throw new Exception("Prepare lookup failed: " . $conn->error);
    }

    $stmt_insert_song = $conn->prepare("INSERT INTO songs (video_id, title, duration, url) VALUES (?, ?, ?, ?)");
    if (!$stmt_insert_song) {
        throw new Exception("Prepare song insert failed: " . $conn->error);
    }

    $stmt_insert_playlist_song = $conn->prepare("INSERT INTO playlist_songs (playlist_id, song_id, position) VALUES (?, ?, ?)");
    if (!$stmt_insert_playlist_song) {
        throw new Exception("Prepare playlist song insert failed: " . $conn->error);
    }

    
    $position = 1;
    foreach ($songs as $video_id) {
        // Clean the video ID
        $video_id = trim($video_id);
        if (empty($video_id)) continue;

        // Check if song exists
        $song_id = null;
        $stmt_lookup->bind_param('s', $video_id);
        if (!$stmt_lookup->execute()) {
            throw new Exception("Lookup execute failed: " . $stmt_lookup->error);
        }
        
        $stmt_lookup->store_result();
        
        // If song doesn't exist, create it
        if ($stmt_lookup->num_rows === 0) {
            // Get video info from YouTube API
            $api_key = 'AIzaSyB3kARbw6-7x133tQTpLcriW4X1DfX16a0'; // Replace with your actual key
            $api_url = "https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=$video_id&key=$api_key";
            $response = file_get_contents($api_url);
            
            if ($response === FALSE) {
                throw new Exception("Failed to fetch video metadata for $video_id");
            }
            
            $data = json_decode($response, true);
            
            if (empty($data['items'])) {
                // Use default values if YouTube API fails
                $title = "YouTube Video " . substr($video_id, 0, 6);
                $duration = 0;
            } else {
                $item = $data['items'][0];
                $title = $item['snippet']['title'];
                $duration_iso = $item['contentDetails']['duration'];
                
                // Convert ISO 8601 duration to seconds
                $interval = new DateInterval($duration_iso);
                $duration = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
            }
            
            $url = "https://www.youtube.com/watch?v=$video_id";
            
            // Insert new song
            $stmt_insert_song->bind_param('ssis', $video_id, $title, $duration, $url);
            if (!$stmt_insert_song->execute()) {
                throw new Exception("Song insert failed: " . $stmt_insert_song->error);
            }
            
            $song_id = $stmt_insert_song->insert_id;
        } else {
            // Song exists, get its ID
            $stmt_lookup->bind_result($song_id);
            $stmt_lookup->fetch();
        }
        
        // Insert into playlist_songs
        $stmt_insert_playlist_song->bind_param('iii', $playlist_id, $song_id, $position);
        if (!$stmt_insert_playlist_song->execute()) {
            throw new Exception("Playlist song insert failed: " . $stmt_insert_playlist_song->error);
        }
        
        $position++;
    }

    // Clean up
    $stmt_lookup->close();
    $stmt_insert_song->close();
    $stmt_insert_playlist_song->close();

    $conn->commit();
    echo json_encode([
        "success" => true, 
        "playlist_id" => $playlist_id,
        "message" => "Mixtape created successfully"
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    error_log("Playlist creation error: " . $e->getMessage());
    echo json_encode([
        "success" => false, 
        "error" => "Failed to create playlist",
        "details" => $e->getMessage()
    ]);
}

$conn->close();
?>