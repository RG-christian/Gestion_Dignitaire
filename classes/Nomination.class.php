<?php
namespace classes;

class Nomination
{
    private $id;
    private $dignitaire_id;
    private $entite_id;
    private $poste_id;
    private $pv_id;
    private $date_debut;
    private $date_fin;
    private $fonction;

    public function __construct($id = null, $dignitaire_id = null, $entite_id = null, $poste_id = null, $pv_id = null, $date_debut = null, $date_fin = null, $fonction = null)
    {
        $this->id = $id;
        $this->dignitaire_id = $dignitaire_id;
        $this->entite_id = $entite_id;
        $this->poste_id = $poste_id;
        $this->pv_id = $pv_id;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->fonction = $fonction;
    }

    // Getters et Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDignitaireId() { return $this->dignitaire_id; }
    public function setDignitaireId($dignitaire_id) { $this->dignitaire_id = $dignitaire_id; }

    public function getEntiteId() { return $this->entite_id; }
    public function setEntiteId($entite_id) { $this->entite_id = $entite_id; }

    public function getPosteId() { return $this->poste_id; }
    public function setPosteId($poste_id) { $this->poste_id = $poste_id; }

    public function getPvId() { return $this->pv_id; }
    public function setPvId($pv_id) { $this->pv_id = $pv_id; }

    public function getDateDebut() { return $this->date_debut; }
    public function setDateDebut($date_debut) { $this->date_debut = $date_debut; }

    public function getDateFin() { return $this->date_fin; }
    public function setDateFin($date_fin) { $this->date_fin = $date_fin; }

    public function getFonction() { return $this->fonction; }
    public function setFonction($fonction) { $this->fonction = $fonction; }
}
