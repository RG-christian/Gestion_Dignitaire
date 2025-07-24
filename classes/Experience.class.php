<?php
namespace classes;

class Experience
{
    private $id;
    private $dignitaire_id;
    private $intitule;
    private $date_debut;
    private $date_fin;
    private $structure_id;
    private $structure_nom; // Pour affichage

    public function __construct(
        $id = null, $dignitaire_id = null, $intitule = null,
        $date_debut = null, $date_fin = null, $structure_id = null
    ) {
        $this->id = $id;
        $this->dignitaire_id = $dignitaire_id;
        $this->intitule = $intitule;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->structure_id = $structure_id;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDignitaireId() { return $this->dignitaire_id; }
    public function setDignitaireId($dignitaire_id) { $this->dignitaire_id = $dignitaire_id; }

    public function getIntitule() { return $this->intitule; }
    public function setIntitule($intitule) { $this->intitule = $intitule; }

    public function getDateDebut() { return $this->date_debut; }
    public function setDateDebut($date_debut) { $this->date_debut = $date_debut; }

    public function getDateFin() { return $this->date_fin; }
    public function setDateFin($date_fin) { $this->date_fin = $date_fin; }

    public function getStructureId() { return $this->structure_id; }
    public function setStructureId($structure_id) { $this->structure_id = $structure_id; }

    public function getStructureNom() { return $this->structure_nom; }
    public function setStructureNom($structure_nom) { $this->structure_nom = $structure_nom; }

    public function afficherExperience() {
        return "{$this->intitule} ({$this->date_debut} - {$this->date_fin})";
    }
}
