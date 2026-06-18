<?php
require_once '../src/User.php';
require_once '../src/Song.php';
require_once '../src/SearchService.php';
require_once '../src/UIHelpers.php';

session_start();

$search_query = isset($_GET['search-query']) ? trim($_GET['search-query']) : '';
$songs = [];

try {
    $searchService = SearchService::getInstance();
    $songs = $searchService->searchSongsByAuthorId($_SESSION["user"]);

} catch (\Exception $e) {
    $_SESSION["error"] = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music App</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/search_style.css">
    <link rel="stylesheet" href="css/user_panel_style.css">
    <link rel="stylesheet" href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="js/user_panel.js" defer></script>
</head>
<body>

<?php echo generateBar(); ?>

<div class="container user-panel">
    <div class="user-panel-nav">
        <a class="nav-button active">Profile</a>
        <a class="nav-button">My Songs</a>
    </div>

    <!-- Profile -->
    <div class="tab profile">
        <h2>Profile</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <h3>Account information</h3>
        <div class="profile-info">
            <div class="profile-image">
                <form action="../scripts/upload_profile_image.php" method="POST" enctype="multipart/form-data">
                    <div class="image-input">
                        <?php
                        $user = $_SESSION["user"];
                        $imageName = !empty($user->getImageName()) ? $user->getImageName() : "default.png";

                        echo '
                        <object data="../assets/profile_images/default.png" type="image/png">
                            <img id="profile-preview" src="../assets/profile_images/' . htmlspecialchars($imageName) . '" alt="User profile image">
                        </object>';
                        ?>
                        <label for="profile-image-upload">
                            <span class="fa fa-cloud-upload"></span> Change image
                        </label>
                        <input id="profile-image-upload" class="image-upload" type="file" name="profile_image"
                               accept="image/*"
                               onchange="previewImage(this, 'profile-preview'); this.form.submit()"/>
                    </div>
                </form>
            </div>
            <div class="profile-details">
                <?php
                echo '<div>Nick: <span class="dark">' . $user->getNick() . '</span></div>';
                echo '<div>Email: <span class="dark">' . $user->getEmail() . '</span></div>';
                echo '<div>Name: <span class="dark">' . $user->getName() . '</span></div>';
                echo '<div>Surname: <span class="dark">' . $user->getSurname() . '</span></div>';
                ?>
            </div>
        </div>
        <hr>
        <h3>Danger zone</h3>
        <div class="form-group">
            <form class="profile-form" method="POST" action="../scripts/change_password.php"
                  onsubmit="return confirm('Are you sure you want to change your password?');">
                <button type="submit" class="plain-button profile-button-danger">
                    Change password
                </button>
                <input type="password" name="old_password" placeholder="Old password" required>
                <input type="password" name="new_password" placeholder="New password" required>
                <input type="password" name="confirm_new_password" placeholder="Confirm new password" required>
            </form>
            <form class="profile-form" method="POST" action="../scripts/delete_account.php"
                  onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                <button type="submit" class="plain-button profile-button-danger">
                    Delete account
                </button>
                <input type="password" name="password" placeholder="Enter password to confirm" required>
            </form>
        </div>
    </div>

    <!-- Songs -->
    <div class="tab songs" style="display: none">
        <h2>My songs</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <h3>Upload new song</h3>
        <div class="form-group">
            <form class="songs-form" action="../scripts/add_song.php" method="POST" enctype="multipart/form-data">
                <div class="image-input">
                    <img id="song-preview" src="../assets/song_images/default.png" alt="Song thumbnail">
                    <label for="song-image-upload" class="image-input-label">
                        <span class="fa fa-cloud-upload"></span> Add image
                    </label>
                    <input id="song-image-upload" class="image-upload" type="file" name="song_image"
                           accept="image/*"
                            onchange="previewImage(this, 'song-preview')"/>
                </div>
                <div class="song-details">
                    <input type="text" name="title" placeholder="Song title" required>
                    <button type="submit" class="button-back" style="width: 100%;">
                        <span class="button-front">Add song</span>
                    </button>
                </div>
            </form>
        </div>
        <hr>
        <div class="song-list">
            <h3>Already uploaded songs</h3>
            <div class="search-results">
                <?php
                // Check for errors
                if (isset($_SESSION['error'])) {
                    echo '<div class="error-message">Error occurred while retrieving songs: ' . htmlspecialchars($_SESSION['error']) . '</div>';
                    unset($_SESSION['error']);

                } else if (empty($songs)) {
                    echo '<div class="error-message">It looks like you have no songs</div>';

                } else {
                    // Generate song results
                    echo '<ul class="result-list">';
                    foreach ($songs as $song) {
                        echo generateSongResult($song);
                    }
                    echo '</ul>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
