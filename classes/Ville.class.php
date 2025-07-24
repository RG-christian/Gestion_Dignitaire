<?php
namespace classes;

class Ville
{
    private $id;
    private $nom;
    private $pays_id;

    public function __construct($id = null, $nom = null, $pays_id = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->pays_id = $pays_id;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPaysId() { return $this->pays_id; }
    public function setPaysId($pays_id) { $this->pays_id = $pays_id; }
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }

}
