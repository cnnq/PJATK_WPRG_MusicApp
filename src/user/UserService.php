<?php
require_once 'User.php';
require_once 'UserRepository.php';

class UserService {
    private static ?UserService $instance = null;

    private UserRepository $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    public static function getInstance(): UserService {
        if (self::$instance === null) {
            self::$instance = new UserService(UserRepository::getInstance());
        }
        return self::$instance;
    }

    /**
     * Registers and logs in a new user, throws an exception if failed
     * @param string $nick
     * @param string $email
     * @param string $name
     * @param string $surname
     * @param string $password
     * @param string $confirm_password
     * @return void
     * @throws Exception User input validation failed
     */
    public function registerUser(string $nick, string $email, string $name, string $surname, string $password, string $confirm_password): void {
        // Validate
        $this->validateRegisterUserInput($nick, $email, $password, $confirm_password);

        // Update database
        $user = new User(null, $nick, $email, $name, $surname);
        $this->repository->insertUser($user, $password);

        // Login user
        $result = $this->repository->getUserWithPasswordByNick($nick);
        $user = new User($result['user_id'], $result['nick'], $result['email'], $result['name'], $result['surname']);

        $this->setSessionUser($user);
    }

    /**
     * Logs in a user
     * @param string $nickOrEmail
     * @param string $password
     * @return void
     * @throws Exception Couldn't find user or received incorrect password
     */
    public function loginUser(string $nickOrEmail, string $password): void {
        // Validate
        if (empty($nickOrEmail)) {
            throw new Exception("Nickname or email required");
        }

        if (empty($password)) {
            throw new Exception("Password required");
        }

        // Try to get user from database
        $result = null;

        if (filter_var($nickOrEmail, FILTER_VALIDATE_EMAIL)) {
            $result = $this->repository->getUserWithPasswordByEmail($nickOrEmail);
        }

        if (empty($result)) {
            $result = $this->repository->getUserWithPasswordByNick($nickOrEmail);
        }

        if (empty($result)) {
            throw new Exception("Invalid credentials");
        }

        $user = new User($result['user_id'], $result['nick'], $result['email'], $result['name'], $result['surname'], $result['image_path']);

        // Verify user
        if (password_verify($password, $result['password'])) {
            $this->setSessionUser($user);

        } else {
            throw new Exception("Invalid credentials");
        }
    }

    /**
     * Validates a user using a password and deletes him from the database
     * @param User $user
     * @param string $password
     * @return void
     * @throws Exception Received an invalid password
     */
    public function deleteUser(User $user, string $password): void {
        // Validate
        if (empty($password)) {
            throw new Exception("Password required");
        }

        // Verify user
        if ($this->verifyPassword($user->getId(), $password)) {
            $this->repository->deleteUser($user->getId());
        } else {
            throw new Exception("Invalid password");
        }
    }

    /* == Account management == */

    /**
     * Validates a user with its old password and then updates it to a new one
     * @param User $user
     * @param string $old_password
     * @param string $new_password
     * @param string $new_confirm_password Confirmation password, have to be the same as $new_password
     * @return void
     * @throws Exception
     */
    public function updatePassword(User $user, string $old_password, string $new_password, string $new_confirm_password): void {
        // Validate
        if ($old_password == $new_password) {
            throw new Exception("New password is the same as the old one.");
        }

        if (strlen($new_password) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }

        if ($new_password != $new_confirm_password) {
            throw new Exception("Password must match the confirmation password.");
        }

        if ($this->verifyPassword($user->getId(), $old_password)) {
            $this->repository->updatePassword($user->getId(), $new_password);
        } else {
            throw new Exception("Invalid password");
        }
    }

    /**
     * Updates a user's profile picture
     * @param User $user
     * @param string $image_path
     * @return void
     */
    public function updateProfileImage(User $user, string $image_path): void {
        $this->repository->updateProfileImage($user->getId(), $image_path);
        $user->setImagePath($image_path);
    }

    /**
     * Updates a user's name
     * @param User $user
     * @param string $name
     * @return void
     */
    public function updateName(User $user, string $name): void {
        $this->repository->updateUserName($user->getId(), $name);
        $user->setName($name);
    }

    /**
     * Updates a user's surname
     * @param User $user
     * @param string $surname
     * @return void
     */
    public function updateSurname(User $user, string $surname): void {
        $this->repository->updateUserSurname($user->getId(), $surname);
        $user->setSurname($surname);
    }

    /* == Helpers == */

    /**
     * Sets the user for the current session
     * @param User $user
     * @return void
     */
    private function setSessionUser(User $user): void {
        session_regenerate_id(true);

        $_SESSION['user'] = $user;
    }


    /**
     * Validate user input
     * @throws Exception
     */
    private function validateRegisterUserInput(string $nick, string $email, string $password, string $confirm_password): void {
        // Check required fields
        if (empty($nick)) throw new Exception("Nickname required");
        if (empty($email)) throw new Exception("Email required");
        if (empty($password)) throw new Exception("Password required");
        if (empty($confirm_password)) throw new Exception("Password confirmation required");

        // Check email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address");
        }

        // Check already taken stuff
        if ($this->repository->containsNick($nick)) {
            throw new Exception("This nickname is already taken");
        }

        if ($this->repository->containsEmail($email)) {
            throw new Exception("This email is already taken");
        }

        // Check password
        if (!$this->validatePassword($password, $confirm_password)) {
            throw new Exception("Invalid password. Password must be at least 8 characters long and match the confirmation password.");
        }
    }

    /**
     * Check if the password contains at least 8 characters and matches the confirmation password
     * @param string $password
     * @param string $confirm_password
     * @return bool
     */
    private function validatePassword(string $password, string $confirm_password): bool {
        if (strlen($password) < 8 || $password != $confirm_password) {
            return false;
        }
        return true;
    }

    /**
     * Verifies provided password with the stored hash
     * @param int $user_id
     * @param string $password
     * @return bool
     */
    private function verifyPassword(int $user_id, string $password): bool {
        $result = $this->repository->getHashedPasswordByUserId($user_id);
        return password_verify($password, $result['password']);
    }
}
