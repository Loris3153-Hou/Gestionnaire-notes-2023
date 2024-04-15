<?php

include_once('../Model/Enseignement.class.php');
include_once('DAO.class.php');

class EnseignementDAO {
	
	/**
	* @param tmp Ligne reçue depuis la base de données
	* @return    Un objet enseignement créé à partir de Enseignement.class.php
	*/
	public function createEnseignement($tmp) {
		$enseignement  = new Enseignement();

		$enseignement->setMailUser($tmp['mailUser']);
		$enseignement->setIdRessource($tmp['idRessource']);
		$enseignement->setIdUE($tmp['idUE']);

		return $enseignement;
	}

	/**
	* @param sql       Requête SQL
	* @param arguments Tableau d'attributs qui remplaceront les ? dans la requête SQL
	* @return          Une liste d'enseignements issus de la base de données
	*/
	function readQuery($sql, $arguments) {

		$dao = new DAO();

		$bdd = new PDO("mysql:host=$dao->address;dbname=$dao->db_name",$dao->user,$dao->pass);

		$rs = $bdd->prepare($sql);
		$rs->execute($arguments);

		$list = array();
		while ($tmp = $rs->fetch()) {
			$enseignement = $this->createEnseignement($tmp);
			array_push($list, $enseignement);
		}
		return $list;
	}

	/**
	* @param sql       Requête SQL
	* @param arguments Tableau d'attributs qui remplaceront les ? dans la requête SQL
	* @return          Une liste d'enseignements issus de la base de données
	*/
	function editQuery($sql, $arguments) {

		$dao = new DAO();

		$bdd = new PDO("mysql:host=$dao->address;dbname=$dao->db_name",$dao->user,$dao->pass);

		$rs = $bdd->prepare($sql);
		$rs->execute($arguments);
	}

	/**
	* @param mailUser    Identifiant d'un enseignant
	* @param idUE        Identifiant d'une unité
	* @param idRessource Identifiant d'une ressource
	* @return            True s'il existe un enseignant dont l'adresse mail est passée en paramètre associé à l'unité et à la ressource passées en paramètre, False sinon
	*/
	function enseignementExists($mailUser, $idUE, $idRessource) {
		$sql = "SELECT * FROM ENSEIGNEMENT WHERE mailUser = ? AND idUE = ? AND idRessource= ?";
		$arguments = array();
		array_push($arguments, $mailUser, $idUE, $idRessource);
		return sizeof($this->readQuery($sql, $arguments))>0;
	}

	/**
	* @param mailUser    Identifiant d'un enseignant
	* @param idUE        Identifiant d'une unité
	* @param idRessource Identifiant d'une ressource
	*/
	function updateEnseignement($mailUser, $idUE, $idRessource) {
		if ($this->enseignementExists($mailUser, $idUE, $idRessource)) {
			$sql = "DELETE FROM ENSEIGNEMENT WHERE mailUser = ? AND idRessource = ? AND idUE = ?;";
		} else {
			$sql = "INSERT INTO ENSEIGNEMENT VALUES(?, ?, ?);";
		}
		$arguments = array();
		array_push($arguments, $mailUser, $idRessource, $idUE);
		$this->editQuery($sql, $arguments);
	}

}

?>