<?php

class Groupe
{

    public $idEtu;
    public $idPromo;

    public function __construct($idEtu, $idPromo){
        $this->idEtu = $idEtu;
        $this->idPromo = $idPromo;
    }

    //getters
    public function getIdEtu(){
        return $this->idEtu;
    }

    public function getIdPromo(){
        return $this->idPromo;
    }

}