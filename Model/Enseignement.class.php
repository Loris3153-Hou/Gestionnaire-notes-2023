<?php

class Enseignement {

	public $mailUser;
	public $idRessource;
	public $idUE;

	public function __construct() {
		$mailUser = "";
		$idRessource = "";
		$idUE = "";
	}

	public function setMailUser($mailUser) {
		$this->mailUser = $mailUser;
	}

	public function setIdRessource($idRessource) {
		$this->idRessource = $idRessource;
	}

	public function setIdUE($idUE) {
		$this->idUE = $idUE;
	}

}

?>