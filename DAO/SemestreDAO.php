<?php

include_once('../Model/Semestre.class.php');
include_once('DAO.class.php');

class SemestreDAO {
	
	/**
	* @param tmp Ligne reçue depuis la base de données
	* @return    Un objet note créé à partir de Semestre.class.php
	*/
	public function createSemestre($tmp) {
		$semestre = new Semestre();

		$semestre->setIdSemestre($tmp['idSemestre']);

		return $semestre;
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
			$semestre = $this->createSemestre($tmp);
			array_push($list, $semestre);
		}
		return $list;
	}

	/**
	* @return La liste de tous les semestres
	*/
	public function getSemestres() {
		$sql = "SELECT * FROM SEMESTRE;";
		return $this->readQuery($sql, array());
	}

	/**
	* @return Le premier semestre de la base de données
	*/
	public function getFirstSemestre() {
		$list = $this->getSemestres();
		$idFirstSemestre = $list[0]->idSemestre;
		return $idFirstSemestre;
	}

}

?>