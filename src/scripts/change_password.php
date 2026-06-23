<?php
require_once '../user/User.php';
require_once '../user/UserService.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // User must be logged in to update their password
    if (!isset($_SESSION['user'])) {
        $_SESSION['error'] = "You must be logged in to change your password.";
        header('Location: ../../public/login.php');
        exit();
    }

    $user = $_SESSION['user'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    try {
        $userService = UserService::getInstance();
        $userService->updatePassword($user, $old_password, $new_password, $confirm_new_password);
        $_SESSION['success'] = "Password updated successfully.";

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
