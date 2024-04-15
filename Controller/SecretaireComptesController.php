<?php

include_once('../DAO/UserDAO.php');
include_once('../DAO/RoleDAO.php');
include_once('../DAO/PromotionDAO.php');
include_once('../DAO/GroupeDAO.php');

class SecretaireComptesController
{

    public $userDao;
    public $roleDao;
    public $promoDao;
    public $groupeDao;

    function __construct(){
        $this->userDao = new UserDAO();
        $this->roleDao = new RoleDAO();
        $this->promoDao = new PromotionDAO();
        $this->groupeDao = new GroupeDAO();
    }

    public function ajouterUnCompte($nom, $pnom, $mail, $role, $promo){
        $this->userDao->createNewUser($mail, $nom, $pnom, $role);
        if ($role == 1){
            if ($promo != 'Non définie'){
                $this->groupeDao->relierEtudiantAPromo($mail, $promo);
            }
        }
    }

    public function afficherLesUtilisateursParRole($role){
        $html = "
        <table cellpadding='5' border = '1' class='tableau tableauFix'>
		<tbody>";
        foreach ($this->userDao->getUtilisateursHorsSecretaire() as $user){
            if(in_array($user->role, $role)){
                $html .= "<tr ";
                if ( $user->role== 'Etudiant'){
                    $html .= "class='elementListe' ";
                }
                $html .= "id='".$user->mailUser."'><td>".$user->nomUser."</td><td>".$user->pnomUser."</td><td>".$user->mailUser."</td><td>".$user->role.
                    "</td><td><button id='modifCompte' class=".$user->mailUser."><img id='imgM' src='../Image/edit.png'/></button></td>
                    <td><button id='supprCompte' class=".$user->mailUser."><img id='imgM' src='../Image/bin.png'/></button>
                    </td></tr>";
            }
        }
        $html .= "</tbody></table>";
        return $html;
    }

    function afficherLesEtudiantsParPromotion($promo){
        $html = "
        <table cellpadding='5' class='tableau tableauFix' border='1'>
		<tbody>";
        foreach ($this->userDao->getEtudiantsParPromotion($promo) as $user){
            $html .= "<tr class='elementListe' id='".$user->mailUser."'><td>".$user->nomUser."</td><td>".$user->pnomUser."</td><td>".$user->mailUser."</td><td>".$user->role.
                    "</td><td><button id='modifCompte' class=".$user->mailUser."><img id='imgM1' src='../Image/edit.png'/></button></td>
                    <td><button id='supprCompte' class=".$user->mailUser."><img id='imgM1' src='../Image/bin.png'/></button>
                    </td></tr>";
               
        }
        $html .= "</tbody></table>";
        return $html;
    }

    public function returnUserSelectionne($idUser){
        foreach ($this->userDao->getUtilisateursHorsSecretaire() as $user){
            if ($user->mailUser == $idUser){
                return $user;
            }
        }
    }

    public function supprimerCompte($idUser){
        $this->userDao->supprUser($idUser);
    }

    public function getListRolesSaufRoleCourant($nomRoleCourant){
        return $this->roleDao->getRolesSauf($nomRoleCourant);
    }

    public function modifierCompte($idUserCourant)
    {
        if (isset($_GET['subFormModifCompte'])){
            $this->userDao->modifierUser($idUserCourant, $_GET['mailUserMod'], $_GET['nomUserMod'], $_GET['pnomUserMod'], $_GET['roleUserMod']);
        }
    }

    public function getIdRoleParNom($nomRole){
        $role = $this->roleDao->getRoleParNom($nomRole)[0];
        return $role->idRole;
    }

    public function getAllPromos(){
        return $this->promoDao->getPromotions();
    }

    function getFirstPromo() {
		$promotionDAO = new PromotionDAO();

		$promotion = $promotionDAO->getFirstPromo();

		return $promotion;
	}

    public function deplaceEtudiants($promo){
        $listGroupes = $this->groupeDao->getGroupes();
        foreach ($_SESSION['listEtus'] as $etu){
            $etuDejaRelie = false;
            foreach ($listGroupes as $groupe){
                if ($etu == $groupe->getIdEtu()){
                    $etuDejaRelie = true;
                }
            }
            if ($etuDejaRelie == false){
                if ($promo != 'Non définie'){
                    $this->groupeDao->relierEtudiantAPromo($etu, $promo);
                }
            }
            else {
                if ($promo != 'Non définie'){
                    $this->groupeDao->changerPromoEtu($etu, $promo);
                }
                else {
                    $this->groupeDao->supprimerLien($etu);
                }
            }
        }
    }

    public function refresh(){

        echo '<script type="text/javascript">window.onload = function() {
                refresh();
                };</script>';

    }

}