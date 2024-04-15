<?php

session_start();

include_once('../DAO/UniteDAO.php');
include_once('../DAO/RessourceDAO.php');
include_once('../DAO/CoefficientDAO.php');
include_once('../DAO/NoteDAO.php');
include_once('../DAO/PromotionDAO.php');
include_once('../DAO/SemestreDAO.php');

class StudentController {

	public function printTable($idSemestre) {

		// instanciation des DAOs nécessaires
		$uniteDAO = new UniteDAO();
		$ressourceDAO = new RessourceDAO();
		$coefficientDAO = new CoefficientDAO();
		$noteDAO = new NoteDAO();

		// on récupère l'adresse mail de l'étudiant connecté
		$mailUser = $_SESSION['mailUser'];

		// on débute le tableau en inscrivant le numéro de semestre en haut à gauche
		$table = "
		<table border='1' cellpadding='5' id='tableaust' class='tableauFix'>
			<tbody>
				<tr>
					<td rowspan = '2' class='semestrest fixedC'>$idSemestre</td>
		";

		// on récupère toutes les unités du semestre de l'étudiant
		$unites = $uniteDAO->getUnitesFromSemestre($idSemestre);
		// on récupère toutes les ressources du semestre de l'étudiant 
		$ressources = $ressourceDAO->getRessourcesFromSemestre($idSemestre);

		$indexOfUnites = array();
		foreach ($unites as $unite) { // on met chaque unité dans le tableau
			$table.="<td colspan = '2' class='unitest fixed' id='top".substr($unite->idUE, 4, 1)."st'>$unite->idUE</td>";
			array_push($indexOfUnites, $unite->idUE);
		}
		$table.="</tr><tr>";

		foreach ($unites as $unite)  // en dessous de chaque unité on affiche COEFF et NOTE
			$table.="<td class='coefst fixed'>COEFF</td><td class='notest fixed'>NOTE</td>";
		$table.= "</tr>";

		// on crée un tableau contenant les UE dont la moyenne n'est pas définitive, i.e. toutes les notes de l'UE n'ont pas été saisies.
		$ueNotDef = array();
		foreach($ressources as $ressource) {
			$cpt = 0;  // compteur servant à décaler les coeffs et notes dans le tableau si nécessaire
			$table.="<tr><td class = 'ressourcest fixed'>$ressource->idRessource - $ressource->nomRessource</td>";

			// on récupère les coeffs du semestre courant d'une ressource
			$coeffs = $coefficientDAO->getCoeffFromRessource($ressource->idRessource);
			foreach ($coeffs as $coeff) {
				while ($coeff->idUE != $indexOfUnites[$cpt]) { // tant que le coeff n'est pas dans la bonne colonne (ex : on veut
																   // mettre un coeff de l'UE1.2 dans la 1ère colonne)
					$table.="<td class = 'emptyst'></td><td class='emptyst'></td>";  // on remplit la colonne de vide
					$cpt++;  // on incrémente le compteur
				}
				$table.= "<td class='coefst'>$coeff->coefRessource</td>"; // on insère le coeff dans le tableau
				// on récupère la note associé au semestre, à l'unité et à la ressource
				$noteEtu = $noteDAO->getNoteFromUserAndUEAndRessource($_SESSION['mailUser'], $coeff->idUE, $coeff->idRessource);
				if ($noteEtu == "--") { // si la note == '--', i.e. s'il n'y a pas de note renseignée
					if (!in_array($coeff->idUE, $ueNotDef)) { // si l'UE n'a pas déjà été entrée dans le tableau
						array_push($ueNotDef, $coeff->idUE); // on l'ajoute dans le tableau des UE non définitives
					}
				}
				$table .= "<td class='notest'>$noteEtu</td>"; // on ajoute dans le tableau la note
				$cpt++; // on incrémente le compteur (on décale d'une colonne)
			}
			$table .= "</tr>";
		}

		// on insère une ligne pour noter "Moyenne"
		$table .="<tr height = '10vh'></tr><tr><td>Moyenne</td>";
		foreach($unites as $unite) {
			// on récupère la moyenne de l'étudiant pour une unité
			$moyenne = $noteDAO->getAverageMarkFromUserAndUE($_SESSION['mailUser'], $unite->idUE);
			// on la met dans le tableau
			$colorM = '';
			if ($moyenne < 8)
				$colorM = 'redsd';	
			else if ($moyenne < 10)
				$colorM = 'orangesd';
			else if ($moyenne < 11)
				$colorM = 'yellowsd';
			else {
				$colorM = '';
			}
			$table.= "<td class='notest moyennest " . $colorM . "' id='".$unite->idUE."st' colspan='2' ";
			if (in_array($unite->idUE, $ueNotDef)) {
				$table .= "style = 'border-width:1px'";
			}
			$table .= " >$moyenne</td>";
		}
		$table .="
				</tr>
			</tbody>
		</table>
		";
		echo $table; // on affiche enfin le tableau
	}

	public function printMenu() {
		$semestreDAO = new SemestreDAO();
		$menu = 
		"
		<div id='semst'>
		";
		$semestres = $semestreDAO->getSemestres();
		foreach ($semestres as $semestre)
			$menu.="<input type='submit' onclick='changeSemesterEtu(this)' id='".$semestre->idSemestre."st' value='$semestre->idSemestre'>";
		$menu.=
		"</div>";

		echo $menu;
	}

	public function getSemestreCourant($mailUser) {
		$promotionDAO = new PromotionDAO();
		return $promotionDAO->getSemestreCourant($mailUser);
	}

}

?>