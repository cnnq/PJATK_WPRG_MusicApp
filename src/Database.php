<?php
class Database {
    private static ?Database $instance = null;

    private PDO $pdo;

    public function __construct() {
        $config = require __DIR__ . '/../config/config.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']};port={$config['port']}";
        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], $config['options']);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("An error occurred while connecting to the database. Please try again later.");
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }
}
