<?php
require_once 'Song.php';
require_once 'User.php';
require_once 'Database.php';
class SearchService {
    private static $instance;

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new SearchService(Database::getInstance());
        }
        return self::$instance;
    }

    public function searchSongsByAnything($anything, $sortBy = 'nothing') {
        if (empty($anything)) {
            return [];
        }

        $results = $this->db->getSongsByAnything($anything, $sortBy);
        return $this->mapResultsToSongs($results);
    }

    public function searchSongsByTitle($title, $sortBy = 'nothing') {
        if (empty($title)) {
            return [];
        }

        $results = $this->db->getSongsByTitle($title, $sortBy);
        return $this->mapResultsToSongs($results);
    }

    public function searchSongsByAuthor($authorName, $sortBy = 'nothing') {
        if (empty($authorName)) {
            return [];
        }

        $results = $this->db->getSongsByAuthor($authorName, $sortBy);
        return $this->mapResultsToSongs($results);
    }

    public function searchSongsByAuthorId($author, $sortBy = 'nothing') {
        if (empty($author)) {
            return [];
        }

        $results = $this->db->getSongsByAuthorId($author, $sortBy);
        return $this->mapResultsWithoutAuthorToSongs($results, $author);
    }
    
    public function searchSongsRandomly($limit) {
        if ($limit <= 0) {
            return [];
        }

        $results = $this->db->getSongsRandomly($limit);
        return $this->mapResultsToSongs($results);
    }

    /* == Map functions == */

    /**
     * @param $results Array containing song and author data
     * @return array
     */
    private function mapResultsToSongs($results) {
        $songs = [];
        foreach ($results as $result) {
            $author = new User($result['user_id'], $result['nick'], $result['email'], $result['name'], $result['surname'], $result['profile_image']);
            $songs[] = new Song($result['song_id'], $result['title'], $author, $result['song_image'], $result['plays'], $result['upvotes'], $result['downvotes']);
        }
        return $songs;
    }

    /**
     * @param $results Array containing song data only
     * @param $author User object of the author of the songs
     * @return array
     */
    private function mapResultsWithoutAuthorToSongs($results, $author) {
        $songs = [];
        foreach ($results as $result) {
            $songs[] = new Song($result['song_id'], $result['title'], $author, $result['song_image'], $result['plays'], $result['upvotes'], $result['downvotes']);
        }
        return $songs;
    }
}
