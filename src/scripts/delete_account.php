<?php
require_once '../song/SongService.php';
require_once '../user/User.php';
require_once '../user/UserService.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // User must be logged in to delete their account
    if (!isset($_SESSION['user'])) {
        $_SESSION['error'] = "You must be logged in to delete your account.";
        header('Location: ../../public/login.php');
        exit();
    }

    $user = $_SESSION['user'];
    $password = $_POST['password'];

    try {
        // Delete profile image
        if (!empty($user->getImagePath())) {
            $profileImagePath = '../../assets/profile_images/' . $user->getImagePath();
            if (file_exists($profileImagePath)) {
                unlink($profileImagePath);
            }
        }

        // Delete uploaded songs
        $songSerivce = SongService::getInstance();
        $songSerivce->deleteSongsByAuthor($user);

        // Delete user
        $userService = UserService::getInstance();
        $userService->deleteUser($user, $password);

        require 'logout.php';

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
