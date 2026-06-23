<?php
require_once 'VoteStatus.php';
require_once __DIR__ . '/../Database.php';

class VoteRepository {
    private static ?VoteRepository $instance = null;

    private Database $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public static function getInstance(): VoteRepository {
        if (self::$instance === null) {
            self::$instance = new VoteRepository(Database::getInstance());
        }
        return self::$instance;
    }

    /**
     * Updates a user's vote for the specified song
     * @param int $user_id
     * @param int $song_id
     * @return mixed
     */
    public function getVote(int $user_id, int $song_id): mixed {
        $sql = "SELECT vote_id, is_up FROM votes WHERE user_id = ? AND song_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$user_id, $song_id]);
        return $query->fetch();
    }

    public function addVote(int $user_id, int $song_id, int $is_up): void {
        $sql = "INSERT INTO votes (user_id, song_id, is_up) VALUES (?, ?, ?)";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$user_id, $song_id, $is_up]);
    }

    /**
     * Removes a vote from the database
     * @param int $vote_id
     * @return void
     */
    public function removeVote(int $vote_id): void {
        $sql = "DELETE FROM votes WHERE vote_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$vote_id]);
    }

    public function updateVote(int $vote_id, int $is_up): void {
        $sql = "UPDATE votes SET is_up = ? WHERE vote_id = ?";
        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$is_up, $vote_id]);
    }

    public function updateSongVoteCounts(int $song_id, int $upvotes, int $downvotes): void {
        $sql = "UPDATE songs SET upvotes = ?, downvotes = ? WHERE song_id = ?";

        $query = $this->db->getPdo()->prepare($sql);
        $query->execute([$upvotes, $downvotes, $song_id]);
    }
}
