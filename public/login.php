<?php
require_once '../src/user/UserService.php';

session_start();

// Set previous page
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SERVER['HTTP_REFERER']) && !str_contains($_SERVER['HTTP_REFERER'], 'login.php')) {
        $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
    }
}

$nickOrEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nickOrEmail = $_POST['nick_or_email'];

    try {
        $userService = UserService::getInstance();
        $userService->loginUser($nickOrEmail, $_POST['password']);

        // Return to the previous page
        $redirectUrl = $_SESSION['previous_page'] ?? 'index.php';
        unset($_SESSION['previous_page']);
        header('Location: ' . $redirectUrl);
        exit();

    } catch (Exception $e) {
        $_SESSION["error"] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Music App</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth_style.css">
    <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body>

<div class="container auth-container">
    <h2>Login</h2>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="error-message">Error occurred while trying to log in: ' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <form action="login.php" method="POST" class="auth-form">
        <div class="form-group">
            <label for="nick_or_email">Nickname or Email</label>
            <input type="text" id="nick_or_email" name="nick_or_email"  value="<?php echo htmlspecialchars($nickOrEmail); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-action">
            <button class="button-back" type="submit">
                <span class="button-front">Login</span>
            </button>
        </div>
        <a href="<?php echo isset($_SESSION['previous_page']) ? htmlspecialchars($_SESSION['previous_page']) : 'index.php'; ?>" class="auth-link"><span class="fa fa-arrow-left" aria-hidden="true"></span> Go back</a>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

</body>
</html>
