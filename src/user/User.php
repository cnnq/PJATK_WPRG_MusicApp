<?php
class User {
    private ?int $id;
    private string $nick;
    private string $email;
    private ?string $name;
    private ?string $surname;
    private ?string $imagePath;

    function __construct(?int $id, string $nick, string $email, ?string $name, ?string $surname, ?string $imagePath = null) {
        $this->id = $id;
        $this->nick = $nick;
        $this->email = $email;
        $this->name = $name;
        $this->surname = $surname;
        $this->imagePath = $imagePath;
    }


    function getId(): ?int {
        return $this->id;
    }

    function getNick(): string {
        return $this->nick;
    }

    function getEmail(): string {
        return $this->email;
    }

    function getName(): string {
        return $this->name;
    }

    function getSurname(): string {
        return $this->surname;
    }

    function setName($name): void {
        $this->name = $name;
    }

    function setSurname($surname): void {
        $this->surname = $surname;
    }

    function getImagePath(): ?string {
        return $this->imagePath;
    }

    function setImagePath($imagePath): void {
        $this->imagePath = $imagePath;
    }
}
