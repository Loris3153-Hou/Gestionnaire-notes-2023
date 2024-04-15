<?php 

class User {

	public $mailUser;
	public $nomUser;
	public $pnomUser;
	public $role;
	public $passUser;

	public function __construct() {
		$this->mailUser = "";
		$this->nomUser = "";
		$this->pnomUser = "";
		$this->role = "";
		$this->passUser = "";
	}

	public function getMailUser() {
		return $mailUser;
	}
	
	public function getNomUser() {
		return $nomUser;
	}

	public function getPnomUser() {
		return $pnomUser;
	}

	public function getIdRole() {
		return $role;
	}

	public function getPassUser() {
		return $passUser;
	}

	public function setMailUser($mailUser) {
		$this->mailUser = $mailUser;
	}

	public function setNomUser($nomUser) {
		$this->nomUser = $nomUser;
	}

	public function setPnomUser($pnomUser) {
		$this->pnomUser = $pnomUser;
	}

	public function setRoleUser($role) {
		$this->role = $role;
	}

	public function setPassUser($passUser) {
		$this->passUser = $passUser;
	}

}

?>