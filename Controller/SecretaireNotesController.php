<?php

include_once('../DAO/NoteDAO.php');
include_once('../DAO/SemestreDAO.php');
include_once('../DAO/RessourceDAO.php');
include_once('../DAO/UniteDAO.php');
include_once('../DAO/UserDAO.php');
include_once('../DAO/CoefficientDAO.php');
include_once('../DAO/PromotionDAO.php');

class SecretaireNotesController{

    public $promos;
    public $listSemestres = array();
    public $listUnites = array();
    public $listRessources = array();
    

    public function __construct()
    {
        $semDAO = new SemestreDAO();
        foreach ($semDAO->getSemestres() as $sem){
            array_push($this->listSemestres, $sem->idSemestre);
        }
        $resDAO = new RessourceDAO();
        $unitDAO = new UniteDAO();
        foreach ($this->listSemestres as $s){
            array_push($this->listRessources, $resDAO->getRessourcesFromSemestre($s));
            array_push($this->listUnites, $unitDAO->getUnitesFromSemestre($s));
        }
    }

    public function afficherPromos(){
        $html="
		<div id='promosd'>
		";
        $promoDAO = new PromotionDAO();
        $this->promos = $promoDAO->getPromotions();
        foreach ($this->promos as $p){
            
            $html .= "<input type='submit' onclick = 'changePromoS(" . $p->idPromo . ")' id='" . $p->idPromo . "' value='" . $p->idPromo . "' >";
        }
        
        return $html . "</div>";
    }

    public function afficherSemestes(){
        $html = "
		<div id='semsd'>
		";
        
        foreach ($this->listSemestres as $s){
            $html .= "<input type='submit' onclick = 'changeSemestreS(" . $s . ")' id='" . $s ."' value='$s'>";
            
        }
        
        return $html . "</div>";
    }

    private function definieResultatsParSemestresEtParPromotion($sem, $promo){
        $listResults = array();
        $listTotaux = array();
        $listRes = $this->getRessourcesParSemestre($sem);
        $listUnit = $this->getUnitesParSemestre($sem);
        foreach ($listRes as $r){
            $listResultsParRes = array();
            foreach ($listUnit as $u){
                $result = "V";
                $etus = new UserDAO();
                $listEtus = $etus->getEtudiantsParPromotion($promo);
                foreach ($listEtus as $etu){
                    $notesDAO = new NoteDAO();
                    $notes = $notesDAO->getNoteFromUserAndUEAndRessource($etu->mailUser, $u->idUE, $r->idRessource);
                    $coeffDAO = new CoefficientDAO();
                    if($coeffDAO->existsCoeff($r, $u)){
                        if($notes!="--"){
                            if($notes == null){
                                $result = "X";
                            }
                        }
                        else{
                            $result = "X";
                        }
                    }
                    else{
                        $result = "";
                    }
                }
                array_push($listResultsParRes, $result);
            }
            array_push($listResults, $listResultsParRes);
        }
        for($i = 0; $i < sizeof($listUnit); $i++){
            $total = "V";
            foreach($listResults as $listR){
                if($listR[$i] == "X"){
                    $total = "X";
                }
            }
            array_push($listTotaux, $total);
        }
        array_push($listResults, $listTotaux);
        return $listResults;
    }

