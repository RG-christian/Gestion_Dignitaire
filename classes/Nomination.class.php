<?php
namespace classes;

class Nomination
{
    private $id;
    private $dignitaire_id;
    private $date_nomination;
    private $pv_id;
    private $entite_id;
    private $poste_id;

    public function __construct($id = null, $dignitaire_id = null, $date_nomination = null, $pv_id = null, $entite_id = null, $poste_id = null)
    {
        $this->id = $id;
        $this->dignitaire_id = $dignitaire_id;
        $this->date_nomination = $date_nomination;
        $this->pv_id = $pv_id;
        $this->entite_id = $entite_id;
        $this->poste_id = $poste_id;
    }

    // Getters et Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDignitaireId() { return $this->dignitaire_id; }
    public function setDignitaireId($dignitaire_id) { $this->dignitaire_id = $dignitaire_id; }

    public function getDateNomination() { return $this->date_nomination; }
    public function setDateNomination($date_nomination) { $this->date_nomination = $date_nomination; }

    public function getPvId() { return $this->pv_id; }
    public function setPvId($pv_id) { $this->pv_id = $pv_id; }

    public function getEntiteId() { return $this->entite_id; }
    public function setEntiteId($entite_id) { $this->entite_id = $entite_id; }

    public function getPosteId() { return $this->poste_id; }
    public function setPosteId($poste_id) { $this->poste_id = $poste_id; }
}
