<?php


// classes/Enfant.class.php

namespace classes;


class Enfant
{
    private $id;
    private $nom;
    private $prenom;
    private $date_naiss;
    private $lieu_naiss;
    private $genre;

    public function __construct(
        $id = null, $nom = null, $prenom = null, $date_naiss = null,
        $lieu_naiss = null, $genre = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naiss = $date_naiss;
        $this->lieu_naiss = $lieu_naiss;
        $this->genre = $genre;
    }

    public function __destruct() {}

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }

    public function getPrenom() { return $this->prenom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }

    public function getDateNaiss() { return $this->date_naiss; }
    public function setDateNaiss($date_naiss) { $this->date_naiss = $date_naiss; }

    public function getLieuNaiss() { return $this->lieu_naiss; }
    public function setLieuNaiss($lieu_naiss) { $this->lieu_naiss = $lieu_naiss; }

    public function getGenre() { return $this->genre; }
    public function setGenre($genre) { $this->genre = $genre; }

    public function afficherEnfant() {
        return "{$this->prenom} {$this->nom} ({$this->genre}, né(e) le {$this->date_naiss})";
    }
}
