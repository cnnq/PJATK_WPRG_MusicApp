<?php
require_once 'User.php';
require_once __DIR__ . '/../Database.php';

class UserRepository {
    private static ?UserRepository $instance = null;

    private Database $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public static function getInstance(): UserRepository {
        if (self::$instance === null) {
            self::$instance = new UserRepository(Database::getInstance());
        }
        return self::$instance;
    }

    /* == Gets == */

    /**
     * Returns a user with its hashed password by email
     * @param string $email Email address of the user
     * @return mixed
     */
    public function getUserWithPasswordByEmail(string $email): mixed {
        $sql = "SELECT user_id, nick, email, password, name, surname, image_path FROM users WHERE email = ?";

        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(array($email));
        return $query->fetch();
    }

    /**
     * Returns a user with its hashed password by nick
     * @param string $nick
     * @return mixed
     */
    public function getUserWithPasswordByNick(string $nick): mixed {
        $sql = "SELECT user_id, nick, email, password, name, surname, image_path FROM users WHERE nick = ?";

        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(array($nick));
        return $query->fetch();
    }

    /**
     * Returns the hashed password of a given user
     * @param int $user_id ID of the user
     * @return mixed
     */
    public function getHashedPasswordByUserId(int $user_id): mixed {
        $sql = "SELECT password FROM users WHERE user_id = ?";

        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(array($user_id));
        return $query->fetch();
    }

    /* == Contains == */

    /**
     * Checks if a nick is already registered in the database
     * @param string $nick
     * @return bool
     */
    public function containsNick(string $nick): bool {
        $query = $this->db->getPdo()->prepare("SELECT user_id FROM users WHERE nick = ?");
        $query->execute(array($nick));
        if ($query->fetch()) {
            return true;
        }
        return false;
    }

    /**
     * Checks if an email is already registered in the database
     * @param string $email
     * @return bool
     */
    public function containsEmail(string $email): bool {
        $query = $this->db->getPdo()->prepare("SELECT user_id FROM users WHERE email = ?");
        $query->execute(array($email));
        if ($query->fetch()) {
            return true;
        }
        return false;
    }

    /* == Inserts, Deletes == */

    /**
     * Hashes a password and inserts it with a new user into the database
     * @param User $user
     * @param string $password
     * @return void
     */
    public function insertUser(User $user, string $password): void {
        $sql = "INSERT INTO users (nick, email, password, name, surname, image_path) VALUES (?, ?, ?, ?, ?, ?)";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(array($user->getNick(), $user->getEmail(), password_hash($password, PASSWORD_DEFAULT), $user->getName(), $user->getSurname(), $user->getImagePath()));
    }

    /**
     * Deletes the user from the database
     * @param int $user_id ID of the user
     * @return void
     */
    public function deleteUser(int $user_id): void {
        $sql = "DELETE FROM users WHERE user_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(array($user_id));
    }

    /* == Updates == */

    /**
     * Updates a user's password
     * @param int $user_id ID of the user
     * @param string $password
     * @return void
     */
    public function updatePassword(int $user_id, string $password): void {
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(array(password_hash($password, PASSWORD_DEFAULT), $user_id));
    }

    /**
     * Updates a user's profile image
     * @param int $user_id ID of the user
     * @param string $image_path Path to the new image
     * @return void
     */
    public function updateProfileImage(int $user_id, string $image_path): void {
        $sql = "UPDATE users SET image_path = ? WHERE user_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(array($image_path, $user_id));
    }

    /**
     * Updates a user's name
     * @param int $user_id ID of the user
     * @param string $name New name
     * @return void
     */
    public function updateUserName(int $user_id, string $name): void {
        $sql = "UPDATE users SET name = ? WHERE user_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(array($name, $user_id));
    }

    /**
     * Updates a user's surname
     * @param int $user_id ID of the user
     * @param string $surname New surname
     * @return void
     */
    public function updateUserSurname(int $user_id, string $surname): void {
        $sql = "UPDATE users SET surname = ? WHERE user_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute(array($surname, $user_id));
    }
}