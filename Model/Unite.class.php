<?php

class Unite {
	public $idUE;
	public $nomUE;

	public function __construct() {
		$idUE = "";
		$nomUE = "";
	}

	public function setIdUE($idUE) {
		$this->idUE = $idUE;
	}

	public function setNomUE($nomUE) {
		$this->nomUE = $nomUE;
	}

}

?>