    public function afficheTableau($sem, $promo) //affiche le tableau et créé le csv correspondant
    {
        $ligneUnitesCsv = array("");
        $lignesRessouresCsv = array();

        $unites = $this->getUnitesParSemestre($sem);
        $ressources = $this->getRessourcesParSemestre($sem);
        $resultats = $this->definieResultatsParSemestresEtParPromotion($sem, $promo);

        $table = "
		<table border='1' cellpadding='5' id='notessd' class='tableau tableauFix'>
		<tbody>
		<tr>
		<td class = 'ressource fixedC'> $promo - $sem</td>
		";

        foreach ($unites as $unite) {  // on met chaque unité dans le tableau
            $table.="<td class='unite fixed' id='top".substr($unite->idUE, 4, 1)."st'>".$unite->idUE."</td>";
            array_push($ligneUnitesCsv, $unite->idUE); //on ajoute les unites à la ligne pour le csv
        }

        // on crée un tableau contenant le statut de chaque couple Unité Ressource, i.e. si toutes les notes pour ce couple ont été saisies ou non.

        $indiceRessource = -1;
        foreach($ressources as $ressource) {
            $ligneRessourceCsv = array();

            $indiceRessource++;
            $cpt = 1;  // compteur servant à décaler les résultats(statuts) dans le tableau si nécessaire
            $table.="<tr><td class='fixed'>" . $ressource->idRessource . " - " . $ressource->nomRessource . "</td>";
            array_push($ligneRessourceCsv, $ressource->idRessource . " - " . $ressource->nomRessource); //on ajoute la ressource à la ligne pour le csv

            // on récupère les statuts du semestre courant d'une ressource
            $statuts = $resultats[$indiceRessource];
            foreach ($statuts as $statut) {
                $table.= "<td class='coefst'>" . $statut . "</td>"; // on insère le statut dans le tableau
                array_push($ligneRessourceCsv, $statut); //on ajoute les statuts à la ligne pour le csv
                $cpt++; // on incrémente le compteur (on décale d'une colonne)
            }
            $table .= "</tr>";
            array_push($lignesRessouresCsv, $ligneRessourceCsv);
        }
        $indiceRessource++;

        $ligneTotauxCsv = array("Total");

        //on affiche la lignes des totaux
        $table.="<tr><td class = 'totaux'>Total</td>";
        $statuts = $resultats[$indiceRessource];
        foreach ($statuts as $statut) {
            $table.= "<td class='coefst'>" . $statut . "</td>"; // on insère le statut dans le tableau
            array_push($ligneTotauxCsv, $statut); //on ajoute les statuts à la ligne pour le csv
            $cpt++; // on incrémente le compteur (on décale d'une colonne)
        }
        $table .= "</tr></tbody></table>";


        echo $table; //on affiche la tableau

        $this->exportDataToCsv($sem, $promo, $ligneUnitesCsv, $lignesRessouresCsv, $ligneTotauxCsv); //on créé le csv
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

        $ligneUnitesCsv = array("");
        $ligneRessouresCsv = array();
        $lignesEtudiantsCsv = array();

		// on commence le tableau bla bla bla
		$table = "
		<table id='notessd' border = '1' class='tableauFix'>
			<tbody>
				<tr>
					<td rowspan='2' class = 'unitesd fixedC'>" . $idPromotion . " - " . $idSemestre . "</td>";

		// on ajoute les unités dans une ligne, chaque unité a une largeur correspondante au nombre de ressource qu'elle possède
		foreach ($unites as $unite) {
			$table .= "<td class = 'unitesd fixed' id='top".substr($unite->idUE, 4, 1)."st' colspan = '" . $uniteDAO->getNbRessourcesFromUE($unite->idUE) . "'>$unite->idUE</td>";
            $nbRessourceParUnite = $uniteDAO->getNbRessourcesFromUE($unite->idUE);
            for ($i=0; $i<$nbRessourceParUnite; $i++){
                array_push($ligneUnitesCsv, $unite->idUE); //on ajoute les unites à la ligne pour le csv
            }
        }
		$table .= "</tr><tr>";

		// on ajoute les ressources en dessous des unités qui leurs sont associées
        array_push($ligneRessouresCsv, " ");
		foreach ($unites as $unite) {
			$ressources = $ressourceDAO->getRessourcesFromUE($unite->idUE);
            if (is_array($ressources) || is_object($ressources)){
                foreach ($ressources as $ressource) {
                    $table .= "<td class = 'ressource fixed' id='" . $unite->idUE . $ressource->idRessource . "'>" . $ressource->idRessource . "</td>";
                    array_push($ligneRessouresCsv, $ressource->idRessource);
                }
            }
			else{
                echo "il y a une erreur";
            }
		}
		$table .= "</tr>";

		// pour chaque étudiant, on va ajouter sa note par rapport à une unité et une ressource données
		foreach ($users as $user) { // pour chaque étudiant
            $ligneEtudiantsCsv = array();
			// on commence par mettre son prénom et nom dans la colonne de gauche
			$table .= "<tr><td class = 'etu fixed' id='" . $user->mailUser . "'>" . $user->pnomUser . " " . $user->nomUser . "</td>";
            array_push($ligneEtudiantsCsv, $user->pnomUser." ".$user->nomUser);
			foreach ($unites as $unite) {
				$ressources = $ressourceDAO->getRessourcesFromUE($unite->idUE);
				foreach ($ressources as $ressource) {
                    $note = $noteDAO->getNoteFromUserAndUEAndRessource($user->mailUser, $unite->idUE, $ressource->idRessource);
					$table .= "<td class ='notesd' id = '" . $user->mailUser . "," . $ressource->idRessource . "," . $unite->idUE . "' >" . $note . "</td>";
                    array_push($ligneEtudiantsCsv, $note);
				}
			}
			$table .= "</tr>";
            array_push($lignesEtudiantsCsv, $ligneEtudiantsCsv);
		}
        
		$table .= "
			</tbody>
		</table>";

		echo $table;

        $this->exportDataToCsv($idSemestre, $idPromotion, $ligneUnitesCsv, $ligneRessouresCsv, $lignesEtudiantsCsv); //on créé le csv
	}

    private function exportDataToCsv($sem, $promo, $lUnites, $lRessources, $lTotaux)
    {
        $delimiter = ';';

        $fileName = 'Bilan_saisie_des_notes_'.$promo.'_'.$sem.'.csv';

        $fp = fopen($fileName, 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($fp, $lUnites, $delimiter);
        if (is_array($lRessources[0]))
            foreach($lRessources as $lR){
                fputcsv($fp, $lR, $delimiter);
            }
        if (is_array($lTotaux[0]))
            foreach ($lTotaux as $lEtu) {
                fputcsv($fp, $lEtu, $delimiter);
            }

        fclose($fp);
        $path = "";
        if ($sem != $this->getFirstSemestre() || $promo != $this->getFirstPromo()) {
            $path="../Other/";
        }
        echo "<a href='$path".$fileName."' id='expCSV' target='_blank'>Exporter</a>";
    }

    private function rechercheIndiceSemestre($s){
        $indice = -1;
        foreach ($this->listSemestres as $sem){
            $indice++;
            if($s == $sem){
                return $indice;
            }
        }
    }

    private function getRessourcesParSemestre($s){
        return $this->listRessources[$this->rechercheIndiceSemestre($s)];
    }

    private function getUnitesParSemestre($s){
        return $this->listUnites[$this->rechercheIndiceSemestre($s)];
    }

    public function getPromos(){
        return $this->promos;
    }

    public function getlistSemestres(){
        return $this->listSemestres;
    }
    //AjoutMerge
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
}
?>