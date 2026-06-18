<?php
require_once '../src/UserService.php';

session_start();

// Set previous page
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SERVER['HTTP_REFERER'])) {
        $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
    }
}

$nick = '';
$name = '';
$surname = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nick = isset($_POST['nick']) ? $_POST['nick'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $surname = isset($_POST['surname']) ? $_POST['surname'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    try {
        $userService = UserService::getInstance();
        $userService->registerUser($_POST['nick'], $_POST['email'], $_POST['name'], $_POST['surname'], $_POST['password'], $_POST['confirm_password']);

        // Return to the previous page
        $redirectUrl = isset($_SESSION['register_referer']) ? $_SESSION['register_referer'] : 'index.php';
        unset($_SESSION['register_referer']);
        header('Location: ' . $redirectUrl);
        exit();

    } catch (\Exception $e) {
        $_SESSION["error"] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Music App</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth_style.css">
    <link rel="stylesheet" href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body>

<div class="container auth-container">
    <h2>Register</h2>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="error-message">Error occurred while trying to register: ' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <form action="register.php" method="POST" class="auth-form">
        <div class="form-group">
            <label for="nick" class="required">Nickname</label>
            <input type="text" id="nick" name="nick" value="<?php echo htmlspecialchars($nick); ?>" required>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
        </div>
        <div class="form-group">
            <label for="surname">Surname</label>
            <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($surname); ?>">
        </div>
        <div class="form-group">
            <label for="email" class="required">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="password" class="required">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password" class="required">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="form-action">
            <button class="button-back" type="submit">
                <span class="button-front">Register</span>
            </button>
        </div>
        <a href="index.php" class="auth-link"><span class="fa fa-arrow-left" aria-hidden="true"></span> Back to Home</a>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
