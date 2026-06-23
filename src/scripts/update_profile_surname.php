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
    $surname = isset($_POST['surname']) ? trim($_POST['surname']) : '';

    try {
        $userService = UserService::getInstance();
        $userService->updateSurname($user, $surname);
        $_SESSION['success'] = "Profile surname updated successfully.";

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
