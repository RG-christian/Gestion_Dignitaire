<?php
// classes/Dignitaire.class.php

class Dignitaire
{
    // Attributs privés correspondant aux champs SQL
    private $id;
    private $nip;
    private $matricule;
    private $nom;
    private $prenom;
    private $date_naissance;
    private $lieu_naissance; // (id de la ville)
    private $nationalite;
    private $genre;
    private $etat_civil;
    private $tel;
    private $adresse;
    private $photo;
    private $casierJud;       // AJOUTÉ
    private $certificatsMed;  // AJOUTÉ

    // --- Attributs publics pour les données liées ---
    /** @var array Liste des diplômes du dignitaire */
    public array $diplomes = [];
    /** @var array Liste des enfants du dignitaire */
    public array $enfants = [];
    /** @var array Liste des postes du dignitaire */
    public array $postes = [];
    /** @var array Liste des langues parlées du dignitaire */
    public array $langues = [];
    /** @var array Liste des expériences du dignitaire */
    public array $experiences = [];

    // Constructeur (tous les paramètres sont optionnels)
    public function __construct(
        $id, $nom, $prenom, $date_naissance, $lieu_naissance, $nationalite,
        $genre, $etat_civil, $tel, $adresse, $nip, $matricule,
        $photo, $casier_judiciaire, $certificats_medicaux    )
    {
        $this->id = $id;
        $this->nip = $nip;
        $this->matricule = $matricule;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->lieu_naissance = $lieu_naissance;
        $this->genre = $genre;
        $this->nationalite = $nationalite;
        $this->tel = $tel;
        $this->adresse = $adresse;
        $this->etat_civil = $etat_civil;
        $this->photo = $photo;
        $this->casierJud = $casier_judiciaire;           // AJOUTÉ
        $this->certificatsMed = $certificats_medicaux; // AJOUTÉ
        // Les tableaux liés sont déjà initialisés plus haut
    }

    // --- Getters & Setters ---
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNip() { return $this->nip; }
    public function setNip($nip) { $this->nip = $nip; }

    public function getMatricule() { return $this->matricule; }
    public function setMatricule($matricule) { $this->matricule = $matricule; }

    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }

    public function getPrenom() { return $this->prenom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }

    public function getDateNaissance() { return $this->date_naissance; }
    public function setDateNaissance($date_naissance) { $this->date_naissance = $date_naissance; }

    public function getLieuNaissance() { return $this->lieu_naissance; }
    public function setLieuNaissance($lieu_naissance) { $this->lieu_naissance = $lieu_naissance; }

    public function getGenre() { return $this->genre; }
    public function setGenre($genre) { $this->genre = $genre; }

    public function getEtatCivil() { return $this->etat_civil; }
    public function setEtatCivil($etat_civil) { $this->etat_civil = $etat_civil; }

    public function getPhoto() { return $this->photo; }
    public function setPhoto($photo) { $this->photo = $photo; }

    public function getTel()
    {
        return $this->tel;
    }


    public function setTel($tel): void
    {
        $this->tel = $tel;
    }


    public function getNationalite()
    {
        return $this->nationalite;
    }


    public function setNationalite($nationalite): void
    {
        $this->nationalite = $nationalite;
    }


    public function getAdresse()
    {
        return $this->adresse;
    }


    public function setAdresse($adresse): void { $this->adresse = $adresse; }

    public function getCasierJud() { return $this->casierJud; }
    public function setCasierJud($casierJud) { $this->casierJud = $casierJud; }

    public function getCertificatsMed() { return $this->certificatsMed; }
    public function setCertificatsMed($certificatsMed) { $this->certificatsMed = $certificatsMed; }

    // --- Méthodes métier (exemples) ---
    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function afficherIdentite() {
        return "Dignitaire : " . $this->getNomComplet() .
            " | Matricule : " . $this->matricule .
            " | NIP : " . $this->nip .
            " | Genre : " . $this->genre .
            " | Né(e) le : " . $this->date_naissance;
    }
    // + d'autres méthodes métiers à ajouter si besoin



}
