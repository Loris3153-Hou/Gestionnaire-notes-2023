<?php

include_once('../Model/Promotion.class.php');
include_once('DAO.class.php');

class PromotionDAO {
	
	/**
	* @param tmp Ligne reçue depuis la base de données
	* @return    Un objet note créé à partir de Promotion.class.php
	*/
	function createPromotion($tmp) {
		$promotion = new Promotion();

		$promotion->setIdPromo($tmp['idPromo']);
		$promotion->setIdSemestre($tmp['idSemestre']);

		return $promotion;
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
			$promotion = $this->createPromotion($tmp);
			array_push($list, $promotion);
		}
		return $list;
	}

	private function executeQuery($sql, $arguments){
        $dao = new DAO();

        $bdd = new PDO("mysql:host=$dao->address;dbname=$dao->db_name",$dao->user,$dao->pass);

        $rs = $bdd->prepare($sql);
        $rs->execute($arguments);
    }

	/**
	* @return La liste de toutes les promotions
	*/
	function getPromotions() {
		$sql = "SELECT * FROM PROMOTION ORDER BY idPromo";
		return $this->readQuery($sql, array());
	}

	/**
	* @param mailUser Adresse mail d'un étudiant
	* @return         Le semestre courant d'un étudiant s'il y en a un, "NULL" s'il n'en a pas
	*/
	function getSemestreCourant($mailUser) {
		$sql = "SELECT PROMOTION.idPromo, idSemestre FROM PROMOTION INNER JOIN GROUPE ON PROMOTION.idPromo = GROUPE.idPromo WHERE mailUser = ?";
		$arguments = array();
		array_push($arguments, $mailUser);
		$list = $this->readQuery($sql, $arguments);
		$semestreCourant = "NULL";
		if (count($list)==1) {
			$semestreCourant = $list[0]->idSemestre;
		}
		return $semestreCourant;
	}

	/**
	* @return La première promotion de la liste
	*/
	function getFirstPromo() {
		$sql = "SELECT * FROM PROMOTION ORDER BY idPromo;";
		$list = $this->readQuery($sql, array());
		$idFirstPromo = $list[0]->idPromo;
		return $idFirstPromo;
	}

	/**
	* @param mailUser Adresse mail d'un étudiant
	* @return         La promotion associée à l'étudiant passé en paramètre
	*/
	function getPromoFromUser($mailUser) {
		$sql = "SELECT * FROM PROMOTION INNER JOIN GROUPE ON PROMOTION.idPromo=GROUPE.idPromo WHERE mailUser = ?";
		$arguments = array();
		array_push($arguments, $mailUser);
		$list = $this->readQuery($sql, $arguments);
		return $list[0];
	}

	function getSemestreCourantFromPromo($idPromotion) {
		$sql = "SELECT * FROM PROMOTION WHERE idPromo = ?";
		$arguments = array();
		array_push($arguments, $idPromotion);
		return $this->readQuery($sql, $arguments)[0]->idSemestre;
	}

	function changeSemestreCourant($promo, $sem){
        $sql = "UPDATE PROMOTION SET idSemestre = ? WHERE idPromo = ?;";
        $arguments = array();
        array_push($arguments, $sem, $promo);
        $this->executeQuery($sql, $arguments);
    }

}

?>