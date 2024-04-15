<?php
include_once('../Model/Role.class.php');
include_once('DAO.class.php');

class RoleDAO {

    private function createRole($tmp){

        $role = new Role($tmp['idRole'], $tmp['nomRole']);

        return $role;
    }

    private function readQuery($sql, $arguments) {
        $dao = new DAO();

		$bdd = new PDO("mysql:host=$dao->address;dbname=$dao->db_name",$dao->user,$dao->pass);
        
        $rs = $bdd->prepare($sql);
        $rs->execute($arguments);

        $list = array();
        while ($tmp = $rs->fetch()) {
            $role = $this->createRole($tmp);
            array_push($list, $role);
        }
        return $list;
    }

    public function getRolesSauf($roleCourant)
    {
        $sql = "SELECT * FROM ROLE WHERE nomRole != ? AND idRole != 4;";
        $argument = array();
        array_push($argument, $roleCourant);
        return $this->readQuery($sql, $argument);
    }

    public function getRoleParNom($nomRole)
    {
        $sql = "SELECT * FROM ROLE WHERE nomRole = ?;";
        $argument = array();
        array_push($argument, $nomRole);
        return $this->readQuery($sql, $argument);
    }

}