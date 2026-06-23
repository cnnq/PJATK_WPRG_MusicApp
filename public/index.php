<?php
require_once '../src/song/SearchService.php';
require_once '../src/UIHelpers.php';

session_start();

// Set previous page
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SERVER['HTTP_REFERER']) && !str_contains($_SERVER['HTTP_REFERER'], 'index.php')) {
        $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
    }
}

$songs = [];

try {
    $searchService = SearchService::getInstance();
    $songs = $searchService->searchSongsRandomly(10);

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
    <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="js/audio_player.js" defer></script>
    <style>
        #outer-box {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 15vh 0;
            margin: auto;
            background-image: linear-gradient(to bottom, rgba(221, 238, 255, 0.1), rgba(221, 221, 221, 1.0)),
            url("../assets/background.jpg");
            background-size: cover;
            background-position: center;
        }

        #outer-box > * {
            width: 70%;
            max-width: 40rem;
        }

        header {
            background-color: #333380;
            color: #fff;
            padding: 0.7rem 0 0.85rem 0;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 3px 4px rgba(10, 10, 32, 0.4);
        }
    </style>
</head>
<body>

<div id="outer-box">
    <header>
        <h1>Music App</h1>
    </header>

    <?php echo generateSearchForm(); ?>
</div>

<?php echo generateAuthButtons(); ?>

<div class="container">
    <div class="search-results">
        <h2>Recommended for you</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">Error occurred while searching for songs: ' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);

        } else if (empty($songs)) {
            echo '<div class="error-message">No recommended songs are available at the moment.</div>';

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

</body>
</html>
