<?php

include_once('../DAO/UserDAO.php');
include_once('../DAO/PromotionDAO.php');
include_once('../DAO/SemestreDAO.php');
include_once('../DAO/RessourceDAO.php');
include_once('../DAO/NoteDAO.php');
include_once('../DAO/UniteDAO.php');

class TeacherController {

	function printTableForRessources($idPromotion) {
		$userDAO = new UserDAO();
		$ressourceDAO = new RessourceDAO();
		$noteDAO = new NoteDAO();
		$promotionDAO = new PromotionDAO();
		$semestreDAO = new SemestreDAO();
		$uniteDAO = new UniteDAO();

		$idSemestre = $promotionDAO->getSemestreCourantFromPromo($idPromotion);

		// on récupère les promotions
		$promos = $promotionDAO->getPromotions();

		// on récupère les semestres
		$semestres = $semestreDAO->getSemestres();

		// on récupère tous les étudiants de la promo choisie
		$users = $userDAO->getUsersFromPromo($idPromotion);

		// on récupère les unités du semestre concerné
		$unites = $uniteDAO->getUnitesFromSemestre($idSemestre);

		$teachersRessources = $ressourceDAO->getRessourceWithTeacher($_SESSION['mailUser']);

		// on commence le tableau bla bla bla
		$table = "
		<table id='notessd' class='tableauFix' border = '1'>
			<tbody>
				<tr>
					<td rowspan='2' class = 'unitesd fixedC'>" . $idPromotion . " - " . $idSemestre . "</td>";

		// on ajoute les unités dans une ligne, chaque unité a une largeur correspondante au nombre de ressource qu'elle possède
		foreach ($unites as $unite) {
			$table .= "<td class = 'unitesd fixed' id='top".substr($unite->idUE, 4, 1)."st' colspan = '" . $uniteDAO->getNbRessourcesFromUE($unite->idUE) . "'>$unite->idUE</td>";
		}
		$table .= "</tr><tr>";

		// on ajoute les ressources en dessous des unités qui leurs sont associées
		foreach ($unites as $unite) {
			$ressources = $ressourceDAO->getRessourcesFromUE($unite->idUE);
			foreach ($ressources as $ressource) {
				$table .= "<td class = 'ressource fixed' id='" . $unite->idUE . $ressource->idRessource . "'>" . $ressource->idRessource . "</td>";
			}
		}
		$table .= "</tr>";

		// pour chaque étudiant, on va ajouter sa note par rapport à une unité et une ressource données
		foreach ($users as $user) { // pour chaque étudiant
			// on commence par mettre son prénom et nom dans la colonne de gauche
			$table .= "<tr><td class = 'etu fixed' id='" . $user->mailUser . "'>" . $user->pnomUser . " " . $user->nomUser . "</td>";
			foreach ($unites as $unite) {
				$ressources = $ressourceDAO->getRessourcesFromUE($unite->idUE);
				foreach ($ressources as $ressource) {
					if (in_array($ressource, $teachersRessources)) {
						$table .= "<td class = 'notesd'><input onclick='selectTheMark(this)' onkeydown='modifierNote(event, this)' id = '" . $user->mailUser . "," . $ressource->idRessource . "," . $unite->idUE . "' class='inputsd' type='text' value = " . $noteDAO->getNoteFromUserAndUEAndRessource($user->mailUser, $unite->idUE, $ressource->idRessource) . ">";
					} else {
						$table .= "<td class = 'notesd' id = '" . $user->mailUser . "," . $ressource->idRessource . "," . $unite->idUE . "' onclick='selectTheMark(this)'>" . $noteDAO->getNoteFromUserAndUEAndRessource($user->mailUser, $unite->idUE, $ressource->idRessource);
					}
					$table .= "</td>";
				}
			}
			$table .= "</tr>";
		}
		$table .= "
			</tbody>
		</table>";

		echo $table;

	}

	function printPromos() {
		$promotionDAO = new PromotionDAO();

		$promotions = $promotionDAO->getPromotions();

		$boutonsPromo = 
		"
		<div id='promosd'>
		";
		foreach ($promotions as $promotion)
			$boutonsPromo .= "<input type='submit' onclick = 'changePromoSD(this)' id='".$promotion->idPromo."sd' value='$promotion->idPromo'>";

		echo $boutonsPromo . "</div>";
	}

	function getPromos() {
		$promotionDAO = new PromotionDAO();

		$promotions = $promotionDAO->getPromotions();

		return $promotions;
	}

	function getSemestres() {
		$semestreDAO = new SemestreDAO();

		$semestres = $semestreDAO->getSemestres();

		return $semestres;
	}

	function getFirstPromo() {
		$promotionDAO = new PromotionDAO();

		$promotion = $promotionDAO->getFirstPromo();

		return $promotion;
	}

	function getFirstSemestre() {
		$semestreDAO = new SemestreDAO();

		$semestre = $semestreDAO->getFirstSemestre();

		return $semestre;
	}

	function deleteNote($mailUser, $idRessource, $idUE) {
		$noteDAO = new NoteDAO();
		$noteDAO->deleteNote($mailUser, $idRessource, $idUE);
	}

	function noteExists($mailUser, $idRessource, $idUE) {
		$noteDAO = new NoteDAO();
		return $noteDAO->noteExists($mailUser, $idRessource, $idUE);
	}

	public function updateNote($mailUser, $idRessource, $idUE, $note) {
		$noteDAO = new NoteDAO();
		$noteDAO->updateNote($mailUser, $idRessource, $idUE, $note);
	}

	public function insertNote($mailUser, $idRessource, $idUE, $note) {
		$noteDAO = new NoteDAO();
		$noteDAO->insertNote($mailUser, $idRessource, $idUE, $note);
	}

}

?>