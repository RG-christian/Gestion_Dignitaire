<?php

namespace classes;

class Pays
{
    private $id;
    private $nom;
    private $code_iso;
    private $indicatif;
    private $continent;
    private $region_id;

    public function __construct($id = null, $nom = null, $code_iso = null, $indicatif = null, $continent = null, $region_id = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->code_iso = $code_iso;
        $this->indicatif = $indicatif;
        $this->continent = $continent;
        $this->region_id = $region_id; // This is the related region ID on table pays

    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getCodeIso() { return $this->code_iso; }
    public function getIndicatif(){ return $this->indicatif; }
    public function getContinent() { return $this->continent; }
    public function getRegionId() { return $this->region_id; }


    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setCodeIso($code_iso) { $this->code_iso = $code_iso; }
    public function setIndicatif($indicatif){ $this->indicatif = $indicatif; }
    public function setContinent($continent) { $this->continent = $continent; }
    public function setRegionId($region_id) { $this->region_id = $region_id; }

}
