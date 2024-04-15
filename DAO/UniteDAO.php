<?php

include_once('../Model/Unite.class.php');
include_once('DAO.class.php');

class UniteDAO {
	
	/**
	* @param tmp Ligne reçue depuis la base de données
	* @return    Un objet note créé à partir de Unite.class.php
	*/
	public function createUnite($tmp) {

		$unite = new Unite();

		$unite->setIdUE($tmp['idUE']);
		$unite->setNomUE($tmp['nomUE']);

		return $unite;

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
			$unite = $this->createUnite($tmp);
			array_push($list, $unite);
		}
		return $list;
	}

	/**
	* @param idSemestre Identifiant d'un semestre
	* @return           Une liste d'unités d'enseignement associée au semestre passé en paramètre
	*/
	function getUnitesFromSemestre($idSemestre) {
		$sql = "SELECT DISTINCT UNITE.idUE, nomUE FROM UNITE INNER JOIN COEFFICIENT ON UNITE.idUE = COEFFICIENT.idUE INNER JOIN RESSOURCE ON COEFFICIENT.idRessource = RESSOURCE.idRessource WHERE idSemestre=? ORDER BY UNITE.idUE;";
		$arguments = array();
		array_push($arguments, $idSemestre);
		return $this->readQuery($sql, $arguments);
	}

	/**
	* @param mailUser   Adresse mail d'un enseignant
	* @param idSemestre Identifiant d'un semestre
	* @return           Une liste d'unités d'enseignement au semestre passé en paramètre pour l'enseignant passé en paramètre      
	*/
	function getUnitesFromTeacherAndSemestre($mailUser, $idSemestre) {
		$sql = "SELECT UNITE.idUE, nomUE FROM UNITE INNER JOIN ENSEIGNEMENT ON UNITE.idUE = ENSEIGNEMENT.idUE INNER JOIN RESSOURCE ON ENSEIGNEMENT.idRessource = RESSOURCE.idRessource WHERE mailUser=? AND idSemestre=? ;";
		$arguments = array();
		array_push($arguments, $mailUser, $idSemestre);
		return $this->readQuery($sql, $arguments);
	}

	/**
	* @param idUE     Identifiant d'une unité d'enseignement
	* @param mailUser Adresse mail d'un enseignant
	* @return         Le nombre de ressources pour l'unité passée en paramètre qu'a l'enseignant passé en paramètre
	*/
	function getNbRessourcesForTeacher($idUE, $mailUser) {
		$sql = "SELECT COUNT(idUE) AS 'idUE', '' AS 'nomUE' FROM ENSEIGNEMENT WHERE idUE = ? AND mailUser = ?;";
		$arguments = array();
		array_push($arguments, $idUE, $mailUser);
		$list = $this->readQuery($sql, $arguments);
		$nbRessources = 0;
		if (count($list)>0)
			$nbRessources = $list[0]->idUE;
		return $nbRessources;
	}

	/**
	* @param idUE     Identifiant d'une unité d'enseignement
	* @return         Le nombre de ressources pour l'unité passée en paramètre
	*/
	function getNbRessourcesFromUE($idUE) {
		$sql = "SELECT COUNT(idRessource) AS 'idUE', '' AS 'nomUE' FROM COEFFICIENT WHERE idUE = ?;";
		$arguments = array();
		array_push($arguments, $idUE);
		$list = $this->readQuery($sql, $arguments);
		$nbRessources = 0;
		if (count($list)>0)
			$nbRessources = $list[0]->idUE;
		return $nbRessources;
	}

	function getNbUEFromSemestre($idSemestre) {
		$sql = "SELECT COUNT(DISTINCT UNITE.idUE) AS 'idUE', '' AS nomUE
		FROM UNITE INNER JOIN COEFFICIENT ON UNITE.idUE = COEFFICIENT.idUE 
		INNER JOIN RESSOURCE ON COEFFICIENT.idRessource = RESSOURCE.idRessource
		WHERE idSemestre = ?;";
		$arguments = array();
		array_push($arguments, $idSemestre);
		$list = $this->readQuery($sql, $arguments);
		return count($list);
	}

}

?>