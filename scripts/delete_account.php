<?php
require_once '../src/User.php';
require_once '../src/UserService.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = isset($_POST['password']) ? $_POST['password'] : '';

    try {
        $user = $_SESSION['user'];

        $userService = UserService::getInstance();
        $userService->deleteUser($user, $password);

        // Delete profile image
        if (!empty($user->getImageName())) {
            $profileImagePath = '../assets/profile_images/' . $user->getImageName();
            if (file_exists($profileImagePath)) {
                unlink($profileImagePath);
            }
        }

        require 'logout.php';

    } catch (\Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header('Location: ../public/user_panel.php');
    exit();
}
