<?php

namespace classes;

class Enfant
{
    private mixed $id;
    private mixed $nom;
    private mixed $prenom;
    private mixed $date_naissance;
    private mixed $lieu_naissance;
    private mixed $genre;
    private mixed $dignitaire_id; // Ajout du dignitaire_id

    public function __construct(
        $id = null, $nom = null, $prenom = null, $date_naissance = null,
        $lieu_naissanse = null, $genre = null, $dignitaire_id = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->lieu_naissance = $lieu_naissanse;
        $this->genre = $genre;
        $this->dignitaire_id = $dignitaire_id;
    }

    public function __destruct() {}

    public function getId() { return $this->id; }
    public function setId($id): void
    { $this->id = $id; }

    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }

    public function getPrenom() { return $this->prenom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }

    public function getDateNaissance () { return $this->date_naissance; }
    public function setDateNaiss($date_naissance) { $this->date_naissance = $date_naissance; }

    public function getLieuNaiss() { return $this->lieu_naissance; }
    public function setLieuNaiss($lieu_naiss) { $this->lieu_naissance = $lieu_naiss; }

    public function getGenre() { return $this->genre; }
    public function setGenre($genre) { $this->genre = $genre; }

    public function getDignitaireId() { return $this->dignitaire_id; }
    public function setDignitaireId($dignitaire_id): void
    { $this->dignitaire_id = $dignitaire_id; }

    public function afficherEnfant(): string
    {
        return "{$this->prenom} {$this->nom} ({$this->genre}, né(e) le {$this->date_naissance})";
    }
}