<?php

include_once('../Controller/StudentController.php');

if (isset($_SESSION['mailUser'])) { //contrat avec le moi du futur

	echo "
	<div id='globalst'>
	<div id='topst'></div>
	<div id='LogoIUTst'>
	<img id='imgIUTst' src='../Image/IUTlaval.png' alt='IUT Laval' title='IUT Laval'>
	</div>
	
	<div id='txtTopst'>
	<h1><span>" . $_SESSION['pnomUser'] . " " . strtoupper($_SESSION['nomUser']) . "</span></h1>
	</div>
	<div id='decost'>
	<img src='../Image/log-out.png' onclick='myFunction()' id='decoImg'/>
	</div>
	<div id='nomUserst'>
	<p></p>
	</div>
	
	";


} else {
	echo "
	<a href = 'login.php'>Connexion</a><br>
	";
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="../Image/logo-iut"/>
	<link rel="stylesheet" href="../style.css">
	<title>Accueil</title>

	<script>

		function myFunction() {
			var r = confirm("Souhaitez-vous vraiment vous déconnecter ?");
			if (r) {
				window.location = '../Other/logout.php';
			}
		}

		function changeSemesterEtu(semestre) {
			let i = 1;
			var elem = document.getElementById('S'+i.toString()+'st');
			for ( i; i < 7; i++) {
				elem = document.getElementById('S'+i.toString()+'st');
				elem.style.background = '#F2B6DD';
				elem.style.width = '97.6px';
				elem.style.height = '44px';
				elem.style.color = '#263F73';
			}
			semestre.style.background = '#263F73';
			semestre.style.width = '18%';
			semestre.style.height = '100%';
			semestre.style.color = '#F2B6DD';
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("tabst").innerHTML = this.responseText;
				}
			};
			xmlhttp.open("GET", "../Other/ajax.php?semestreCliqueEtu=" + semestre.value, true);
			xmlhttp.send();
		}

	</script>

</head>

<body>
	<?php 
	$studentController = new StudentController();
	$studentController->printMenu();
	echo "
	<script>
		var semestre = document.getElementById('" . $studentController->getSemestreCourant($_SESSION['mailUser']) . "st');
		semestre.style.background = '#263F73';
		semestre.style.width = '18%';
		semestre.style.height = '100%';
		semestre.style.color = '#F2B6DD';
	</script>
	";
	?>
	<div id='tabst'>
		<?php
		$studentController->printTable($studentController->getSemestreCourant($_SESSION['mailUser']));
		?>
	</div>
</body>

<style  type="text/css">

body
{
	font-size:20px;
	color: #263F73;
	margin:0; 
	padding: 0;
	font-family: "Roboto", sans-serif;
}

p
{
	font-size: 30px;
}

h1
{
	font-size: 45px;
	line-height: 150%;
}

span
{
	font-size: 2.5vw;
	line-height: 150%;
}

#yellowsd {
    background: yellow;
}

#orangesd {
    background-color: orange;
}

#redsd {
    background-color: red;
}

</style>
</html>