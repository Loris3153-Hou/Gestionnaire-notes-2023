<?php

include_once('../Model/Note.class.php');
include_once('DAO.php');

class NoteDAO {
	
	/**
	* @param tmp Ligne reçue depuis la base de données
	* @return    Un objet note créé à partir de Note.class.php
	*/
	public function createNote($tmp) {
		$note  = new Note();

		$note->setMailUser($tmp['mailUser']);
		$note->setIdRessource($tmp['idRessource']);
		$note->setIdUE($tmp['idUE']);
		$note->setNote($tmp['note']);

		return $note;
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
			$note = $this->createNote($tmp);
			array_push($list, $note);
		}
		return $list;
	}

	/**
	* @param sql       Requête SQL
	* @param arguments Tableau d'attributs qui remplaceront les ? dans la requête SQL
	* @return          Une liste de notes issues de la base de données
	*/
	function editQuery($sql, $arguments) {

		$dao = new DAO();

		$bdd = new PDO("mysql:host=$dao->address;dbname=$dao->db_name",$dao->user,$dao->pass);

		$rs = $bdd->prepare($sql);
		$rs->execute($arguments);
	}

	/**
	* @param mailUser    Adresse mail d'un étudiant
	* @param idUE        Identifiant d'une unité d'enseignement
	* @param idRessource Identifiant d'une ressource
	* @return            La note d'un étudiant arrondie à 0.01 pour une unité et une ressource donnée s'il y en a une de renseignée dans la base de données ou "--" s'il n'y a pas de note renseignée
	*/
	public function getNoteFromUserAndUEAndRessource($mailUser, $idUE, $idRessource) {
		$sql = "SELECT * FROM NOTE WHERE mailUser = ? AND idUE = ? AND idRessource = ?;";
		$arguments = array();
		array_push($arguments, $mailUser, $idUE, $idRessource);
		$list = $this->readQuery($sql, $arguments);
		$note = "--";
		if (count($list)>0)
			$note = round(floatval($list[0]->note), 2);
		return $note;
	}

	/**
	* @param mailUser Adresse mail d'un étudiant
	* @param idUE     Identifiant d'une unité d'enseignement
	* @return 		La moyenne de l'unité d'enseignement passée en paramètre arrondie à 0.01
	*/
	public function getAverageMarkFromUserAndUE($mailUser, $idUE) {
		$sql = "SELECT mailUser, '' AS idRessource, NOTE.idUE, SUM(note*coefRessource)/100 AS 'note'
		FROM COEFFICIENT INNER JOIN RESSOURCE ON COEFFICIENT.idRessource = RESSOURCE.idRessource
		INNER JOIN NOTE ON RESSOURCE.idRessource = NOTE.idRessource AND COEFFICIENT.idUE = NOTE.idUE
		WHERE mailUser = ? AND  NOTE.idUE = ? ;";
		$arguments = array();
		array_push($arguments, $mailUser, $idUE);
		$notes = $this->readQuery($sql, $arguments);
		$moyenne = round(floatval($notes[0]->note), 2);
		return $moyenne;
	}

	/**
	* @param mailUser    Adresse mail d'un étudiant
	* @param idRessource Identifiant d'une ressource
	* @return            La moyenne de la ressource passée en paramètre arrondie à 0.01 s'il y a des notes renseignées ou "--" s'il n'y en a pas
	*/
	public function getAverageMarkFromUserAndRessource($mailUser, $idRessource) {
		$sql = "SELECT mailUser, idRessource, '' AS idUE, AVG(note) AS note FROM NOTE WHERE mailUser = ? AND idRessource = ?";
		$arguments = array();
		array_push($arguments, $mailUser, $idRessource);
		$list = $this->readQuery($sql, $arguments);
		$note = $list[0]->note;
		if (is_numeric($note))
			$note = round(floatval($note), 2);
		else
			$note = "--";
		return $note;
	}

	/**
	* @param idPromo     Identifiant d'une promotion
	* @param idRessource Identifiant d'une ressource
	* @return            La moyenne de tous les étudiants de la promotion passée en paramètre pour la ressource passée en paramètre arrondie à 0.01 ou "--" s'il n'y a pas de notes entrées
	*/
	public function getAverageMarkFromEveryStudentAndRessource($idPromo, $idRessource){
		$sql = "SELECT NOTE.mailUser, idRessource, idUE, (SUM(note) / COUNT(note)) AS 'note', idPromo
		FROM NOTE INNER JOIN GROUPE
		ON NOTE.mailUser = GROUPE.mailUser WHERE idPromo = ? AND idRessource = ?;";
		$arguments = array();
		array_push($arguments, $idPromo, $idRessource);
		$notes = $this->readQuery($sql, $arguments);
		$moyenne = "--";
		if (count($notes)>0) {
			$moyenne = round(floatval($notes[0]->note), 2);
		}
		return $moyenne;
	}

	/**
	* @param mailUser    Adresse mail d'un étudiant
	* @param idRessource Identifiant d'une ressource
	*/
	public function deleteNote($mailUser, $idRessource, $idUE) {
		$sql = "DELETE FROM NOTE WHERE mailUser = ? AND idRessource = ? AND idUE = ?;";
		$arguments = array();
		array_push($arguments, $mailUser, $idRessource, $idUE);
		$this->editQuery($sql, $arguments);
	}

	/**
	* @param mailUser    Identifiant d'un étudiant
	* @param idRessource Identifiant d'une ressource
	* @param idUE        Idetifiant d'une unité
	*/
	function noteExists($mailUser, $idRessource, $idUE) {
		$sql = "SELECT * FROM NOTE WHERE mailUser = ? AND idRessource = ? AND idUE = ?;";
		$arguments = array();
		array_push($arguments, $mailUser, $idRessource, $idUE);
		return sizeof($this->readQuery($sql, $arguments))>0;
	}

	public function insertNote($mailUser, $idRessource, $idUE, $note) {
		$sql = "INSERT INTO NOTE VALUES (?, ?, ?, ?);";
		$arguments = array();
		array_push($arguments, $mailUser, $idRessource, $idUE, $note);
		$this->editQuery($sql, $arguments);
	}

	public function updateNote($mailUser, $idRessource, $idUE, $note) {
		$sql = "UPDATE NOTE SET note = ? WHERE mailUser = ? AND idRessource = ? AND idUE = ?;";
		$arguments = array();
		array_push($arguments, $note, $mailUser, $idRessource, $idUE);
		$this->editQuery($sql, $arguments);
	}

}

?>