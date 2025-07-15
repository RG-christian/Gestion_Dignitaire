<?php


// classes/LangueParlee.class.php

namespace classes;


class LangueParlee
{
    private $id;
    private $dignitaire_id;
    private $langue_id;
    private $niveau;

    public function __construct(
        $id = null, $dignitaire_id = null, $langue_id = null, $niveau = null
    ) {
        $this->id = $id;
        $this->dignitaire_id = $dignitaire_id;
        $this->langue_id = $langue_id;
        $this->niveau = $niveau;
    }

    public function __destruct() {}

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDignitaireId() { return $this->dignitaire_id; }
    public function setDignitaireId($dignitaire_id) { $this->dignitaire_id = $dignitaire_id; }

    public function getLangueId() { return $this->langue_id; }
    public function setLangueId($langue_id) { $this->langue_id = $langue_id; }

    public function getNiveau() { return $this->niveau; }
    public function setNiveau($niveau) { $this->niveau = $niveau; }

    public function afficherLangueParlee() {
        return "Langue ID {$this->langue_id} - Niveau : {$this->niveau}";
    }
}
