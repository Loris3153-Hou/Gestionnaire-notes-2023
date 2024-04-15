<?php

include_once('../Model/Coefficient.class.php');
include_once('DAO.class.php');

 class CoefficientDAO {
 	
 	/**
	* @param tmp Ligne reçue depuis la base de données
	* @return    Un objet note créé à partir de Coefficient.class.php
	*/
 	public function createCoefficient($tmp) {

 		$coefficient = new Coefficient();

 		$coefficient->setIdRessource($tmp['idRessource']);
 		$coefficient->setIdUE($tmp['idUE']);
 		$coefficient->setCoefRessource($tmp['coefRessource']);

 		return $coefficient;

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
			$coefficient = $this->createCoefficient($tmp);
			array_push($list, $coefficient);
		}
		return $list;
	}

	/**
	* @param  idRessource Identifiant d'une ressource
	* @return Une liste de coefficients pour la ressource passée en paramètre
	*/
	public function getCoeffFromRessource($idRessource) {
		$sql = "SELECT * FROM COEFFICIENT WHERE idRessource = ?;";
		$arguments = array();
		array_push($arguments, $idRessource);
		return $this->readQuery($sql, $arguments);
	}

	/**
	* @param res   Ressource
	* @param unite Unité
	* @return      True s'il y a un coefficient associé à la ressource et à l'unité passées en paramètre, False si non
	*/
	public function existsCoeff($res, $unit){
        $sql = "SELECT * FROM COEFFICIENT WHERE idRessource = ? AND idUE = ?;";
        $arguments = array();
        array_push($arguments, $res->idRessource, $unit->idUE);
        if($this->readQuery($sql, $arguments) != null){
            return true;
        }
        return false;
    }

 }

?>