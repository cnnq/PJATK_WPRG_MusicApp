<?php
require_once __DIR__ . '/../vote/VoteStatus.php';

class Song {
    private int $id;
    private string $title;
    private User $author;
    private ?string $imagePath;
    private ?string $songPath;
    private int $plays;
    private int $upvotes;
    private int $downvotes;
    private VoteStatus $currentUserVote;

    public function __construct($id, $title, $author, $imagePath, $songPath = null, $plays = 0, $upvotes = 0, $downvotes = 0, $currentUserVote = VoteStatus::NONE) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->imagePath = $imagePath;
        $this->songPath = $songPath;
        $this->plays = $plays;
        $this->upvotes = $upvotes;
        $this->downvotes = $downvotes;
        $this->currentUserVote = $currentUserVote;
    }


    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getAuthor(): User {
        return $this->author;
    }

    public function getImagePath(): ?string {
        return $this->imagePath;
    }

    public function getSongPath(): ?string {
        return $this->songPath;
    }

    public function getPlays(): int {
        return $this->plays;
    }

    public function incrementPlays(): void {
        $this->plays++;
    }

    public function getUpvotes(): int {
        return $this->upvotes;
    }

    public function incrementUpvotes(): void {
        $this->upvotes++;
    }

    public function decrementUpvotes(): void {
        $this->upvotes--;
    }

    public function getDownvotes(): int {
        return $this->downvotes;
    }

    public function incrementDownvotes(): void {
        $this->downvotes++;
    }

    public function decrementDownvotes(): void {
        $this->downvotes--;
    }

    public function getCurrentUserVote(): VoteStatus {
        return $this->currentUserVote;
    }
}