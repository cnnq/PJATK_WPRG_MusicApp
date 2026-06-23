<?php
require_once __DIR__ . '/../user/User.php';
require_once __DIR__ . '/../user/UserService.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // User must be logged in to update profile image
    if (!isset($_SESSION['user'])) {
        $_SESSION['error'] = "You must be logged in to update your profile image.";
        header('Location: ../../public/login.php');
        exit();
    }


    // Check if the form was submitted successfully
    if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Error occurred while uploading a file: ' . $_FILES['profile_image']['error'];
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Get file info
    $sourcePath = $_FILES['profile_image']['tmp_name'];
    $fileName = $_FILES['profile_image']['name'];
    $fileSize = $_FILES['profile_image']['size'];
    $fileType = $_FILES['profile_image']['type'];
    $fileNameComponents = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameComponents));

    $allowedFileExtensions = array('jpg', 'gif', 'png', 'jpeg');

    // Check if the file is an image
    if (!in_array($fileExtension, $allowedFileExtensions)) {
        $_SESSION['error'] = 'Cannot upload files with extensions other than: ' . implode(',', $allowedFileExtensions);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }


    // Save old file name for later removal
    $user = $_SESSION['user'];
    $oldFileName = $user->getImagePath();

    // Change file name
    $newFileName = 'user_' . $user->getId() . '_' . time() . '.' . $fileExtension;
    $uploadDirectory = '../../assets/profile_images/';
    $destinationPath = $uploadDirectory . $newFileName;

    // Upload file
    if (!move_uploaded_file($sourcePath, $destinationPath)) {
        $_SESSION['error'] = 'Error occurred while uploading a file.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Remove old file
    if (!empty($oldFileName)) {
        $oldFilePath = $uploadDirectory . $oldFileName;
        if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }
    }

    try {
        // Update
        $userService = UserService::getInstance();
        $userService->updateProfileImage($user, $newFileName);
        $_SESSION['success'] = "File was successfully uploaded.";

    } catch (Exception $e) {
        $_SESSION['error'] = "Error occurred while updating database: " . $e->getMessage();
    }
}

header('Location: ../../public/user_panel.php');
exit();
