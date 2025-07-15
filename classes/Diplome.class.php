<?php
// classes/Diplome.class.php
namespace classes;

class Diplome
{
    // Attributs privés correspondant aux champs SQL
    private $id;
    private $dignitaire_id;
    private $intitule;
    private $etablissement;
    private $annee;
    private $ville_id;
    private $domaine_id;
    private $code;
    private $type;

    // Constructeur
    public function __construct(
        $id = null, $dignitaire_id = null, $intitule = null, $etablissement = null,
        $annee = null, $ville_id = null, $domaine_id = null, $code = null, $type = null
    ) {
        $this->id = $id;
        $this->dignitaire_id = $dignitaire_id;
        $this->intitule = $intitule;
        $this->etablissement = $etablissement;
        $this->annee = $annee;
        $this->ville_id = $ville_id;
        $this->domaine_id = $domaine_id;
        $this->code = $code;
        $this->type = $type;
    }

    public function __destruct() {}

    // Getters & Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDignitaireId() { return $this->dignitaire_id; }
    public function setDignitaireId($dignitaire_id) { $this->dignitaire_id = $dignitaire_id; }

    public function getIntitule() { return $this->intitule; }
    public function setIntitule($intitule) { $this->intitule = $intitule; }

    public function getEtablissement() { return $this->etablissement; }
    public function setEtablissement($etablissement) { $this->etablissement = $etablissement; }

    public function getAnnee() { return $this->annee; }
    public function setAnnee($annee) { $this->annee = $annee; }

    public function getVilleId() { return $this->ville_id; }
    public function setVilleId($ville_id) { $this->ville_id = $ville_id; }

    public function getDomaineId() { return $this->domaine_id; }
    public function setDomaineId($domaine_id) { $this->domaine_id = $domaine_id; }

    public function getCode() { return $this->code; }
    public function setCode($code) { $this->code = $code; }

    public function getType() { return $this->type; }
    public function setType($type) { $this->type = $type; }

    // Méthode métier exemple
    public function afficherDiplome(): string
    {
        return "{$this->intitule} - {$this->type} ({$this->annee})";
    }
}
