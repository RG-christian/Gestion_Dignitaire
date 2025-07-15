<?php
namespace classes;

class Decoration
{
    private $deco_id;
    private $deco_nom;
    private $deco_type;
    private $deco_niveau;
    private $deco_grade;
    private $deco_date_obtention;
    private $deco_autorite;
    private $deco_motif;
    private $deco_description;
    private $deco_fichierAttestation;

    public function __construct(
        $deco_id = null, $deco_nom = null, $deco_type = null, $deco_niveau = null,
        $deco_grade = null, $deco_date_obtention = null, $deco_autorite = null,
        $deco_motif = null, $deco_description = null, $deco_fichierAttestation = null
    ) {
        $this->deco_id = $deco_id;
        $this->deco_nom = $deco_nom;
        $this->deco_type = $deco_type;
        $this->deco_niveau = $deco_niveau;
        $this->deco_grade = $deco_grade;
        $this->deco_date_obtention = $deco_date_obtention;
        $this->deco_autorite = $deco_autorite;
        $this->deco_motif = $deco_motif;
        $this->deco_description = $deco_description;
        $this->deco_fichierAttestation = $deco_fichierAttestation;
    }

    // Getters et Setters
    public function getId() { return $this->deco_id; }
    public function setId($id) { $this->deco_id = $id; }

    public function getNom() { return $this->deco_nom; }
    public function setNom($n) { $this->deco_nom = $n; }

    public function getType() { return $this->deco_type; }
    public function setType($t) { $this->deco_type = $t; }

    public function getNiveau() { return $this->deco_niveau; }
    public function setNiveau($n) { $this->deco_niveau = $n; }

    public function getGrade() { return $this->deco_grade; }
    public function setGrade($g) { $this->deco_grade = $g; }

    public function getDateObtention() { return $this->deco_date_obtention; }
    public function setDateObtention($d) { $this->deco_date_obtention = $d; }

    public function getAutorite() { return $this->deco_autorite; }
    public function setAutorite($a) { $this->deco_autorite = $a; }

    public function getMotif() { return $this->deco_motif; }
    public function setMotif($m) { $this->deco_motif = $m; }

    public function getDescription() { return $this->deco_description; }
    public function setDescription($d) { $this->deco_description = $d; }

    public function getFichierAttestation() { return $this->deco_fichierAttestation; }
    public function setFichierAttestation($f) { $this->deco_fichierAttestation = $f; }
}
