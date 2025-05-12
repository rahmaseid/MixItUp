<?php
    require_once '../includes/db.php';
    require_once '../auth/session_check.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mixtape_id = $_POST['mixtape_id'] ?? null;
        $new_title = $_POST['new_title'] ?? '';

        if (!$mixtape_id || empty(trim($new_title))) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit();
        }

        $stmt = $conn->prepare("UPDATE mixtapes SET title = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_title, $mixtape_id, $_SESSION['user_id']);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Update failed or no change made']);
        }
    }
?>