<?php
require_once __DIR__ . '/../song/SongService.php';
require_once __DIR__ . '/../vote/VoteService.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if(!isset($_SESSION['user'])) {
        $_SESSION['error'] = "You must be logged in to vote.";
        header('Location: ../../public/login.php');
        exit();
    }

    $user = $_SESSION['user'];
    $song_id = $_GET['song_id'];
    $is_up = $_GET['is_up'];

    $service = SongService::getInstance();
    $song = $service->getSong($song_id);

    $service = VoteService::getInstance();
    $service->updateVote($user, $song, $is_up);
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();