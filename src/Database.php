<?php
class Database {
    private static $instance;

    private $pdo;

    public function __construct() {
        $config = require '../config/config.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']};port={$config['port']}";
        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], $config['options']);

        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception("An error occurred while connecting to the database. Please try again later.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // == Songs ==

    /**
     * @param $anything String that will be matched against song title, author's nick, name and surname
     * @return array
     */
    public function getSongsByAnything($anything, $sortBy) {
        $sql = "
            SELECT song_id, title, songs.image_path AS song_image, plays, upvotes, downvotes,
                   user_id, nick, email, name, surname, users.image_path AS profile_image
            FROM songs
            LEFT JOIN users ON songs.author_id = users.user_id
            WHERE title LIKE ? OR nick LIKE ? OR name LIKE ? OR surname LIKE ? " .
            $this->getOrderBySql($sortBy);

        $query = $this->pdo->prepare($sql);
        $query->execute(['%' . $anything . '%', '%' . $anything . '%', '%' . $anything . '%', '%' . $anything . '%']);
        return $query->fetchAll();
    }

    /**
     * @param $title String that will be matched against song title
     * @param $sortBy String determining the order of results
     * @return array
     */
    public function getSongsByTitle($title, $sortBy) {
        $sql = "
            SELECT song_id, title, songs.image_path AS song_image, plays, upvotes, downvotes,
                   user_id, nick, email, name, surname, users.image_path AS profile_image
            FROM songs
            LEFT JOIN users ON songs.author_id = users.user_id
            WHERE title LIKE ? " .
            $this->getOrderBySql($sortBy);

        $query = $this->pdo->prepare($sql);
        $query->execute(['%' . $title . '%']);
        return $query->fetchAll();
    }

    /**
     * @param $authorName String that will be matched against author's nick, name and surname
     * @param $sortBy String determining the order of results
     * @return array
     */
    public function getSongsByAuthor($authorName, $sortBy) {
        $sql = "
            SELECT song_id, title, songs.image_path AS song_image, plays, upvotes, downvotes,
                   user_id, nick, email, name, surname, users.image_path AS profile_image
            FROM songs
            LEFT JOIN users ON songs.author_id = users.user_id
            WHERE nick LIKE ? OR name LIKE ? OR surname LIKE ? " .
            $this->getOrderBySql($sortBy);

        $query = $this->pdo->prepare($sql);
        $query->execute(['%' . $authorName . '%', '%' . $authorName . '%', '%' . $authorName . '%']);
        return $query->fetchAll();
    }

    /**
     * @param $author User Exact author whose songs will be returned
     * @param $sortBy String determining the order of results
     * @return array
     */
    public function getSongsByAuthorId($author, $sortBy) {
        $sql = "
            SELECT song_id, title, image_path AS song_image, plays, upvotes, downvotes
            FROM songs
            WHERE author_id = ? " .
            $this->getOrderBySql($sortBy);

        $query = $this->pdo->prepare($sql);
        $query->execute(array($author->getId()));
        return $query->fetchAll();
    }

    private function getOrderBySql($sortBy) {
        switch ($sortBy) {
            case 'newest':
                return "ORDER BY song_id DESC";

            case 'title':
                return "ORDER BY title ASC";

            case 'plays':
                return "ORDER BY plays DESC";

            case 'upvotes':
                return "ORDER BY upvotes DESC";

            case 'nothing':
            default:
                return "";
        }
    }

    public function getSongsRandomly($limit = 10) {
        $sql = "
            SELECT song_id, title, songs.image_path AS song_image, plays, upvotes, downvotes,
                   user_id, nick, email, name, surname, users.image_path AS profile_image
            FROM songs
            LEFT JOIN users ON songs.author_id = users.user_id
            ORDER BY RAND()
            LIMIT ?";

        $query = $this->pdo->prepare($sql);
        $query->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }

    public function insertSong($title, $author_id, $image_path) {
        $sql = "INSERT INTO songs (title, author_id, image_path) VALUES (?, ?, ?)";
        $query = $this->pdo->prepare($sql);
        $query->execute([$title, $author_id, $image_path]);
    }


    // == Users ==

    public function getUserWithPasswordByEmail($email) {
        $sql = "SELECT user_id, nick, email, password, name, surname, image_path FROM users WHERE email = ?";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($email));
        return $query->fetch();
    }

    public function getUserWithPasswordByNick($nick) {
        $sql = "SELECT user_id, nick, email, password, name, surname, image_path FROM users WHERE nick = ?";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($nick));
        return $query->fetch();
    }

    public function getPasswordByUserId($user_id) {
        $sql = "SELECT password FROM users WHERE user_id = ?";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($user_id));
        return $query->fetch();
    }

    public function insertUser($user, $password) {
        $sql = "INSERT INTO users (nick, email, password, name, surname, image_path) VALUES (?, ?, ?, ?, ?, ?)";
        $query = $this->pdo->prepare($sql);
        $query->execute(array($user->getNick(), $user->getEmail(), password_hash($password, PASSWORD_DEFAULT), $user->getName(), $user->getSurname(), $user->getImageName()));
    }

    public function deleteUser($user_id) {
        $sql = "DELETE FROM users WHERE user_id = ?";
        $query = $this->pdo->prepare($sql);
        $query->execute(array($user_id));
    }

    public function updatePassword($user_id, $password) {
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $query = $this->pdo->prepare($sql);
        $query->execute(array(password_hash($password, PASSWORD_DEFAULT), $user_id));
    }

    public function updateProfileImage($user_id, $image_path) {
        $sql = "UPDATE users SET image_path = ? WHERE user_id = ?";
        $query = $this->pdo->prepare($sql);
        $query->execute(array($image_path, $user_id));
    }

    public function containsNick($nick) {
        $query = $this->pdo->prepare("SELECT user_id FROM users WHERE nick = ?");
        $query->execute(array($nick));
        if ($query->fetch()) {
            return true;
        }
        return false;
    }

    public function containsEmail($email) {
        $query = $this->pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $query->execute(array($email));
        if ($query->fetch()) {
            return true;
        }
        return false;
    }
}
