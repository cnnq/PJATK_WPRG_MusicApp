<?php
require_once 'Song.php';
require_once 'SongRepository.php';
require_once '../user/User.php';
require_once __DIR__ . '/../vote/VoteRepository.php';

class SongService {
    private static ?SongService $instance = null;

    private SongRepository $songRepository;
    private VoteRepository $voteRepository;

    public function __construct(SongRepository $songRepository, VoteRepository $voteRepository) {
        $this->songRepository = $songRepository;
        $this->voteRepository = $voteRepository;
    }

    public static function getInstance(): SongService {
        if (self::$instance === null) {
            self::$instance = new SongService(SongRepository::getInstance(), VoteRepository::getInstance());
        }
        return self::$instance;
    }

    /**
     * Increments the play count for a specified song
     * @param Song $song
     * @return void
     */
    public function incrementPlays(Song $song): void {
        $this->songRepository->incrementPlays($song->getId());
        $song->incrementPlays();
    }

    public function getSong(int $song_id): Song {
        $result = $this->songRepository->getSong($song_id);

        $author = new User($result['user_id'], $result['nick'], $result['email'], $result['name'], $result['surname'], $result['profile_image']);

        $currentUserVote = VoteStatus::NONE;
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $resultVote = $this->voteRepository->getVote($user->getId(), $song_id);
            if ($resultVote) {
                $currentUserVote = $resultVote['is_up'] ? VoteStatus::UP : VoteStatus::DOWN;
            }
        }

        return new Song($result['song_id'], $result['title'], $author, $result['song_image'], $result['song_path'], $result['plays'], $result['upvotes'], $result['downvotes'], $currentUserVote);
    }

    /**
     * Inserts a new song into the database
     */
    public function insertSong(string $title, int $author_id, ?string $image_path, ?string $song_path): void {
        $this->songRepository->insertSong($title, $author_id, $image_path, $song_path);
    }

    /**
     * Deletes all songs uploaded by a specified user
     * @param User $user The author of the songs
     * @return void
     */
    public function deleteSongsByAuthor(User $user): void {
        $this->songRepository->deleteSongsByAuthor($user->getId());
    }
}