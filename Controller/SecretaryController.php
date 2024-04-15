<?php

include_once('../DAO/EnseignementDAO.php');
include_once('../DAO/RessourceDAO.php');
include_once('../DAO/UniteDAO.php');
include_once('../DAO/UserDAO.php');
include_once('../DAO/SemestreDAO.php');
include_once('../DAO/PromotionDAO.php');
include_once('../DAO/NoteDAO.php');

class SecretaryController {
    function printTableForEnseignement($idSemestre) {
        $uniteDAO = new UniteDAO();
        $ressourceDAO = new RessourceDAO();
        $userDAO = new UserDAO();
        $enseignementDAO = new EnseignementDAO();

        $unites = $uniteDAO->getUnitesFromSemestre($idSemestre);

        $table = "
		<table border = '1' onmouseleave='allWhite();' class='tableauFix'>
			<tbody>
				<tr>
					<td rowspan='2' class = 'unitesd fixedC'>" . $idSemestre . "</td>";

	    // on ajoute les unités dans une ligne, chaque unité a une largeur correspondante au nombre de ressource qu'elle possède
	    foreach ($unites as $unite) {
	    	$table .= "<td class = 'unitesd fixed' id='top".substr($unite->idUE, 4, 1)."st' colspan = '" . $uniteDAO->getNbRessourcesFromUE($unite->idUE) . "'>$unite->idUE</td>";
	    }
	    $table .= "</tr><tr>";

	    // on ajoute les ressources en dessous des unités qui leurs sont associées
	    foreach ($unites as $unite) {
	    	$ressources = $ressourceDAO->getRessourcesFromUE($unite->idUE);
	    	foreach ($ressources as $ressource) {
		    	$table .= "<td class='ressource fixed' id='$unite->idUE$ressource->idRessource' >" . $ressource->idRessource . "</td>";
	    	}
	    }
	    $table .= "</tr>";

        $enseignants = $userDAO->getUsersFromRole('Enseignant');

        foreach ($enseignants as $enseignant) { // pour chaque étudiant
			// on commence par mettre son prénom et nom dans la colonne de gauche
			$table .= "<tr><td class='enseignant fixed' id='$enseignant->mailUser'>" . $enseignant->pnomUser . " " . $enseignant->nomUser . "</td>";
			foreach ($unites as $unite) {
				$ressources = $ressourceDAO->getRessourcesFromUE($unite->idUE);
				foreach ($ressources as $ressource) {
                    $table .= "<td onmouseover='colorTable(this, event);' onclick='ajouterOuSupp(this);' id='$enseignant->mailUser,$unite->idUE,$ressource->idRessource'>";
					if ($enseignementDAO->enseignementExists($enseignant->mailUser, $unite->idUE, $ressource->idRessource)) {
                        $table .= "X";
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

    function printSemestres() {
		$semestreDAO = new SemestreDAO();

		$semestres = $semestreDAO->getSemestres();

		$boutonsSemestre =
		"
		<div id='semsd'>
		";
		foreach($semestres as $semestre)
			$boutonsSemestre .= "<input type='submit' onclick='changeSemestreSe(this)'  id='$semestre->idSemestre' value='$semestre->idSemestre'>";

		echo $boutonsSemestre  . "</div>";
	}

    function updateEnseignement($mailUser, $idUE, $idRessource) {
        $enseignementDAO = new EnseignementDAO();
        $enseignementDAO->updateEnseignement($mailUser, $idUE, $idRessource);
    }

    function getFirstSemestre() {
		$semestreDAO = new SemestreDAO();

		$semestre = $semestreDAO->getFirstSemestre();

		return $semestre;
	}

	function getFirstPromo() {
		$promotionDAO = new PromotionDAO();

		$promotion = $promotionDAO->getFirstPromo();

		return $promotion;
	}

	function printTableForPromotion(){ //mathilde

        $semestreDAO = new SemestreDAO();
        $promoDAO = new PromotionDAO();

        $semestres = $semestreDAO->getSemestres();

        $table = "
		<table border = '1' onmouseleave='allWhite();' id='tableauSecretaire2'>
			<tbody>
				<tr>
					<td></td>";

        // on ajoute les unités dans une ligne, chaque unité a une largeur correspondante au nombre de ressource qu'elle possède
        foreach ($semestres as $semestre) {
            $table .= "<td class = 'semestreTab' id='".$semestre->idSemestre."bis'>$semestre->idSemestre</td>";
        }
        $table .= "</tr>";

        $promos = $promoDAO->getPromotions();

        foreach ($promos as $promo) { // pour chaque étudiant
            // on commence par mettre son prénom et nom dans la colonne de gauche
            $table .= "<tr><td class='promoTab' id='".$promo->idPromo."bis'>$promo->idPromo</td>";
            foreach ($semestres as $semestre) {
                $table .= "<td onmouseover='colorTable(this);' onclick='ajouterOuSupp(this);' id='$promo->idPromo,$semestre->idSemestre'>";
                if ($promo->idSemestre == $semestre->idSemestre) {
                    $table .= "X";
                }
                $table .= "</td>";
            }
            $table .= "</tr>";
        }
        $table .= "
			</tbody>
		</table>";

        echo $table;
	}
	
	function changeSemestreCourant($promo, $sem){
        $promoDao = new PromotionDAO();
        $promoDao->changeSemestreCourant($promo, $sem);
    }

}

?>