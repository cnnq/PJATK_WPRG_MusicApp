<?php
require_once 'User.php';
require_once 'Database.php';

class UserService {
    private static $instance;

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new UserService(Database::getInstance());
        }
        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function registerUser($nick, $email, $name, $surname, $password, $confirm_password) {
        // Validate
        $this->validateRegisterUserInput($nick, $email, $password, $confirm_password);

        // Update database
        $user = new User(null, $nick, $email, $name, $surname);
        $this->db->insertUser($user, $password);

        // Login user
        $result = $this->db->getUserWithPasswordByNick($nick);
        $user = new User($result['user_id'], $result['nick'], $result['email'], $result['name'], $result['surname']);

        $this->setSessionUser($user);
    }

    public function loginUser($nickOrEmail, $password) {
        // Validate
        if (empty($nickOrEmail)) {
            throw new \Exception("Nickname or email required");
        }

        if (empty($password)) {
            throw new \Exception("Password required");
        }

        // Try to get user from database
        $result = null;

        if (filter_var($nickOrEmail, FILTER_VALIDATE_EMAIL)) {
            $result = $this->db->getUserWithPasswordByEmail($nickOrEmail);
        }

        if (empty($result)) {
            $result = $this->db->getUserWithPasswordByNick($nickOrEmail);
        }

        if (empty($result)) {
            throw new \Exception("Invalid credentials");
        }

        $user = new User($result['user_id'], $result['nick'], $result['email'], $result['name'], $result['surname'], $result['image_path']);

        // Verify user
        if (password_verify($password, $result['password'])) {
            $this->setSessionUser($user);

        } else {
            throw new \Exception("Invalid credentials");
        }
    }

    public function deleteUser($user, $password) {
        // Validate
        if (empty($password)) {
            throw new \Exception("Password required");
        }

        // Verify user
        if ($this->verifyPassword($user->getId(), $password)) {
            $this->db->deleteUser($user->getId());
        } else {
            throw new \Exception("Invalid password");
        }
    }

    /* == Account management == */

    public function changePassword($user, $old_password,  $new_password, $new_confirm_password) {
        // Validate
        if ($old_password == $new_password) {
            throw new \Exception("New password is the same as the old one.");
        }

        if (strlen($new_password) < 8) {
            throw new \Exception("Password must be at least 8 characters long.");
        }

        if ($new_password != $new_confirm_password) {
            throw new \Exception("Password must match the confirmation password.");
        }

        if ($this->verifyPassword($user->getId(), $old_password)) {
            $this->db->updatePassword($user->getId(), $new_password);
        } else {
            throw new \Exception("Invalid password");
        }
    }

    public function updateProfileImage($user, $image_path) {
        $this->db->updateProfileImage($user->getId(), $image_path);
    }

    /* == Helpers == */

    private function setSessionUser($user) {
        session_regenerate_id(true);

        $_SESSION['user'] = $user;
    }


    /**
     * Validate user input
     * @throws Exception
     */
    private function validateRegisterUserInput($nick, $email, $password, $confirm_password) {
        // Check required fields
        if (empty($nick)) throw new \Exception("Nickname required");
        if (empty($email)) throw new \Exception("Email required");
        if (empty($password)) throw new \Exception("Password required");
        if (empty($confirm_password)) throw new \Exception("Password confirmation required");

        // Check email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email address");
        }

        // Check already taken stuff
        if ($this->db->containsNick($nick)) {
            throw new \Exception("This nickname is already taken");
        }

        if ($this->db->containsEmail($email)) {
            throw new \Exception("This email is already taken");
        }

        // Check password
        if (!$this->validatePassword($password, $confirm_password)) {
            throw new \Exception("Invalid password. Password must be at least 8 characters long and match the confirmation password.");
        }
    }

    /**
     * Check if the password contains at least 8 characters and matches the confirmation password
     */
    private function validatePassword($password, $confirm_password) {
        if (strlen($password) < 8 || $password != $confirm_password) {
            return false;
        }
        return true;
    }

    /**
     * @param $user_id
     * @param $password
     * @return bool
     */
    private function verifyPassword($user_id, $password) {
        $result = $this->db->getPasswordByUserId($user_id);
        return password_verify($password, $result['password']);
    }
}
