<?php

include_once('../Model/Ressource.class.php');
include_once('DAO.class.php');

class RessourceDAO {
	
	/**
	* @param tmp Ligne reçue depuis la base de données
	* @return    Un objet note créé à partir de Ressource.class.php
	*/
	public function createRessource($tmp) {
		$ressource = new Ressource();

		$ressource->setIdRessource($tmp['idRessource']);
		$ressource->setNomRessource($tmp['nomRessource']);
		$ressource->setIdSemestre($tmp['idSemestre']);

		return $ressource;
	}

	/**
	* @param sql       Requête SQL
	* @param arguments Tableau d'attributs qui remplaceront les ? dans la requête SQL
	* @return          Une liste de notes issues de la base de données
	*/
	function readQuery($sql, $arguments) {
		$dao = new DAO();

		$bdd = new PDO("mysql:host=$dao->address;dbname=$dao->db_name",$dao->user,$dao->pass);

		$rs = $bdd->prepare($sql);
		$rs->execute($arguments);

		$list = array();
		while ($tmp = $rs->fetch()) {
			$ressource = $this->createRessource($tmp);
			array_push($list, $ressource);
		}
		return $list;
	}

	/**
	* @param idSemestre Identifiant d'un semestre
	* @return           Une liste de Ressources appartenant au semestre passé en paramètre
	*/
	function getRessourcesFromSemestre($idSemestre) {
		$semestreNum = substr($idSemestre, 1, 1);
		$sql = "SELECT * FROM RESSOURCE WHERE idRessource LIKE '%".$semestreNum.".%';";
		return $this->readQuery($sql, array());
	}

	/**
	* @param idSemestre Identifiant d'un semestre
	* @param mailUser   Adresse mail d'un enseignant
	* @return           Une liste de ressources enseignées par l'enseignant passé en paramètre au semestre passé en paramètre
	*/
	function getRessourcesFromSemestreAndTeacher($idSemestre, $mailUser) {
		$sql = "SELECT * FROM RESSOURCE INNER JOIN ENSEIGNEMENT ON RESSOURCE.idRessource = ENSEIGNEMENT.idRessource WHERE RESSOURCE.idRessource LIKE '%".$semestreNum.".%' AND mailUser = ? ORDER BY ENSEIGNEMENT.idUE;";
		$arguments = array();
		array_push($arguments, $mailUser);
		return $this->readQuery($sql, $arguments);
	}

	/**
	* @param mailUser Adresse mail d'un enseignant
	* @return         Une liste de ressources enseignées par l'enseignant passé en paramètre
	*/
	function getRessourceWithTeacher($mailUser) {
		$sql = "SELECT DISTINCT RESSOURCE.idRessource, nomRessource, idSemestre FROM RESSOURCE INNER JOIN ENSEIGNEMENT ON RESSOURCE.idRessource = ENSEIGNEMENT.idRessource WHERE mailUser = ?;";
		$arguments = array();
		array_push($arguments, $mailUser);
		return $this->readQuery($sql, $arguments);
	}

	/**
	* @param idUE     Identidiant d'une unité d'enseignement
	* @param mailUser Adresse mail d'un enseignant
	* @return         Une liste d'unités d'enseignement qui concerne l'enseignant passé en paramètre
	*/
	function getRessourcesFromUEAndTeacher($idUE, $mailUser) {
		$sql = "SELECT DISTINCT RESSOURCE.idRessource, nomRessource, idSemestre FROM RESSOURCE INNER JOIN COEFFICIENT ON RESSOURCE.idRessource=COEFFICIENT.idRessource INNER JOIN ENSEIGNEMENT ON RESSOURCE.idRessource = ENSEIGNEMENT.idRessource WHERE COEFFICIENT.idUE = ? AND mailUser = ?;";
		$arguments = array();
		array_push($arguments, $idUE, $mailUser);
		return $this->readQuery($sql, $arguments);
	}

	/**
	* @param idUE     Identidiant d'une unité d'enseignement
	* @return         Une liste des ressources associées à l'unité d'enseignement passée en paramètre
	*/
	function getRessourcesFromUE($idUE) {
		$sql = "SELECT RESSOURCE.idRessource, nomRessource, idSemestre FROM RESSOURCE INNER JOIN COEFFICIENT ON RESSOURCE.idRessource = COEFFICIENT.idRessource WHERE idUE=?;";
		$arguments = array();
		array_push($arguments, $idUE);
		return $this->readQuery($sql, $arguments);
	}

}

?>