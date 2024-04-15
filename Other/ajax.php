<?php
include_once('../Controller/StudentController.php');
include_once('../Controller/StudiesDirectorController.php');
include_once('../Controller/TeacherController.php');
include_once('../Controller/SecretaryController.php');
include_once('../DAO/DAO.class.php');
include_once("../Controller/SecretaireNotesController.php");
include_once("../Controller/SecretaireComptesController.php");


$studentController = new StudentController();
$studiesDirectorController = new StudiesDirectorController();
$teacherController = new TeacherController();
$secretaryController = new SecretaryController();
$dao = new DAO();
$snc = new SecretaireNotesController();
$scc = new SecretaireComptesController();

//Etudiant
if (isset($_GET['semestreCliqueEtu'])) {
	$studentController->printTable($_GET['semestreCliqueEtu']);
}

//Directeur des études
if (isset($_GET['promotionCliqueSD'])) {
	$_SESSION['choixPromo'] = $_GET['promotionCliqueSD'];

	$studiesDirectorController->printTableFor($_SESSION['vue'], $_SESSION['choixPromo'], $_SESSION['choixSemestre']);
}

if (isset($_GET['semestreCliqueSD'])) {
	$_SESSION['choixSemestre'] = $_GET['semestreCliqueSD'];
	
	$studiesDirectorController->printTableFor($_SESSION['vue'], $_SESSION['choixPromo'], $_SESSION['choixSemestre']);
}

if (isset($_GET['view'])) {
	$_SESSION['vue'] = $_GET['view'];
	$studiesDirectorController->printTableFor($_SESSION['vue'], $_SESSION['choixPromo'], $_SESSION['choixSemestre']);
}

if (isset($_GET['mailUser']) && isset($_GET['idRessource']) && isset($_GET['idUE']) && isset($_GET['note'])) {
	if ($_GET['note'] == "") {
		$studiesDirectorController->deleteNote($_GET['mailUser'], $_GET['idRessource'], $_GET['idUE']);
	} else if ($studiesDirectorController->noteExists($_GET['mailUser'], $_GET['idRessource'], $_GET['idUE'])) {
		$studiesDirectorController->updateNote($_GET['mailUser'], $_GET['idRessource'], $_GET['idUE'], $_GET['note']);
	} else {
		$studiesDirectorController->insertNote($_GET['mailUser'], $_GET['idRessource'], $_GET['idUE'], $_GET['note']);
	}

	$studiesDirectorController->printTableFor($_SESSION['vue'], $_SESSION['choixPromo'], $_SESSION['choixSemestre']);
}

//Enseignant
if (isset($_GET['promotionCliqueTe'])) {
	$_SESSION['choixPromo'] = $_GET['promotionCliqueTe'];
	$teacherController->printTableForRessources($_SESSION['choixPromo']);
}

if (isset($_GET['mailUserTe']) && isset($_GET['idRessource']) && isset($_GET['idUE']) && isset($_GET['note'])) {
	if ($_GET['note'] == "") {
		$teacherController->deleteNote($_GET['mailUserTe'], $_GET['idRessource'], $_GET['idUE']);
	} else if ($teacherController->noteExists($_GET['mailUserTe'], $_GET['idRessource'], $_GET['idUE'])) {
		$teacherController->updateNote($_GET['mailUserTe'], $_GET['idRessource'], $_GET['idUE'], $_GET['note']);
	} else {
		$teacherController->insertNote($_GET['mailUserTe'], $_GET['idRessource'], $_GET['idUE'], $_GET['note']);
	}

	$teacherController->printTableForRessources($_SESSION['choixPromo']);
}

//Secrétaire
if (isset($_GET['semestreCliqueSe'])) {
	$_SESSION['choixSemestre'] = $_GET['semestreCliqueSe'];
	$secretaryController->printTableForEnseignement($_SESSION['choixSemestre']);
}

if (isset($_GET['mailUserSe']) && isset($_GET['idUE']) && isset($_GET['idRessource'])) {
	$secretaryController->updateEnseignement($_GET['mailUserSe'], $_GET['idUE'], $_GET['idRessource']);
	$secretaryController->printTableForEnseignement($_SESSION['choixSemestre']);
}

if (isset($_GET['bddAddress'])) {
	$dao->address = $_GET['bddAddress'];
}

if (isset($_GET['promotionCliqueNotes'])) {
	$_SESSION['promo'] = $_GET['promotionCliqueNotes'];
	$snc->printTableForRessources($_SESSION['promo'], $_SESSION['sem']);
}

if (isset($_GET['semestreCliqueNotes'])) {
	$_SESSION['sem'] = $_GET['semestreCliqueNotes'];
	$snc->printTableForRessources($_SESSION['promo'], $_SESSION['sem']); 
}

if (isset($_GET['semestreCliqueS'])) {
	$_SESSION['sem'] = $_GET['semestreCliqueS'];
	$snc->afficheTableau($_SESSION['sem'], $_SESSION['promo']);
}

if (isset($_GET['promotionCliqueS'])) {
	$_SESSION['promo'] = $_GET['promotionCliqueS'];
	$snc->afficheTableau($_SESSION['sem'], $_SESSION['promo']);
}

if (isset($_GET['suppr'])){
    $scc->supprimerCompte($_GET['suppr']);
}

if (isset($_GET['changePromo'])){
    $scc->deplaceEtudiants($_GET['changePromo']);
}

if (isset($_GET['listEtus'])){
	$listEtus = array();
	$mail = '';
	for($i = 0; $i < strlen($_GET['listEtus']); $i++){
		if ($_GET['listEtus'][$i] != ','){
			$mail .= $_GET['listEtus'][$i];
		}
		else {
			array_push($listEtus, $mail);
			$mail = '';
		}
	}
	array_push($listEtus, $mail);
    $_SESSION['listEtus'] = $listEtus;
}

if (isset($_GET['idPromo']) && isset($_GET['idSem'])) { //mathilde
    $secretaryController->changeSemestreCourant($_GET['idPromo'], $_GET['idSem']);
    $secretaryController->printTableForPromotion();
}

if (isset($_GET['refresh'])){
	if(isset($_SESSION['visionTriCourant'])){
                if ($_SESSION['visionTriCourant'] == 'etus'){
                    echo $scc->afficherLesUtilisateursParRole(array('Etudiant'));
                }
                else if ($_SESSION['visionTriCourant'] == 'triPromo' && isset($_SESSION['triPromo'])){
                    echo $scc->afficherLesEtudiantsParPromotion($_SESSION['triPromo']);
                }
                else if ($_SESSION['visionTriCourant'] == 'perso'){
                    echo $scc->afficherLesUtilisateursParRole(array('Enseignant','Directeur des études'));
                }
                else {
                    echo $scc->afficherLesUtilisateursParRole(array('Etudiant','Enseignant','Directeur des études'));
                }
            }
            else {
                echo $scc->afficherLesUtilisateursParRole(array('Etudiant','Enseignant','Directeur des études'));
            }
}

?>