<?php

namespace classes;

class Langue {
    private $id;
    private $nom;

    public function __construct($id = null, $nom = null) {
        $this->id = $id;
        $this->nom = $nom;
    }
    public function __destruct() {}

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }

    public function afficherLangue() {
        return "Langue : {$this->nom}";
    }
}
