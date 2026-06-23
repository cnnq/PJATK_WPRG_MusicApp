<?php
require_once 'Song.php';
require_once 'SongRepository.php';
require_once __DIR__ . '/../user/User.php';
require_once __DIR__ . '/../vote/VoteStatus.php';
require_once __DIR__ . '/../vote/VoteRepository.php';

class SearchService {
    private static ?SearchService $instance = null;

    private SongRepository $repository;
    private VoteRepository $voteRepository;

    public function __construct(SongRepository $repository, VoteRepository $voteRepository) {
        $this->repository = $repository;
        $this->voteRepository = $voteRepository;
    }

    public static function getInstance(): SearchService {
        if (self::$instance === null) {
            self::$instance = new SearchService(SongRepository::getInstance(), VoteRepository::getInstance());
        }
        return self::$instance;
    }


    public function searchSongsByAnything(string $anything, string $sortBy = 'nothing'): array {
        if (empty($anything)) {
            return [];
        }

        $results = $this->repository->getSongsByAnything($anything, $sortBy);
        return $this->mapResultsToSongs($results);
    }

    public function searchSongsByTitle(string $title, string $sortBy = 'nothing'): array {
        if (empty($title)) {
            return [];
        }

        $results = $this->repository->getSongsByTitle($title, $sortBy);
        return $this->mapResultsToSongs($results);
    }

    public function searchSongsByAuthor(string $authorName, string $sortBy = 'nothing'): array {
        if (empty($authorName)) {
            return [];
        }

        $results = $this->repository->getSongsByAuthor($authorName, $sortBy);
        return $this->mapResultsToSongs($results);
    }

    public function searchSongsByAuthorId(User $author, string $sortBy = 'nothing'): array {
        $results = $this->repository->getSongsByAuthorId($author->getId(), $sortBy);
        return $this->mapResultsWithoutAuthorToSongs($results, $author);
    }
    
    public function searchSongsRandomly(int $limit): array {
        if ($limit <= 0) {
            return [];
        }

        $results = $this->repository->getSongsRandomly($limit);
        return $this->mapResultsToSongs($results);
    }

    /* == Map functions == */

    private function mapResultsToSongs(array $results): array {
        $songs = [];
        foreach ($results as $result) {
            $author = new User($result['user_id'], $result['nick'], $result['email'], $result['name'], $result['surname'], $result['profile_image']);
            $currentUserVote = $this->getCurrenUserVote($result['song_id']);
            $songs[] = new Song($result['song_id'], $result['title'], $author, $result['song_image'], $result['song_path'], $result['plays'], $result['upvotes'], $result['downvotes'], $currentUserVote);
        }
        return $songs;
    }

    private function mapResultsWithoutAuthorToSongs(array $results, User $author): array {
        $songs = [];
        foreach ($results as $result) {
            $currentUserVote = $this->getCurrenUserVote($result['song_id']);
            $songs[] = new Song($result['song_id'], $result['title'], $author, $result['song_image'], $result['song_path'], $result['plays'], $result['upvotes'], $result['downvotes'], $currentUserVote);
        }
        return $songs;
    }

    private function getCurrenUserVote($song_id): VoteStatus {
        $currentUserVote = VoteStatus::NONE;
        if (isset($_SESSION['user']) ) {
            $user = $_SESSION['user'];
            $result = $this->voteRepository->getVote($user->getId(), $song_id);

            if ($result) {
                $currentUserVote = $result['is_up'] ? VoteStatus::UP : VoteStatus::DOWN;
            }
        }
        return $currentUserVote;
    }
}
