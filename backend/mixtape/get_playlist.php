<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../auth/session_check.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing playlist ID']);
    exit();
}

$playlist_id = intval($_GET['id']);

// Check if the playlist exists and belongs to the logged-in user
$stmt = $conn->prepare(
    "SELECT p.playlist_id, p.title, p.created_at, s.video_id, s.title AS song_title, s.duration
     FROM playlists p
     JOIN playlist_songs ps ON p.playlist_id = ps.playlist_id
     JOIN songs s ON ps.song_id = s.song_id
     WHERE p.playlist_id = ? AND p.user_id = ?"
);
$stmt->bind_param('ii', $playlist_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Playlist not found or access denied']);
    exit();
}

$playlist = [
    'playlist_id' => $playlist_id,
    'title' => '',
    'created_at' => '',
    'tracks' => []
];

while ($row = $result->fetch_assoc()) {
    if (empty($playlist['title'])) {
        $playlist['title'] = $row['title'];
        $playlist['created_at'] = $row['created_at'];
    }

    $playlist['tracks'][] = [
        'videoId' => $row['video_id'],
        'title' => $row['song_title'],
        'duration' => $row['duration']
    ];
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'playlist' => $playlist]);
?>