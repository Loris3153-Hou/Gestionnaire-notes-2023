<?php

class Role
{

    public $idRole;
    public $nomRole;

    public function __construct($idRole, $nomRole){
        $this->idRole = $idRole;
        $this->nomRole = $nomRole;
    }

    public function getNomRole()
    {
        return $this->nomRole;
    }

    public function getIdRole(){
        return $this->idRole;
    }

}