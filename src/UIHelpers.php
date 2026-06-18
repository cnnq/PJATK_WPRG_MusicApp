<?php
require_once 'Song.php';
require_once 'User.php';

function generateSearchForm() {
    $currentQuery = isset($_GET['search-query']) ? htmlspecialchars($_GET['search-query']) : '';
    $currentSearchBy = isset($_GET['search-by']) ? $_GET['search-by'] : 'anything';
    $currentSortBy = isset($_GET['sort-by']) ? $_GET['sort-by'] : 'nothing';

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

function generateBar() {
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

function generateVoteBar($song) {
    $upvotes = $song->getUpvotes();
    $downvotes = $song->getDownvotes();

    $totalVotes = $upvotes + $downvotes;
    if ($totalVotes == 0) return '';

    $upPercentage = round(($upvotes / $totalVotes) * 100);
    $downPercentage = round(($downvotes / $totalVotes) * 100);

    return '
    <div class="vote-bar-container">
        <div class="vote-bar" title="up: ' . $upvotes . ', down: ' . $downvotes .'" >
            <div class="upvote" style="width:' . $upPercentage . '%;"></div>
            <div class="downvote" style="width:' . $downPercentage . '%;"></div>
        </div>
    </div>';
}

function generatePlayButton() {
    return '
    <button class="button-back" type="submit">
        <span class="button-front"><span class="fa fa-play"></span></span>
    </button>';
}

/**
 * @param $song Song Song for which a result form will be generated
 */
function generateSongResult($song) {
    $author = $song->getAuthor();

    return'
    <li class="song-result">
        <div class="song-image">
            <object data="../assets/song_images/default.png" type="image/png">
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
        generatePlayButton() .
    '</li>';
}

function generateAuthButtons() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) {
        return '
        <div class="auth-buttons-fixed">
            <a href="user_panel.php" class="plain-button auth-button-panel">Logged as <br>'. htmlspecialchars($_SESSION['user']->getNick()) . '</a>
            <a href="../scripts/logout.php" class="plain-button auth-button-logout">Log Out</a>
        </div>';

    } else {
        return '
    <div class="auth-buttons-fixed">
        <a href="login.php" class="plain-button auth-button-login">Log In</a>
        <a href="register.php" class="plain-button auth-button-register">Register</a>
    </div>';
    }
}
