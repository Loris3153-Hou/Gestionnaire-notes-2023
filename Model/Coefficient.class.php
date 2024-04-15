<?php

 class Coefficient {
 	
	public $idRessource;
	public $idUE;
	public $coefRessource;

	public function __construct() {
		$idRessource = "";
		$idUE = "";
		$coefRessource = "";
	}

	public function setIdRessource($idRessource) {
		$this->idRessource = $idRessource;
	}
	public function setIdUE($idUE) {
		$this->idUE = $idUE;
	}
	public function setCoefRessource($coefRessource) {
		$this->coefRessource = $coefRessource;
	}

}

?>