<?php
require_once __DIR__ . '/song/Song.php';
require_once __DIR__ . '/user/User.php';

/**
 * Generates the HTML for a search form
 * @return string
 */
function generateSearchForm(): string {
    $currentQuery = isset($_GET['search-query']) ? htmlspecialchars($_GET['search-query']) : '';
    $currentSearchBy = $_GET['search-by'] ?? 'anything';
    $currentSortBy = $_GET['sort-by'] ?? 'nothing';

    // Options
    $searchByOptions = [
        'anything' => 'Search by',
        'title' => 'Title',
        'author' => 'Author'
    ];

    $sortByOptions = [
        'nothing' => 'Sort by',
        'newest' => 'Newest',
        'title' => 'Title',
        'plays' => 'Most Played',
        'upvotes' => 'Most Upvoted'
    ];

    // Generate HTML for `select` tags
    $searchByHtml = '<select name="search-by" class="select">';
    foreach ($searchByOptions as $value => $label) {
        $selected = ($value === $currentSearchBy) ? ' selected' : '';
        $searchByHtml .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
    }
    $searchByHtml .= '</select>';

    $sortHtml = '<select name="sort-by" class="select">';
    foreach ($sortByOptions as $value => $label) {
        $selected = ($value === $currentSortBy) ? ' selected' : '';
        $sortHtml .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
    }
    $sortHtml .= '</select>';

    // Generate the search form
    return '
    <form class="search-form" action="../public/search.php" method="GET">
        <div class="search-input">
            <input type="text" name="search-query" placeholder="Search for songs, artists, whatever..." value="' . $currentQuery . '">
            <button class="button-back" type="submit">
                <span class="button-front">Search</span>
            </button>
        </div>
        <div class="search-options">
            ' . $searchByHtml . '
            ' . $sortHtml . '
        </div>
    </form>';
}

/**
 * Generates the HTML for the bar at the top of the page
 * @return string
 */
function generateBar(): string {
    $currentQuery = isset($_GET['search-query']) ? htmlspecialchars($_GET['search-query']) : '';

    // Generate the search form
    return '
    <div class="bar">
    <h1><a href="index.php" style="color: inherit; text-decoration: none;">Music App</a></h1>
        <form class="search-form" action="../public/search.php" method="GET">
            <div class="search-input">
                <input type="text" name="search-query" placeholder="Search for songs, artists, whatever..." value="' . $currentQuery . '">
                <button class="button-back" type="submit">
                    <span class="button-front">Search</span>
                </button>
            </div>
        </form>
    </div>';
}

/**
 * Generates the HTML for the vote bar (in the song result)
 * @param $song Song Song for which the vote bar will be generated
 * @return string
 */
function generateVoteBar(Song $song): string {
    $upvotes = $song->getUpvotes();
    $downvotes = $song->getDownvotes();

    $totalVotes = $upvotes + $downvotes;
    if ($totalVotes == 0) return '';

    $upPercentage = round(($upvotes / $totalVotes) * 100);
    $downPercentage = round(($downvotes / $totalVotes) * 100);

    return '
    <div class="vote-bar-container" id="vote-bar-container-' . $song->getId() . '">
        <div class="vote-bar" title="up: ' . $upvotes . ', down: ' . $downvotes .'" >
            <div class="upvote" style="width:' . $upPercentage . '%;"></div>
            <div class="downvote" style="width:' . $downPercentage . '%;"></div>
        </div>
    </div>';
}

/**
 * Generates the HTML for the buttons at the bottom of the song result
 * @param $song Song Song for which the buttons will be generated
 * @return string
 */
function generateSongResultButtons(Song $song): string {
    $song_id = $song->getId();
    $html = '<div class="song-result-buttons">';

    // Play button
    if ($song->getSongPath()) {
        $html .= '
            <audio id="audio-' . $song->getId() . '" src="../assets/songs/' . htmlspecialchars($song->getSongPath()) . '"></audio>
            <button class="button-back play-button" type="button" onclick="togglePlay(' . $song->getId() . ')">
                <span class="button-front">
                    <span id="icon-' . $song->getId() . '" class="fa fa-play"></span>
                </span>
            </button>
        ';
    }

    if (isset($_SESSION['user'])) {
        $upActive = '';
        $downActive = '';

        switch ($song->getCurrentUserVote()) {
            case VoteStatus::UP:
                $upActive = 'active';
                break;
            case VoteStatus::DOWN:
                $downActive = 'active';
                break;
            case VoteStatus::NONE:
                break;
        }

        $html .= '
            <a href="../src/scripts/vote.php?song_id=' . $song_id . '&is_up=1"
               class="vote-button upvote-button ' . $upActive . '">
                <span class="fa fa-thumbs-up"></span>
            </a>
            <a href="../src/scripts/vote.php?song_id=' . $song_id . '&is_up=0" 
               class="vote-button downvote-button ' . $downActive . '">
                <span class="fa fa-thumbs-down"></span>
            </a>
        ';
    }

    $html .= '</div>';

    return $html;
}

/**
 * Generates the HTML for a song result
 * @param $song Song Song for which a result form will be generated
 */
function generateSongResult(Song $song): string {
    $author = $song->getAuthor();

    return'
    <li class="song-result">
        <div class="song-image">
            <object data="../assets/song_images/default.png" type="image/*">
                <img src="../assets/song_images/' . htmlspecialchars($song->getImagePath()) . '" alt="' . htmlspecialchars($song->getTitle()) . ' thumbnail">
            </object>
        </div>
        <div class="song-info">
            <h3>' . htmlspecialchars($song->getTitle()) . '</h3>
            <div class="song-details">
                <div class="song-author">By: <span class="dark">' . htmlspecialchars($author->getName()) . ' ' . htmlspecialchars($author->getSurname()) . ' (' . htmlspecialchars($author->getNick()) . ')</span></div>
                <div class="song-play-info">
                    <div class="song-play-count"> Plays:  <span class="dark">' . $song->getPlays() . '</span></div>' .
                    generateVoteBar($song) .
                '</div>
            </div>
        </div>' .
        generateSongResultButtons($song) .
    '</li>';
}

/**
 * Generates the HTML for the buttons in the bottom left corner of the page
 * @return string
 */
function generateAuthButtons(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) {
        return '
        <div class="fixed-button-container">
            <a href="user_panel.php" class="plain-button button-panel">Logged as <br>'. htmlspecialchars($_SESSION['user']->getNick()) . '</a>
            <a href="../src/scripts/logout.php" class="plain-button button-logout">Log Out</a>
        </div>';

    } else {
        return '
        <div class="fixed-button-container">
            <a href="login.php" class="plain-button button-login">Log In</a>
            <a href="register.php" class="plain-button button-register">Register</a>
        </div>';
    }
}

/**
 * Generates the HTML for the button that takes the user back to the previous page
 * @return string
 */
function generateBackButton(): string {
    $previous_page = $_SESSION['previous_page'] ?? 'index.php';

    return '
    <div class="fixed-button-container">
        <a href="' . $previous_page . '" class="plain-button button-go-back">Go back</a>
    </div>';
}
