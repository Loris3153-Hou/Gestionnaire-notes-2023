<?php 

include_once('../Model/User.class.php');
include_once('DAO.class.php');

class UserDAO {

	/**
	* @param tmp Ligne reçue depuis la base de données
	* @return    Un objet note créé à partir de User.class.php
	*/
	public function createUser($tmp) {

		$user = new User();
		$user->setMailUser($tmp['mailUser']);
		$user->setNomUser($tmp['nomUser']);
		$user->setPnomUser($tmp['pnomUser']);
		switch ($tmp['idRole']) {
			case '1':
			$role = 'Etudiant';
			break;
			case '2';
			$role = 'Enseignant';
			break;
			case '3':
			$role = 'Directeur des études';
			break;
			case '4':
			$role = 'Secrétaire';
			break;

		}
		$user->setRoleUser($role);
		$user->setPassUser($tmp['passUser']);

		return $user;
	}

	/**
	* @param sql       Requête SQL
	* @param arguments Tableau d'attributs qui remplaceront les ? dans la requête SQL
	* @return          Une liste de notes issues de la base de données
	*/
	public function readQuery($sql, $arguments) {

		$dao = new DAO();

		$bdd = new PDO("mysql:host=$dao->address;dbname=$dao->db_name",$dao->user,$dao->pass);

		$rs = $bdd->prepare($sql);
		$rs->execute($arguments);

		$list = array();
		while ($tmp = $rs->fetch()) {
			$user = $this->createUser($tmp);
			array_push($list, $user);
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
	* @param mailUser Adresse mail d'un utilisateur
	* @return         L'utilisateur dont l'adresse mail correspond à celle passée en paramètre
	*/
	public function getUser($mailUser) {
		$sql = "SELECT * FROM UTILISATEUR WHERE mailUser = ?;";
		$arguments = array();
		array_push($arguments, $mailUser);
		$list = $this->readQuery($sql, $arguments);
		if (count($list)>0) {
			return $list[0];
		}
		return "";
	}

	/**
	* @param idPromo Identifiant d'une promotion
	* @return        Une liste d'étudiants associée à la promotion passée en paramètre
	*/
	public function getUsersFromPromo($idPromo) {
		$sql = "SELECT * FROM UTILISATEUR INNER JOIN GROUPE ON UTILISATEUR.mailUser=GROUPE.mailUser INNER JOIN ROLE ON ROLE.idRole = UTILISATEUR.idRole WHERE idPromo = ?;";
		$arguments = array();
		array_push($arguments, $idPromo);
		return $this->readQuery($sql, $arguments);
	}

	/**
	* @param role Role d'un utilisateur
	* @return     La liste de tous les utilisateurs ayant le rôle passé en paramètre
	*/
	public function getUsersFromRole($role) {
		$sql = "SELECT * FROM UTILISATEUR INNER JOIN ROLE ON ROLE.idRole = UTILISATEUR.idRole WHERE ROLE.idRole IN (SELECT idRole FROM ROLE WHERE nomRole = ?)";
		$arguments = array();
		array_push($arguments, $role);
		return $this->readQuery($sql, $arguments);
	}

	public function getUtilisateursHorsSecretaire() {
		$sql = "SELECT * FROM UTILISATEUR WHERE idRole != 4;";
		$arguments = array();
		array_push($arguments);
		return $this->readQuery($sql, $arguments);
	}

	public function getEtudiantsParPromotion($promotion){
	    $sql = "SELECT UTILISATEUR.* FROM GROUPE INNER JOIN UTILISATEUR ON GROUPE.mailUser = UTILISATEUR.mailUser WHERE idPromo = ?;";
        $arguments = array();
        array_push($arguments, $promotion);
        return $this->readQuery($sql, $arguments);
    }

    public function createNewUser($mail, $nom, $pnom, $role){
        $sql = "INSERT INTO UTILISATEUR VALUES (?, ?, ?, NULL, ?);";
        $arguments = array();
        array_push($arguments, $mail, $nom, $pnom, $role);
        $this->executeQuery($sql, $arguments);
    }

    public function supprUser($mail){
        $sql = "DELETE FROM NOTE WHERE mailUser = ?;";
        $arguments = array();
        array_push($arguments, $mail);
        $this->executeQuery($sql, $arguments);

        $sql = "DELETE FROM GROUPE WHERE mailUser = ?;";
        $this->executeQuery($sql, $arguments);

        $sql = "DELETE FROM UTILISATEUR WHERE mailUser = ?;";
        $this->executeQuery($sql, $arguments);

        $sql = "DELETE FROM ENSEIGNEMENT WHERE mailUser = ?;";
        $this->executeQuery($sql, $arguments);
    }

    public function modifierUser($mailCourant, $mail, $nom, $pnom, $role){
        $sql = "UPDATE UTILISATEUR SET mailUser = ?, nomUser = ?, pnomUser = ?, idRole = ? WHERE mailUser = ?;";
        $arguments = array();
        array_push($arguments, $mail, $nom, $pnom, $role, $mailCourant);
        $this->executeQuery($sql, $arguments);
    }

}
?>