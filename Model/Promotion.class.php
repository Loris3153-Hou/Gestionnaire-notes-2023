<?php

class Promotion {
	public $idPromo;
	public $idSemestre;

	public function __construct() {
		$idPromo = "";
		$idSemestre = "";
	}

	public function setIdPromo($idPromo) {
		$this->idPromo = $idPromo;
	}

	public function setIdSemestre($idSemestre) {
		$this->idSemestre = $idSemestre;
	}

}

?>