<?php
require_once '../user/UserService.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) {
        $_SESSION['error'] = "You must be logged in to update your profile details.";
        header('Location: ../../public/login.php');
        exit();
    }

    $user = $_SESSION['user'];
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';

    try {
        $userService = UserService::getInstance();
        $userService->updateName($user, $name);
        $_SESSION['success'] = "Profile name updated successfully.";

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
