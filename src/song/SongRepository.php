<?php
require_once __DIR__ . '/../Database.php';

class SongRepository {

    private static ?SongRepository $instance = null;

    private Database $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public static function getInstance(): SongRepository {
        if (self::$instance === null) {
            self::$instance = new SongRepository(Database::getInstance());
        }
        return self::$instance;
    }


    /**
     * @param $anything string Value that will be matched against song title, author's nick, name and surname
     * @param $sortBy string Value determining the order of results. Possible values: newest, title, plays or upvotes
     * @return array
     */
    public function getSongsByAnything(string $anything, string $sortBy): array {
        $sql = "
            SELECT songs.song_id AS song_id, title, songs.image_path AS song_image, song_path, plays, upvotes, downvotes,
                   users.user_id AS user_id, nick, email, name, surname, users.image_path AS profile_image
            FROM songs
            LEFT JOIN users ON songs.author_id = users.user_id
            WHERE title LIKE ? OR nick LIKE ? OR name LIKE ? OR surname LIKE ? " .
            $this->getOrderBySql($sortBy);

        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(['%' . $anything . '%', '%' . $anything . '%', '%' . $anything . '%', '%' . $anything . '%']);
        return $query->fetchAll();
    }

    /**
     * @param $title String Value that will be matched against song title
     * @param $sortBy String Value determining the order of results. Possible values: newest, title, plays or upvotes
     * @return array
     */
    public function getSongsByTitle(string $title, string $sortBy): array {
        $sql = "
            SELECT songs.song_id AS song_id, title, songs.image_path AS song_image, song_path, plays, upvotes, downvotes,
                   users.user_id AS user_id, nick, email, name, surname, users.image_path AS profile_image
            FROM songs
            LEFT JOIN users ON songs.author_id = users.user_id
            WHERE title LIKE ? " .
            $this->getOrderBySql($sortBy);

        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(['%' . $title . '%']);
        return $query->fetchAll();
    }

    /**
     * @param string $authorName Value that will be matched against author's nick, name and surname
     * @param string $sortBy Value determining the order of results. Possible values: newest, title, plays or upvotes
     * @return array
     */
    public function getSongsByAuthor(string $authorName, string $sortBy): array {
        $sql = "
            SELECT songs.song_id AS song_id, title, songs.image_path AS song_image, song_path, plays, upvotes, downvotes,
                   users.user_id AS user_id, nick, email, name, surname, users.image_path AS profile_image
            FROM songs
            LEFT JOIN users ON songs.author_id = users.user_id
            WHERE nick LIKE ? OR name LIKE ? OR surname LIKE ? " .
            $this->getOrderBySql($sortBy);

        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(['%' . $authorName . '%', '%' . $authorName . '%', '%' . $authorName . '%']);
        return $query->fetchAll();
    }

    /**
     * @param int $author_id Exact author whose songs will be returned
     * @param string $sortBy Value determining the order of results. Possible values: newest, title, plays or upvotes
     * @return array
     */
    public function getSongsByAuthorId(int $author_id, string $sortBy): array {
        $sql = "
            SELECT song_id, title, image_path AS song_image, song_path, plays, upvotes, downvotes
            FROM songs
            WHERE author_id = ? " .
            $this->getOrderBySql($sortBy);

        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$author_id]);
        return $query->fetchAll();
    }

    /**
     * Returns a random number of songs from the database
     * @param int $limit Maximum number of songs to be returned
     * @return array
     */
    public function getSongsRandomly(int $limit = 10): array {
        $sql = "
            SELECT songs.song_id AS song_id, title, songs.image_path AS song_image, song_path, plays, upvotes, downvotes,
                   users.user_id AS user_id, nick, email, name, surname, users.image_path AS profile_image
            FROM songs
            LEFT JOIN users ON songs.author_id = users.user_id
            ORDER BY RAND()
            LIMIT ?";

        $query = $this->db->getPdo()->prepare($sql);
        $query->bindValue(1, $limit, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Increments the play count for a specified song
     * @param int $song_id ID of the song
     * @return void
     */
    public function incrementPlays(int $song_id): void {
        $sql = "UPDATE songs SET plays = plays + 1 WHERE song_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$song_id]);
    }

    public function getSong(int $song_id) {
        $sql = "
            SELECT songs.song_id AS song_id, title, songs.image_path AS song_image, song_path, plays, upvotes, downvotes,
                   users.user_id AS user_id, nick, email, name, surname, users.image_path AS profile_image
            FROM songs
            LEFT JOIN users ON songs.author_id = users.user_id
            WHERE songs.song_id = ?";

        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$song_id]);
        return $query->fetch();
    }

    /**
     * Inserts a new song into the database
     * @param string $title
     * @param int $author_id
     * @param string $image_path
     * @param string $song_path
     * @return void
     */
    public function insertSong(string $title, int $author_id, ?string $image_path, ?string $song_path): void {
        $sql = "INSERT INTO songs (title, author_id, image_path, song_path) VALUES (?, ?, ?, ?)";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$title, $author_id, $image_path, $song_path]);
    }

    /**
     * Deletes all songs uploaded by a specified user
     * @param int $author_id
     * @return void
     */
    public function deleteSongsByAuthor(int $author_id): void {
        $sql = "DELETE FROM songs WHERE author_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$author_id]);
    }

    private function getOrderBySql(string $sortBy): string {
        return match ($sortBy) {
            'newest' => "ORDER BY song_id DESC",
            'title' => "ORDER BY title ASC",
            'plays' => "ORDER BY plays DESC",
            'upvotes' => "ORDER BY upvotes DESC",
            default => "",
        };
    }
}