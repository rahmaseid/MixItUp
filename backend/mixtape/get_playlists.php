<?php
 // get_playlists.php
 session_start();
 require_once '../includes/db.php';
 header('Content-Type: application/json');
 
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
 
 // Execute the query
 if ($stmt = $conn->prepare(
     "SELECT playlist_id, title, created_at
      FROM playlists
      WHERE user_id = ?
      ORDER BY created_at DESC"
 )) {
     $stmt->bind_param('i', $user_id);
     $stmt->execute();
     $result = $stmt->get_result();
 
     // 3. Gather results
     $playlists = [];
     while ($row = $result->fetch_assoc()) {
         $playlists[] = [
             'playlist_id' => (int)$row['playlist_id'],
             'title'       => $row['title'],
             'created_at'  => $row['created_at']
         ];
     }
 
     $stmt->close();
     $conn->close();
 
     // return JSON
     echo json_encode(['success'   => true, 'playlists' => $playlists]);
     exit;
 }
 
 // If prepare failed
 $conn->close();
 echo json_encode(['success' => false, 'message' => 'Failed to fetch playlists.']);