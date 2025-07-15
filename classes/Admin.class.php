<?php
class Admin {
    private $id;
    private $username;
    private $password;    // hashé
    private $nom_complet;

    public function __construct($id, $username, $password, $nom_complet) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->nom_complet = $nom_complet;
    }

    // Getters
    public function getId()          { return $this->id; }
    public function getUsername()    { return $this->username; }
    public function getPassword()    { return $this->password; }
    public function getNomComplet()  { return $this->nom_complet; }

    // Setters
    public function setId($id)                   { $this->id = $id; }
    public function setUsername($username)       { $this->username = $username; }
    public function setPassword($password)       { $this->password = $password; }
    public function setNomComplet($nom_complet)  { $this->nom_complet = $nom_complet; }
}
