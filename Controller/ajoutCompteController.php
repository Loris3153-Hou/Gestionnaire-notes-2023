<?php

include('SecretaireComptesController.php');

$scc = new SecretaireComptesController();
$mail = $_GET['mail'];
$nom = $_GET['nom'];
$pnom = $_GET['pnom'];
$role = $_GET['role'];
$scc->ajouterUnCompte($nom, $pnom, $mail, $role);
echo 'finito';

?>
