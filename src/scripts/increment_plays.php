<?php
require_once __DIR__ . '/../song/SongService.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $songId = $_POST['song_id'];

    if ($songId) {
        try {
            $service = SongService::getInstance();
            $song = $service->getSong((int)$songId);
            $service->incrementPlays($song);

            echo json_encode(['status' => 'success']);

        } catch (Exception $e) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Song not found']);
        }

    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing song_id']);
    }

} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

exit();