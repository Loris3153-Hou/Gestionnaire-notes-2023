<?php

class GroupeDAO
{

    private function createUser($tmp) {

        require_once '../Model/Groupe.php';

        $groupe = new Groupe($tmp['mailUser'], $tmp['idPromo']);
        return $groupe;
    }

    private function readQuery($sql, $arguments) {

        require 'DAO.php';

        $bdd = new PDO("mysql:host=localhost;dbname=$db_name",$user,$pass);
        $rs = $bdd->prepare($sql);
        $rs->execute($arguments);

        $list = array();
        while ($tmp = $rs->fetch()) {
            $groupe = $this->createUser($tmp);
            array_push($list, $groupe);
        }
        return $list;
    }

    private function executeQuery($sql, $arguments){
        require 'DAO.php';

        $bdd = new PDO("mysql:host=localhost;dbname=$db_name",$user,$pass);
        $rs = $bdd->prepare($sql);
        $rs->execute($arguments);
    }

    public function relierEtudiantAPromo($etu, $promo)
    {
        $sql = "INSERT INTO GROUPE VALUES (?, ?);";
        $arguments = array();
        array_push($arguments, $etu, $promo);
        $this->executeQuery($sql, $arguments);
    }

    public function changerPromoEtu($etu, $promo)
    {
        $sql = "UPDATE GROUPE SET idPromo = ? WHERE mailUser = ?;";
        $arguments = array();
        array_push($arguments, $promo, $etu);
        $this->executeQuery($sql, $arguments);
    }

    public function supprimerLien($etu)
    {
        $sql = "DELETE FROM GROUPE WHERE mailUser = ?;";
        $arguments = array();
        array_push($arguments, $etu);
        $this->executeQuery($sql, $arguments);
    }

    public function getGroupes()
    {
        $sql = "SELECT * FROM GROUPE;";
        $argument = array();
        array_push($argument);
        return $this->readQuery($sql, $argument);
    }

}