<?php
require_once '../src/Database.php';
require_once '../src/User.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../public/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $user = $_SESSION['user'];
    $author_id = $user->getId();

    if (empty($title)) {
        $_SESSION['error'] = "Song title cannot be empty.";
        header('Location: ../public/user_panel.php');
        exit();
    }

    $image_path = 'default.png';

    if (isset($_FILES['song_image']) && $_FILES['song_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['song_image']['tmp_name'];
        $fileName = $_FILES['song_image']['name'];
        $fileSize = $_FILES['song_image']['size'];
        $fileType = $_FILES['song_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $newFileName = 'user_' . $author_id . '_' . time() . '.' . $fileExtension;
        $uploadFileDir = '../assets/song_images/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image_path = $newFileName;
        } else {
            $_SESSION['error'] = "There was an error moving the uploaded file.";
            header('Location: ../public/user_panel.php');
            exit();
        }
    }

    try {
        $db = Database::getInstance();
        $db->insertSong($title, $author_id, $image_path);
        $_SESSION['success'] = "Song added successfully!";
    } catch (\Exception $e) {
        $_SESSION['error'] = "An error occurred while adding the song: " . $e->getMessage();
    }

    header('Location: ../public/user_panel.php');
    exit();
}
