<?php
class User {
    private $id;
    private $nick;
    private $email;
    private $name;
    private $surname;
    private $imageName;

    function __construct($id, $nick, $email, $name, $surname, $imageName = null) {
        $this->id = $id;
        $this->nick = $nick;
        $this->email = $email;
        $this->name = $name;
        $this->surname = $surname;
        $this->imageName = $imageName;
    }

    function getId() {
        return $this->id;
    }

    function getNick() {
        return $this->nick;
    }

    function getEmail() {
        return $this->email;
    }

    function getName() {
        return $this->name;
    }

    function getSurname() {
        return $this->surname;
    }

    function getImageName() {
        return $this->imageName;
    }

    function setImageName($imageName) {
        $this->imageName = $imageName;
    }
}
