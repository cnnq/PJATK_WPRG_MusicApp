<?php
require_once 'VoteRepository.php';
require_once '../user/User.php';
require_once '../song/Song.php';

class VoteService {

    private static ?VoteService $instance = null;

    private VoteRepository $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    public static function getInstance(): VoteService {
        if (self::$instance === null) {
            self::$instance = new VoteService(VoteRepository::getInstance());
        }
        return self::$instance;
    }


    /**
     * Updates a user's vote for the specified song
     * @param User $user
     * @param Song $song
     * @param int $isUp Clicked button: 1 for upvote, 0 for downvote
     * @return void
     */
    public function updateVote(User $user, Song $song, int $isUp): void {
        $vote = $this->repository->getVote($user->getId(), $song->getId());

        if ($vote) {
            if ($vote['is_up'] == $isUp) {
                // Remove vote if clicking the same button
                $this->repository->removeVote($vote['vote_id']);

                if ($isUp) {
                    $song->decrementUpvotes();
                } else {
                    $song->decrementDownvotes();
                }

            } else {
                // Change vote type
                $this->repository->updateVote($vote['vote_id'], $isUp);

                if ($isUp) {
                    $song->incrementUpvotes();
                    $song->decrementDownvotes();
                } else {
                    $song->decrementUpvotes();
                    $song->incrementDownvotes();
                }
            }

        } else {
            // New vote
            $this->repository->addVote($user->getId(), $song->getId(), $isUp);

            if ($isUp) {
                $song->incrementUpvotes();
            } else {
                $song->incrementDownvotes();
            }
        }

        $this->repository->updateSongVoteCounts($song->getId(), $song->getUpvotes(), $song->getDownvotes());
    }
}
