<?php
require_once __DIR__ . '/../user/User.php';
require_once __DIR__ . '/../song/SongService.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // User must be logged in to add a new song
    if (!isset($_SESSION['user'])) {
        $_SESSION['error'] = "You must be logged in to upload a song.";
        header('Location: ../../public/login.php');
        exit();
    }

    if (empty($_POST['title'])) {
        $_SESSION['error'] = "Song title cannot be empty.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $author_id = $_SESSION['user']->getId();
    $title = trim($_POST['title']);


    $image_path = null;

    // Save image if uploaded
    if (isset($_FILES['song_image']) && $_FILES['song_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['song_image']['tmp_name'];
        $fileName = $_FILES['song_image']['name'];
        $fileNameComponents = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameComponents));

        $newFileName = 'user_' . $author_id . '_' . time() . '.' . $fileExtension;
        $destinationDir = '../../assets/song_images/';
        $destinationPath = $destinationDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            $image_path = $newFileName;

        } else {
            $_SESSION['error'] = "There was an error moving the uploaded image.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    $song_path = null;

    // Save song file if uploaded
    if (isset($_FILES['song_file']) && $_FILES['song_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['song_file']['tmp_name'];
        $fileName = $_FILES['song_file']['name'];
        $fileNameComponents = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameComponents));

        $newFileName = 'user_' . $author_id . '_' . time() . '.' . $fileExtension;
        $destinationDir = '../../assets/songs/';
        $destinationPath = $destinationDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            $song_path = $newFileName;

        } else {
            $_SESSION['error'] = "There was an error moving the uploaded sound file.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    try {
        $service = SongService::getInstance();
        $service->insertSong($title, $author_id, $image_path, $song_path);
        $_SESSION['success'] = "Song added successfully!";

    } catch (Exception $e) {
        $_SESSION['error'] = "An error occurred while adding the song: " . $e->getMessage();
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
