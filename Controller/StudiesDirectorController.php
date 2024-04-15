<?php

include_once('../DAO/UserDAO.php');
include_once('../DAO/PromotionDAO.php');
include_once('../DAO/SemestreDAO.php');
include_once('../DAO/RessourceDAO.php');
include_once('../DAO/NoteDAO.php');
include_once('../DAO/UniteDAO.php');

class StudiesDirectorController {

	function printTableFor($tableName, $idPromo, $idSemestre) {
		switch ($tableName) {
			case 'gestion':
				$this->printTableForUnitesAJ($idPromo, $idSemestre);
				break;
			case 'note':
				$this->printTableForRessources($idPromo, $idSemestre);
				break;
			case 'moyenne':
				$this->printTableForMoyennes($idPromo, $idSemestre);
				break;
			case 'résumé':
				$this->printTableForResultatPromo($idPromo, $idSemestre);
				break;
			default:
				echo "<em>Vue non reconnue</em>";
				break;
		}
	}

	function printTableForRessources($idPromotion, $idSemestre) {
		$userDAO = new UserDAO();
		$ressourceDAO = new RessourceDAO();
		$noteDAO = new NoteDAO();
		$promotionDAO = new PromotionDAO();
		$semestreDAO = new SemestreDAO();
		$uniteDAO = new UniteDAO();

		// on récupère les promotions
		$promos = $promotionDAO->getPromotions();

		// on récupère les semestres
		$semestres = $semestreDAO->getSemestres();

		// on récupère tous les étudiants de la promo choisie
		$users = $userDAO->getUsersFromPromo($idPromotion);

		// on récupère les unités du semestre concerné
		$unites = $uniteDAO->getUnitesFromSemestre($idSemestre);

		// on commence le tableau bla bla bla
		$table = "
		<table id='notessd' border = '1' class='tableauFix'>
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
					$table .= "<td class ='notesd'><input onclick='selectTheMark(this)' onkeydown='modifierNote(event, this)' id = '" . $user->mailUser . "," . $ressource->idRessource . "," . $unite->idUE . "' class='inputsd' type='text' value = " . $noteDAO->getNoteFromUserAndUEAndRessource($user->mailUser, $unite->idUE, $ressource->idRessource) . "></td>";
				}
			}
			$table .= "</tr>";
		}
		$table .= "
			</tbody>
		</table>";

		echo $table;

	}

	function printTableForResultatPromoSemestre($idPromo, $idSemestre) {
		$userDAO = new UserDAO();
		$uniteDAO = new UniteDAO();
		$noteDAO = new NoteDAO();

		$users = $userDAO->getUsersFromPromo($idPromo);
		$unites = $uniteDAO->getUnitesFromSemestre($idSemestre);

		$table = "
		<table border = '1' class='tableauFix'>
			<tbody>
				<tr>
					<td class='empty' colspan='2'></td>
		";

		$cpt = 1;
		foreach ($unites as $unite) {
			$table .= "<td colspan='2' class='competence fixed'>C" . $cpt . "</td>";
			$cpt++;
		}
		$table .= "</tr><tr><td>Nom</td><td>Prénom</td>";

		foreach ($unites as $unite) {
			$table .= "<td>$unite->idUE</td><td>Validation</td>";
		}

		$table .="<td>Nb UE AJ</td></tr>";

		foreach ($users as $user) {
			$cptUnitesAJ = 0;
			$table .= "<tr><td>$user->pnomUser</td><td>$user->nomUser</td>";
			foreach ($unites as $unite) {
				$moyenneUE = $noteDAO->getAverageMarkFromUserAndUE($user->mailUser, $unite->idUE);
				$colorMoyenne = "";
				$colorValidation = "";
				$validation = "ACQ";
				if ($moyenneUE<10) {
					$cptUnitesAJ++;
					$validation = "AJ";
					$colorValidation = "red";
				} 
				if ($moyenneUE<8)
					$colorMoyenne = "red";
				$table .= "<td $ class='moyenne $colorMoyenne'>$moyenneUE</td><td class='validation $colorValidation'>$validation</td>";
				
			}
			$color ="";
			if ($cptUnitesAJ>0)
				$color = "yellow";
			if ($cptUnitesAJ>1)
				$color = "orange";
			if ($cptUnitesAJ>2)
				$color = "red";
			$table .= "<td class='nbUEAJ $color'>$cptUnitesAJ</td></tr>";
		}

		$table .= "
			</tbody>
		</table>
		";

		echo $table;

	}

	function printTableForResultatPromo($idPromo, $idSemestre) {
		$promoDAO = new PromotionDAO();
		$uniteDAO = new UniteDAO();
		$userDAO = new UserDAO();
		$noteDAO = new NoteDAO();

		$users = $userDAO->getUsersFromPromo($idPromo);

		$premierS = $idSemestre;
		$secondS = "S" . strval(intval(substr($idSemestre, 1, 1)) + 1);

		if (intval(substr($idSemestre, 1, 1))%2==0) {
			$premierS = "S" . strval(intval(substr($idSemestre, 1, 1)) -1);
			$secondS = $idSemestre;
		}

		$table = "
		<table border = '1' class='tableauFix'>
			<tbody>
				<tr>
					<td class='empty' colspan='2'></td>
		";

		$unitesPremierS = $uniteDAO->getUnitesFromSemestre($premierS);
		$unitesSecondS = $uniteDAO->getUnitesFromSemestre($secondS);

		$cpt = 1;
		foreach ($unitesPremierS as $unite) {
			$table .= "<td colspan='6' class='competence fixed'>C" . $cpt . "</td>";
			$cpt++;
		}
		$table .= "</tr><tr><td class='fixed competence'>Nom Prénom</td>";

		$cpt=0;
		foreach ($unitesPremierS as $unite) {
			$numC = $cpt+1;
			$table .= "
			<td class='lightgreen fixed'>" . $unite->idUE . "</td>
			<td class='lightgreen fixed'>Décision</td>
			<td class='lightgreen fixed'>" . $unitesSecondS[$cpt]->idUE . "</td>
			<td class='lightgreen fixed'>Décision</td>
			<td class='competence green  fixed'>C" . $numC . "</td>
			<td class='green  fixed'>Décision</td>";

			$cpt++;
		}

		$table .="<td>Nb UE AJ</td></tr>";

		foreach($users as $user) {
			$cptUnitesAJ = 0;
			$table .= "<tr><td class='fixed competence'>".$user->pnomUser." ".$user->nomUser."</td>";
			$cpt = 0;
			foreach($unitesPremierS as $unite1) {
				$moyenneUEPremierS = $noteDAO->getAverageMarkFromUserAndUE($user->mailUser, $unite1->idUE);
				$color = "";
				$decision = "ACQ";
				if ($moyenneUEPremierS<10) {
					$color = "yellow";
					$decision = "AJ";
				}
				$table .= "<td class='moyenne $color'>$moyenneUEPremierS</td><td class='validation'>$decision</td>";

				$moyenneUESecondS = $noteDAO->getAverageMarkFromUserAndUE($user->mailUser, $unitesSecondS[$cpt]->idUE);
				$color = "";
				$decision = "ACQ";
				if ($moyenneUESecondS<10) {
					$color = "yellow";
					$decision = "AJ";
				}
				$table .= "<td class='moyenne $color'>$moyenneUESecondS</td><td class='validation'>$decision</td>";

				$moyenneC = ($moyenneUEPremierS + $moyenneUESecondS)/2;
				$color = "";
				$decision = "VAL";
				if ($moyenneC<10) {
					$color = "red";
					$decision = "AJ";
					$cptUnitesAJ++;
				}

				$table .= "<td class='moyenne $color'>$moyenneC</td><td class='validation $color'>$decision</td>";

				$cpt++;

			}
			$color ="";
			if ($cptUnitesAJ>0)
				$color = "yellow";
			if ($cptUnitesAJ>1)
				$color = "orange";
			if ($cptUnitesAJ>2)
				$color = "red";
			$table .= "<td class='nbUEAJ $color'>$cptUnitesAJ</td></tr>";

		}

		$table .= "
			</tbody>
		</table>
		";

		echo $table;

	}

	function printTableForUnitesAJ($idPromotion, $idSemestre) {
		$promotionDAO = new PromotionDAO();
		$userDAO = new UserDAO();
		$noteDAO = new NoteDAO();
		$uniteDAO = new UniteDAO();
		$promotionDAO = new PromotionDAO();
		$semestreDAO = new SemestreDAO();

		// on récupère les promotions
		$promos = $promotionDAO->getPromotions();

		// on récupère les semestres
		$semestres = $semestreDAO->getSemestres();

		// on récupère les étudiants associés à la promotion choisie et les unités associées au semestre
		$users = $userDAO->getUsersFromPromo($idPromotion);
		$unites = $uniteDAO->getUnitesFromSemestre($idSemestre);

		$table = "
		<table id='gestionssd' border = '1'>
			<tbody>
				<tr>
					<td>" . $idPromotion . " - " . $idSemestre . "</td><td>Promotion</td><td>Unité(s) < 10</td>
				</tr>
		";
		// tableau qui enregistre les couleurs desquelles les étudiants doivent être surlignés par rapport au nombre d'unité dont la moyenne<10

		foreach ($users as $user) { // pour chaque étudiant
			
			$cptUniteAj = 0; // on crée un compteur d'unités <10
			
			foreach ($unites as $unite) { // pour chaque unité de l'étudiant
				if ($noteDAO->getAverageMarkFromUserAndUE($user->mailUser, $unite->idUE)<10) { // si la moyenne est inférieure à 10
					$cptUniteAj ++; // on incrément le compteur
				}
			}
			
			$color = ''; // on crée une variable color qui est vide au départ, pour que si un étudiant n'a pas d'unités<10, la case reste 
			switch ($cptUniteAj) { 
				case (1): // s'il y a une unité ajournée
				$color = 'yellowsd'; // couleur = jaune
				break;
				case (2): // s'il y en a 2
				$color = 'orangesd'; // couleur = orange
				break;
			}
			if ($cptUniteAj>2) // s'il y en a plus de 2
				$color = 'redsd'; // couleur = rouge

			$table .= "<tr class='$color'>"; // on crée une ligne avec un identifiant couleur
			$table .= "<td>" . $user->pnomUser ." " . $user->nomUser . "</td><td>" . $idPromotion . "</td>"; // on met le prénom, le nom et la promo de l'étudiant
			
			
			$table .= "<td>" . $cptUniteAj . "</td></tr>";
			
		}

		echo $table; // on affiche le tableau
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

	function printSemestres() {
		$semestreDAO = new SemestreDAO();

		$semestres = $semestreDAO->getSemestres();

		$boutonsSemestre =
		"
		<div id='semsd'>
		";
		foreach($semestres as $semestre)
			$boutonsSemestre .= "<input type='submit' onclick='changeSemestreSD(this)'  id='".$semestre->idSemestre."sd' value='$semestre->idSemestre'>";

		echo $boutonsSemestre  . "</div>";
	}

	function printTableForMoyennes($idPromo, $idSemestre) {
		
		$uniteDAO = new UniteDAO();
		$userDAO = new UserDAO();
		$noteDAO = new NoteDAO();
		
		// on récupère les unités du semestre concerné
		$unites = $uniteDAO->getUnitesFromSemestre($idSemestre);

		// on récupère tous les étudiants de la promo choisie
		$users = $userDAO->getUsersFromPromo($idPromo);

		$table = "
		<table border=1 id='moyennesd'>
			<tbody>
				<tr><td> $idPromo - $idSemestre </td>
		";

		foreach ($unites as $unite) {
			$table .= "<td class='unitesd' id='top".substr($unite->idUE, 4, 1)."st'>" . $unite->idUE . "</td>";
		}

		$table .= "</tr>";

		foreach ($users as $user) {
			$table .= "<tr><td>" . $user->pnomUser . " " . $user->nomUser . "</td>";
			foreach ($unites as $unite) {
				$note = $noteDAO->getAverageMarkFromUserAndUE($user->mailUser, $unite->idUE);
				$class = "";
				if ($note<10) {
					$class = "redsd";
				}
				$table .= "<td class = 'notesd $class'>" . $note . "</td>";
			}
			$table .= "</tr>";
		}

		$table .= "
			</tbody>
		</table>
		";

		echo $table;

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
