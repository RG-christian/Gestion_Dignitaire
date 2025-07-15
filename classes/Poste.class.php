<?php

// classes/Poste.class.php

namespace classes;


class Poste
{
    private $id;
    private $dignitaire_id;
    private $titre;
    private $ville_id;
    private $date_debut;
    private $date_fin;
    private $entite_id;

    public function __construct(
        $id = null, $dignitaire_id = null, $titre = null, $ville_id = null,
        $date_debut = null, $date_fin = null, $entite_id = null
    ) {
        $this->id = $id;
        $this->dignitaire_id = $dignitaire_id;
        $this->titre = $titre;
        $this->ville_id = $ville_id;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->entite_id = $entite_id;
    }

    public function __destruct() {}

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDignitaireId() { return $this->dignitaire_id; }
    public function setDignitaireId($dignitaire_id) { $this->dignitaire_id = $dignitaire_id; }

    public function getTitre() { return $this->titre; }
    public function setTitre($titre) { $this->titre = $titre; }

    public function getVilleId() { return $this->ville_id; }
    public function setVilleId($ville_id) { $this->ville_id = $ville_id; }

    public function getDateDebut() { return $this->date_debut; }
    public function setDateDebut($date_debut) { $this->date_debut = $date_debut; }

    public function getDateFin() { return $this->date_fin; }
    public function setDateFin($date_fin) { $this->date_fin = $date_fin; }

    public function getEntiteId() { return $this->entite_id; }
    public function setEntiteId($entite_id) { $this->entite_id = $entite_id; }

    // Méthode métier exemple
    public function afficherPoste() {
        return "{$this->titre} ({$this->date_debut} - {$this->date_fin})";
    }
}
