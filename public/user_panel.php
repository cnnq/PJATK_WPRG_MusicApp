<?php
require_once '../src/song/Song.php';
require_once '../src/song/SearchService.php';
require_once '../src/user/User.php';
require_once '../src/UIHelpers.php';

session_start();

// Set previous page
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SERVER['HTTP_REFERER']) && !str_contains($_SERVER['HTTP_REFERER'], 'user_panel.php')) {
        $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
    }
}

$search_query = isset($_GET['search-query']) ? trim($_GET['search-query']) : '';
$songs = [];

if (!isset($_SESSION["user"])) {
    $_SESSION["error"] = "You must be logged in to view user panel.";
    header('Location: login.php');
    exit();
}

try {
    $searchService = SearchService::getInstance();
    $songs = $searchService->searchSongsByAuthorId($_SESSION["user"]);

} catch (Exception $e) {
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
    <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="js/user_panel.js" defer></script>
    <script src="js/audio_player.js" defer></script>
</head>
<body>

<?php echo generateBar(); ?>
<?php echo generateBackButton(); ?>

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
                <form action="../src/scripts/update_profile_image.php" method="POST" enctype="multipart/form-data">
                    <div class="image-input">
                        <?php
                        $user = $_SESSION["user"];
                        $imagePath = $user->getImagePath() ?? "default.png";

                        echo '
                        <object data="../assets/profile_images/default.png" type="image/*">
                            <img id="profile-preview" src="../assets/profile_images/' . htmlspecialchars($imagePath) . '" alt="User profile image">
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
                ?>
                <br>
                <form class="inline-form" action="../src/scripts/update_profile_name.php" method="POST">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user->getName()); ?>">
                    <button type="submit" class="inline-button">Update</button>
                </form>
                <form class="inline-form" action="../src/scripts/update_profile_surname.php" method="POST">
                    <label for="surname">Surname:</label>
                    <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user->getSurname()); ?>">
                    <button type="submit" class="inline-button">Update</button>
                </form>
            </div>
        </div>
        <hr>
        <h3>Danger zone</h3>
        <div class="form-group">
            <form class="profile-form" method="POST" action="../src/scripts/change_password.php"
                  onsubmit="return confirm('Are you sure you want to change your password?');">
                <button type="submit" class="plain-button profile-button-danger">
                    Change password
                </button>
                <input type="password" name="old_password" placeholder="Old password" required>
                <input type="password" name="new_password" placeholder="New password" required>
                <input type="password" name="confirm_new_password" placeholder="Confirm new password" required>
            </form>
            <form class="profile-form" method="POST" action="../src/scripts/delete_account.php"
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
            <form class="songs-form" action="../src/scripts/add_song.php" method="POST" enctype="multipart/form-data">
                <div class="image-input">
                    <img id="song-preview" src="../assets/song_images/default.png" alt="Song thumbnail">
                    <label for="song-image-upload" class="image-input-label">
                        <span class="fa fa-cloud-upload"></span> Add image
                    </label>
                    <input id="song-image-upload" class="image-upload" type="file" name="song_image"
                           accept="image/*" onchange="previewImage(this, 'song-preview')"/>
                </div>
                <div class="song-details">
                    <input type="text" name="title" placeholder="Song title" required>
                    <input id="song-file-upload" placeholder="Song file" type="file" name="song_file" accept="audio/*" >
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
