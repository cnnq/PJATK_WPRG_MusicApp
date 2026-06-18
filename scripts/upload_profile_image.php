<?php
require_once '../src/User.php';
require_once '../src/UserService.php';

session_start();

// How did you get here?
if (!isset($_SESSION['user'])) {
    header('Location: ../public/login.php');
    exit();
}

// Check if the form was submitted successfully
if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Error occurred while uploading a file: ' . $_FILES['profile_image']['error'];
    header('Location: ../public/login.php');
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
    header('Location: ../public/login.php');
    exit();
}

// Save old file name for later removal
$user = $_SESSION['user'];
$oldFileName = $user->getImageName();

// Change file name
$newFileName = 'user_' . $user->getId() . '_' . time() . '.' . $fileExtension;
$uploadDirectory = '../assets/profile_images/';
$destinationPath = $uploadDirectory . $newFileName;

// Upload file
if (!move_uploaded_file($sourcePath, $destinationPath)) {
    $_SESSION['error'] = 'Error occurred while uploading a file.';
    header('Location: ../public/user_panel.php');
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
    $user->setImageName($newFileName);
    $userService = UserService::getInstance();
    $userService->updateProfileImage($user, $newFileName);

    $_SESSION['success'] = "File was successfully uploaded.";

} catch (Exception $e) {
    $_SESSION['error'] = "Error occurred while updating database: " . $e->getMessage();
}


header('Location: ../public/user_panel.php');
exit();
