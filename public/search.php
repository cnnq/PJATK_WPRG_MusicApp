<?php
require_once '../src/song/SearchService.php';
require_once '../src/UIHelpers.php';

session_start();

// Set previous page
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SERVER['HTTP_REFERER']) && !str_contains($_SERVER['HTTP_REFERER'], 'search.php')) {
        $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
    }
}

$search_query = isset($_GET['search-query']) ? trim($_GET['search-query']) : '';
$search_by = $_GET['search-by'] ?? 'anything';
$sort_by = $_GET['sort-by'] ?? 'nothing';
$songs = [];
$isRandom = false;

try {
    $searchService = SearchService::getInstance();

    if (empty($search_query)) {
        $songs = $searchService->searchSongsRandomly(10);
        $isRandom = true;

    } else {
        $songs = match ($search_by) {
            'title' => $searchService->searchSongsByTitle($search_query, $sort_by),
            'author' => $searchService->searchSongsByAuthor($search_query, $sort_by),
            default => $searchService->searchSongsByAnything($search_query, $sort_by),
        };

        $isRandom = false;
    }

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
</head>
<body>

<?php echo generateBar(); ?>
<?php echo generateAuthButtons(); ?>

<div class="container">
    <div class="search-header">
        <h2>Advanced search</h2>
        <?php echo generateSearchForm(); ?>
    </div>

    <div class="search-results">
        <h2>Search results</h2>
        <?php
        // Check for errors
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">Error occurred while seraching for songs: ' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
            return;

        } else if (empty($songs)) {
            echo '<div class="error-message">Sorry. No songs found matching ' . htmlspecialchars($search_query) . '</div>';
            return;

        } else if (empty($search_query)) {
            echo '<div class="error-message">No search query was provided.</br> Here you have some random results instead.</div>';
        }

        // Generate song results
        echo '<ul class="result-list">';
        foreach ($songs as $song) {
            echo generateSongResult($song);
        }
        echo '</ul>';
        ?>
    </div>
</div>

</body>
</html>
