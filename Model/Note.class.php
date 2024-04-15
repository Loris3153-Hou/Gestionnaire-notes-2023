<?php

class Note {

	public $mailUser;
	public $idRessource;
	public $idUE;
	public $note;

	public function __construct() {
		$mailUser = "";
		$idRessource = "";
		$idUE = "";
		$note = "";
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

	public function setNote($note) {
		$this->note = $note;
	}

}

?>