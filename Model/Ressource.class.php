<?php

class Ressource {
	public $idRessource;
	public $nomRessource;
	public $idSemestre;

	public function __construct() {
		$idRessource = "";
		$nomRessource = "";
		$idSemestre = "";
	}

	public function setIdRessource($idRessource) {
		$this->idRessource = $idRessource;
	}

	public function setNomRessource($nomRessource) {
		$this->nomRessource = $nomRessource;
	}

	public function setIdSemestre($idSemestre) {
		$this->idSemestre = $idSemestre;
	}

}

?>