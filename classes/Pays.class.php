<?php

namespace classes;

class Pays
{
    private $id;
    private $nom;
    private $code_iso;
    private $continent;

    public function __construct($id = null, $nom = null, $code_iso = null, $continent = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->code_iso = $code_iso;
        $this->continent = $continent;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getCodeIso() { return $this->code_iso; }
    public function getContinent() { return $this->continent; }

    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setCodeIso($code_iso) { $this->code_iso = $code_iso; }
    public function setContinent($continent) { $this->continent = $continent; }
}
