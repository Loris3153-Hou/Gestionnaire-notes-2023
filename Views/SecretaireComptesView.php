<?php
    session_start();
    require '../Controller/SecretaireComptesController.php';
    $scc = new SecretaireComptesController();

    $_SESSION['promo'] = $scc->getFirstPromo();
?>

<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../style.css">
        <link rel="icon" type="image/png" href="../Image/logo-iut"/>
        <title>Espace secrétariat : Gestion des comptes</title>
    </head>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script type="text/javascript">

        //Fonctions générales

        function montrerBoutonsPromo(){
            boutonsPromos = document.getElementsByClassName("promoBoutons");
            for (const value of boutonsPromos) {
                value.style.visibility = 'visible';
            }
        }

        function cacherBoutonsPromo(){
            boutonsPromos = document.getElementsByClassName("promoBoutons");
            for (const value of boutonsPromos) {
                value.style.visibility = 'hidden';
            }
        }

        function montrerBoutonsDeplacerSupprimer(){
            boutonsGestionParLot = document.getElementsByClassName("gestionParLotBoutons");
            for (const value of boutonsGestionParLot) {
                value.style.visibility = 'visible';
            }
        }

        function cacherBoutonsDeplacerSupprimer(){
            boutonsGestionParLot = document.getElementsByClassName("gestionParLotBoutons");
            for (const value of boutonsGestionParLot) {
                value.style.visibility = 'hidden';
            }
        }

        function selected(){
            selection = false;
            elementsListe = document.getElementsByClassName("elementListe");
            for (const value of elementsListe) {
                if (value.style.backgroundColor == 'grey'){
                    selection = true;
                }
            }
            return selection;
        }

        function etusSelected(){
            etus = [];
            elementsListe = document.getElementsByClassName("elementListe");
            for (const value of elementsListe) {
                if (value.style.backgroundColor == 'grey'){
                    etus.push(value.id);
                }
            }
            console.log(etus);
            return etus;
        } 

        function refresh(){
            window.location = "SecretaireComptesView.php";
        } 

        $(document).ready(function(){
            $("select").change(function(){
                $(this).find("option:selected").each(function(){
                    if ($(this).attr("class") === "roleOption"){
                        var val = $(this).attr("value");
                        if(val === "1"){
                            $("#promoSelector").show();
                        } else{
                            $("#promoSelector").hide();
                        }
                    }
                });
            }).change();
        });

        $(function() {
            $('tr').css('cursor', 'pointer')
                .click(function() {
                    if ($(this).attr("class") === "elementListe"){
                        if (this.style.backgroundColor == 'grey'){
                            this.style.backgroundColor = 'white'
                        }
                        else{
                            this.style.backgroundColor = 'grey';
                        }
                    }
                    if (selected()){
                        montrerBoutonsDeplacerSupprimer();
                    }
                    else{
                        cacherBoutonsDeplacerSupprimer();
                    }
                });
        });

        //Fonctions liées à l'ajout des comptes :

        let popupAjoutCompte;
        let popupConfirmAjoutCompte
        document.addEventListener("click", (e) => {
            if (e.target.className === "ajoutCompte") {			// Si bouton "Ajouter un compte" cliqué : ouvrir popupAjoutCompte
                popupAjoutCompte = document.getElementById(e.target.dataset.id);
                openModal(popupAjoutCompte);
            }

            else if (e.target.className === "annulerPopupAjoutCompte") {
                closeModal(popupAjoutCompte);
            }

            else if (e.target.className === "annulerPopupConfirmAjoutCompte") {
                closeModal(popupConfirmAjoutCompte);
            }

            else if (e.target.className === "modCompte") {
            }

            else {
                return;
            }
        });


        isOpenPopupConfAjout = false;
        function affichePopupConfirmAjoutCompte() {
            popupConfirmAjoutCompte = document.getElementById('popupComfirmAjoutCompte');
            openModal(popupConfirmAjoutCompte);
            isOpenPopupConfAjout = true;
        }

        function fermerPopupConfirmAjoutCompte() {
            if (isOpenPopupConfAjout == true){
                closeModal(popupConfirmAjoutCompte);
            }
        }

        //Fonctions constantes liés aux popups
        const openModal = (popup) => {
            document.body.style.overflow = "hidden";
            popup.setAttribute("open", true);
            document.addEventListener("keydown", escClose);
            let overlay = document.createElement("div");
            overlay.id = "modal-overlay";
            document.body.appendChild(overlay);
        };

        const closeModal = (popup) => {
            document.body.style.overflow = "auto";
            popup.removeAttribute("open");
            document.removeEventListener("keydown", escClose);
            document.body.removeChild(document.getElementById("modal-overlay"));
        };

        const escClose = (e) => {
            if (e.keyCode == 27) {
                closeModal();
            }
        };

        /*$(function() {

            $("#confirmAjoutButton").click(function() {

                var mail = $("#mailFormAjoutCompte").val();
                var nom = $("#nomFormAjoutCompte").val();
                var pnom = $("#pnomFormAjoutCompte").val();
                var role = $("#roleFormAjoutCompte").val();
                $.ajax( {
                    type: "GET",
                    url: 'ajoutCompteController.php',
                    data: { mail:mail, nom:nom, pnom:pnom, role:role },
                    success: function( response ) {
                        $('#test').html( response ); //Affichage de l'url cible, ici ajoutCompteController.php, dans une DIV
                        //console.log( response );
                    },
                    error: function( response ) {
                        $('#test').text('Erreur pour poster le formulaire : '+ response.status + " " + response.statusText);
                        //console.log( response );
                    }
                } );
                //$('#test').load('ajoutCompteController.php', { mail:mail, nom:nom, pnom:pnom, role:role }, function( response ) { });
                //https://analyse-innovation-solution.fr/publication/fr/jquery/les-requetes-ajax-avec-jquery
                //https://www.tutos.eu/3730
            } );
        } );*/

        //Fonctions liées à la suppression et la modification des comptes :

        let popupSuppressionCompte;
        let popupSuppressionComptes;
        let popupModificationCompte;
        document.addEventListener("click", (e) => {
            if (e.target.id === "supprCompte") {
                window.location.href = "SecretaireComptesView.php?var1=" + e.target.className;
            }

            if (e.target.id === "modifCompte") {
                window.location.href = "SecretaireComptesView.php?var2=" + e.target.className;
                /*var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {};
                xmlhttp.open("GET", "SecretaireComptesView.php?var2=" + e.target.className, true);
                xmlhttp.send();*/
            }

            if (e.target.className === "annuler") {
                closeModal(document.getElementById(e.target.dataset.id));
            }
        });

        function openPopupSuppressionCompte(){
            popupSuppressionCompte = document.getElementById('popupSuppressionCompte');
            openModal(popupSuppressionCompte);
        }

        var isOpenPopupSuppr = false;
        function openPopupSuppressionComptes(){
            popupSuppressionComptes = document.getElementById('popupSuppressionComptes');
            openModal(popupSuppressionComptes);
            isOpenPopupSuppr = true;
        }

        function supprCompte(user){
            supprCompteFunction(user);
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                document.getElementById("list1").innerHTML = this.responseText;
            };
            xmlhttp.open("GET", "../Other/ajax.php?refresh=" + "", true);
            xmlhttp.send();
            if (!isOpenPopupSuppr){
                closeModal(popupSuppressionCompte);
                isOpenPopupSuppr = false;
            }
        }

        function supprCompteFunction(user){
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {};
            xmlhttp.open("GET", "../Other/ajax.php?suppr=" + user, true);
            xmlhttp.send();
        }

        function supprComptes(){
            elementsListe = document.getElementsByClassName("elementListe");
            for (const value of elementsListe) {
                if (value.style.backgroundColor == 'grey'){
                    supprCompteFunction(value.id);
                }
            }
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                document.getElementById("list1").innerHTML = this.responseText;
            };
            xmlhttp.open("GET", "../Other/ajax.php?refresh=" + "", true);
            xmlhttp.send();
            closeModal(popupSuppressionComptes);
            //window.location.reload();
        }

        function openPopupModificationCompte(){
            popupModificationCompte = document.getElementById('popupModificationCompte');
            openModal(popupModificationCompte);
        }

        function closePopupModificationCompte(){
            closeModal(popupModificationCompte);
        }

        let popupValidModCompte;
        function openPopupValidModCompte(){
            popupValidModCompte = document.getElementById('popupValidModCompte');
            openModal(popupValidModCompte);
        }

        function closePopupValidModCompte(){
            closeModal(popupValidModCompte);
        }

        let popupChangePromo;
        function openPopupChangePromo(){
            popupChangePromo = document.getElementById('popupChangePromo');
            creerListEtusSelected();
            openModal(popupChangePromo);
        }

        function creerListEtusSelected(){
            listEtusSelected = etusSelected();
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {};
            xmlhttp.open("GET", "../Other/ajax.php?listEtus=" + listEtusSelected, true);
            xmlhttp.send();
        }

        function deplacerEtus(promo){
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                document.getElementById("list1").innerHTML = this.responseText;
            };
            xmlhttp.open("GET", "../Other/ajax.php?changePromo=" + promo + "&refresh=" + "", true);
            xmlhttp.send();
        }

    </script>

    <body>

        
        <div id='globalsd'>
		<div id='topsd'></div>
		<div id='LogoIUTsd'>
		<img id='imgIUTsd' src='../Image/IUTlaval.png' alt='IUT Laval' title='IUT Laval'>
		</div>
	
		<div id='txtTopsd'>
		<h1><span><?php echo $_SESSION['pnomUser'] . " " . mb_strtoupper($_SESSION['nomUser']); ?></span></h1>
		</div>
        <div id='setting'>
		<a href='../Other/paramBDD.php'><img src='../Image/settings.png' id='set'/></a>
		</div>
		<div id='decosd'>
		<img src='../Image/log-out.png' onclick='myFunction()' id='decoImg'/>
		</div>
        

        <?php

            //Affichage général de la page

            if(isset($_GET['allUsers'])){
                $_SESSION['visionTriCourant'] = 'allUsers';
            }

            else if(isset($_GET['etus'])){
                $_SESSION['visionTriCourant'] = 'etus';
            }

            else if(isset($_GET['perso'])){
                $_SESSION['visionTriCourant'] = 'perso';
            }

            else if(isset($_GET['triPromo'])){
                $_SESSION['visionTriCourant'] = 'triPromo';
                $_SESSION['triPromo'] = $_GET['triPromo'];
            }

            if(isset($_SESSION['visionTriCourant'])){
                if ($_SESSION['visionTriCourant'] == 'etus'){
                    $html = "<div id='list1'>";
                    $html .= $scc->afficherLesUtilisateursParRole(array('Etudiant'));
                    $html .= "</div>";
                    echo $html;
                    echo '<script type="text/javascript">window.onload = function() {
                    montrerBoutonsPromo();
                    };</script>';
                }
                else if ($_SESSION['visionTriCourant'] == 'triPromo' && isset($_SESSION['triPromo'])){
                    $html = "<div id='list1'>";
                    $html .= $scc->afficherLesEtudiantsParPromotion($_SESSION['triPromo']);
                    $html .= "</div>";
                    echo $html;
                    echo '<script type="text/javascript">window.onload = function() {
                    montrerBoutonsPromo();
                    };</script>';
                }
                else if ($_SESSION['visionTriCourant'] == 'perso'){
                    $html = "<div id='list1'>";
                    $html .= $scc->afficherLesUtilisateursParRole(array('Enseignant','Directeur des études'));
                    $html .= "</div>";
                    echo $html;
                    echo '<script type="text/javascript">window.onload = function() {
                    cacherBoutonsPromo();
                    };</script>';
                }
                else {
                    $html = "<div id='list1'>";
                    $html .= $scc->afficherLesUtilisateursParRole(array('Etudiant','Enseignant','Directeur des études'));
                    $html .= "</div>";
                    echo $html;
                    echo '<script type="text/javascript">window.onload = function() {
                    cacherBoutonsPromo();
                    };</script>';
                }
            }
            else {
                $html = "<div id='list1'>";
                $html .= $scc->afficherLesUtilisateursParRole(array('Etudiant','Enseignant','Directeur des études'));
                $html .= "</div>";
                echo $html;
                echo '<script type="text/javascript">window.onload = function() {
                    cacherBoutonsPromo();
                    };</script>';
            }

            //Gestion des ajouts de comptes

            if(isset($_GET['subFormAjoutCompte'])) {
                echo '<script type="text/javascript">window.onload = function() {
                    affichePopupConfirmAjoutCompte();
                    };</script>';
            }

            if(isset($_POST['subFormConfirmAjoutCompte'])) {
                $scc->ajouterUnCompte($_GET['nom'], $_GET['pnom'], $_GET['mail'], $_GET['role'], $_GET['promo']);
                echo '<script type="text/javascript">window.onload = function() {
                    fermerPopupConfirmAjoutCompte();
                    };</script>';
                $scc->refresh();
            }

            //Gestion suppression de comptes

            if (isset($_GET["var1"])) {
                echo '<script type="text/javascript">window.onload = function() {
                    openPopupSuppressionCompte();
                    };</script>';
            }

            //Gestion modification de comptes

            if (isset($_GET["var2"])) {
                echo '<script type="text/javascript">window.onload = function() {
                        openPopupModificationCompte();
                        };</script>';
            }

            if (isset($_GET['subFormModifCompte'])){
                echo '<script type="text/javascript">window.onload = function() {
                        openPopupValidModCompte();
                        closePopupModificationCompte();
                        };</script>';
            }

            if (isset($_POST['subValidModCompte'])){
                $scc->modifierCompte($_SESSION['userSelectionne']);
                echo '<script type="text/javascript">window.onload = function() {
                        closePopupValidModCompte();
                        };</script>';
                        $scc->refresh();
            }

            if (isset($_POST['subFormDeplacement'])){
                echo '<script type="text/javascript">window.onload = function() {
                        deplacerEtus("'.$_POST["changePromo"].'");
                        };</script>';
            }
        ?>

        <!--Affichage général de la page-->

        <div id='ongletsd'>
        <button class="ajoutCompte" id='addCompte' data-id="popupAjoutCompte">Ajouter un compte</button>
        <a href='SecretaireNotesView.php'><input id='retnotes' type='submit' value='Saisie des notes'></a>
        <a href='SecretaryView.php'><input id='retnotes' type='submit' value='Enseignants/Ressources'></a>
        <a href='SecretaryViewPromo.php'><input id='retnotes' type='submit' value='Promo/Semestre'></a>
        </div>

        <form method="get" id='users'>
        
            <input type="submit" id="allUsers" name="allUsers" value="Tous les utilisateurs"><br>
            <input type="submit" id="etus" name="etus" value="Etudiants" >
            <input type="submit" id="perso" name="perso" value="Personnel">
        </form>
         <form method="get" id='promosd'>
            <?php
            foreach ($scc->getAllPromos() as $promo){
                echo '<input class="promoBoutons" onclick="changePromo(' . $promo->idPromo.')" id="'.$promo->idPromo.'" type="submit" name="triPromo" value="'.$promo->idPromo.'" style="visibility : hidden">';
            }
            ?>
            </form>
            <br>
        </form>
        <div id ="gestEtu">
        <button id='envTo' class="gestionParLotBoutons" style="visibility : hidden" onclick="openPopupChangePromo()">Envoyer vers</button><br>
        <button id='suppL' class="gestionParLotBoutons" style="visibility : hidden" onclick="openPopupSuppressionComptes()">Supprimer</button>
        </div>

        <!--Popups ajout de comptes-->

        <div id="popupAjoutCompte" class="popup" role="dialog" tabindex="-1">
            <div class="model-inner speLogf">
                <div class="popup-header">
                    <h2>Ajouter un compte</h2>
                </div>
                <form method="get" class='speLogf'>
                    Identifiant (Mail universitaire) : <input type="text" name="mail" id="mailFormAjoutCompte" required> <br><br>
                    Nom : <input type="text" name="nom" id="nomFormAjoutCompte" required> <br><br>
                    Prénom : <input type="text" name="pnom" id="pnomFormAjoutCompte" required> <br><br>
                    Rôle : <select class='butPop' name="role" id="roleFormAjoutCompte" required>
                        <option class="roleOption" value="1">Etudiant</option>
                        <option class="roleOption" value="2">Enseignant</option>
                        <option class="roleOption" value="3">Directeur des études</option>
                    </select> <br><br>
                    <div id="promoSelector">Promotion : <select class='butPop' name="promo" id="promoFormAjoutCompte">
                        <option value="Non définie">Non définie</option>
                        <?php
                            foreach ($scc->getAllPromos() as $promo){
                                echo '<option value="'.$promo->idPromo.'">'.$promo->idPromo.'</option>';
                            }
                        ?>
                        </select> <br><br></div>
                    <input class='butPop' type="submit" name="subFormAjoutCompte" value="Valider" data-id='popupComfirmAjoutCompte'>
                </form>
                <button class="annulerPopupAjoutCompte" id='butAnAd'>Annuler</button>
            </div>
        </div>

        <div id="popupComfirmAjoutCompte" class="popup" role="dialog" tabindex="-1">
            <div class="model-inner speLogf">
                <div class="popup-header speLogf">
                    <h2>Êtes-vous sûr de vouloir ajouter ce compte ?</h2>
                </div>
                <?php if(isset($_GET['subFormAjoutCompte'])) {
                    echo 'Identifiant : '.$_GET['mail'].'<br>';
                    echo 'Nom : '.$_GET['nom'].'<br>';
                    echo 'Prénom : '.$_GET['pnom'].'<br>';
                    switch ($_GET['role']) {
                        case '1':
                            $role = 'Etudiant';
                            break;
                        case '2';
                            $role = 'Enseignant';
                            break;
                        case '3':
                            $role = 'Directeur des études';
                            break;}
                    echo 'Rôle : '.$role.'<br>';
                    if ($role == 'Etudiant'){
                        echo 'Promotion : '.$_GET['promo'].'<br>';
                    }
                }?>
                <form method="post"><input class='butPop' type="submit" name="subFormConfirmAjoutCompte" value="Confirmer"></form>
                <button class="annulerPopupConfirmAjoutCompte" id='butAnAdC'>Annuler</button>
            </div>
        </div>

        <!--Popups suppression de comptes-->

        <div id="popupSuppressionCompte" class="popup" role="dialog" tabindex="-1">
            <div class="model-inner speLogf">
                <div class="popup-header speLogf">
                    <h3>Êtes-vous sûr de vouloir supprimer cet utilisateur ainsi que toutes les informations qui le concernent ?</h3>
                    <?php

                        //echo '<script type="text/javascript">document.write(variable_js_test);</script>';

                        if (isset($_GET["var1"])) {
                            $idUserSectionne = $_GET["var1"];
                            $userSelectionne = $scc->returnUserSelectionne($idUserSectionne);
                            echo $userSelectionne->nomUser;
                            echo $userSelectionne->nomUser."<br>";
                            echo '<button class="confirmSuppressionCompte butPop" onclick=\'supprCompte("'.$idUserSectionne.'")\'>Confirmer</button>';
                        }

                    ?>

                    <button data-id="popupSuppressionCompte" class="annuler" id="butAnSup1">Annuler</button>
                </div>
            </div>
        </div>

        <div id="popupSuppressionComptes" class="popup" role="dialog" tabindex="-1">
            <div class="model-inner speLogf">
                <div class="popup-header speLogf">
                    <h3>Êtes-vous sûr de vouloir supprimer les utilisateurs séléctionnés ainsi que toutes les informations qui les concernent ?</h3>
                    <button class="confirmSuppressionComptes" onclick="supprComptes()">Confirmer</button>
                    <button data-id="popupSuppressionComptes" class="annuler" id="butAnSup">Annuler</button>
                </div>
            </div>
        </div>

        <!--Popups modification de comptes-->

        <div id="popupModificationCompte" class="popup" role="dialog" tabindex="-1">
            <div class="model-inner speLogf">
                <div class="popup-header speLogf">
                    <h2>Modification de compte :</h2>
                    <?php

                    if (isset($_GET["var2"])) {
                        $idUserSectionne = $_GET["var2"];
                        $_SESSION['userSelectionne'] = $idUserSectionne;
                        $userSelectionne = $scc->returnUserSelectionne($idUserSectionne);
                        $listRolesHorsRoleCourant = $scc->getListRolesSaufRoleCourant($userSelectionne->role);
                        echo "<form method ='get' class='speLogf'>";
                        echo "Identifiant (Mail universitaire) : <input type='text' name='mailUserMod' value='".$idUserSectionne."' required><br>";
                        echo "Nom : <input type='text' name='nomUserMod' value='".$userSelectionne->nomUser."' required><br>";
                        echo "Prénom : <input type='text' name='pnomUserMod' value='".$userSelectionne->pnomUser."' required><br>";
                        echo 'Rôle : <select class="butPop" name="roleUserMod" id="roleFormModifCompte">
                                    <option value="'.$scc->getIdRoleParNom($userSelectionne->role).'">'.$userSelectionne->role.'</option>
                                    <option value="'.$listRolesHorsRoleCourant[0]->idRole.'">'.$listRolesHorsRoleCourant[0]->nomRole.'</option>
                                    <option value="'.$listRolesHorsRoleCourant[1]->idRole.'">'.$listRolesHorsRoleCourant[1]->nomRole.'</option>
                              </select><br>';
                        echo "<input type='submit' name='subFormModifCompte' value='Valider' class='confirmModificationCompte butPop'></form>"; //onclick=\"modifCompte('".$idUserSectionne."')\"
                    }

                    ?>

                    <button data-id="popupModificationCompte" id="butAnMod" class="annuler" >Annuler</button>
                </div>
            </div>
        </div>

        <div id="popupValidModCompte" class="popup" role="dialog" tabindex="-1">
            <div class="model-inner">
                <div class="popup-header">
                    <h3>Êtes-vous sûr de vouloir apporter ces modifications ?</h3>
                    <?php

                    if (isset($_GET['subFormModifCompte'])){
                        echo 'Identifiant : '.$_GET['mailUserMod'].'<br>';
                        echo 'Nom : '.$_GET['nomUserMod'].'<br>';
                        echo 'Prénom : '.$_GET['pnomUserMod'].'<br>';
                        switch ($_GET['roleUserMod']) {
                            case '1':
                                $role = 'Etudiant';
                                break;
                            case '2';
                                $role = 'Enseignant';
                                break;
                            case '3':
                                $role = 'Directeur des études';
                                break;}
                        echo 'Rôle : '.$role.'<br>';
                        echo '<form method="post"><input type="submit" name="subValidModCompte" value="Confirmer"></form>';
                    }

                    ?>

                    <button data-id="popupValidModCompte" class="annuler">Annuler</button>
                </div>
            </div>
        </div>

    <div id="popupChangePromo" class="popup" role="dialog" tabindex="-1">
        <div class="model-inner">
            <div class="popup-header">
                <h3>Déplacer les étudiants séléctionnés dans la promotion:</h3>
            </div>
            <form method="post">
                <?php
                foreach ($scc->getAllPromos() as $promo){
                    echo '<input type="radio" name="changePromo" value="'.$promo->idPromo.'">'.$promo->idPromo.'<br>';
                }
                ?>
                <input type="radio" name="changePromo" value="Non définie">Non définie<br>
            <input type="submit" name="subFormDeplacement" value="Confirmer"></form>
            <button data-id="popupChangePromo" class="annuler" >Annuler</button>
        </div>
    </div>

    </body>


    <style>
        
        h2{
            font-size:40px;
            margin-top:-10px;
        }
        #imgM, #imgM1{
            width:20px;
            height:20px;
            position: sticky;
            z-index:2;
        }

    </style>

</html>