<?php
require_once '../src/User.php';
require_once '../src/UserService.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_new_password = isset($_POST['confirm_new_password']) ? $_POST['confirm_new_password'] : '';

    try {
        $userService = UserService::getInstance();
        $userService->changePassword($_SESSION['user'], $old_password, $new_password, $confirm_new_password);

    } catch (\Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header('Location: ../public/user_panel.php');
    exit();
}
