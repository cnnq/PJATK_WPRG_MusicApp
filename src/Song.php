<?php

class Song {
    private $id;
    private $title;
    private $author;
    private $imagePath;
    private $plays;
    private $upvotes;
    private $downvotes;

    public function __construct($id, $title, $author, $imagePath, $plays = 0, $upvotes = 0, $downvotes = 0) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->imagePath = $imagePath;
        $this->plays = $plays;
        $this->upvotes = $upvotes;
        $this->downvotes = $downvotes;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function getPlays() {
        return $this->plays;
    }

    public function getUpvotes() {
        return $this->upvotes;
    }

    public function getDownvotes() {
        return $this->downvotes;
    }
}