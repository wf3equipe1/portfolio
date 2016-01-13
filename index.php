<?php//Page d'accueil du site
session_start();
require_once 'composants/db.php';
// Titre
// Photo de couverture
// Affichage des news
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Page principale</title>
 	<meta charset="utf-8">
 	<link rel="stylesheet" href="css/style.css">
 </head>
 <body>
	 <main>
	 	<?php include_once 'composants/menugauche.php'; //MENU DE GAUCHE?>

	 	<section id="rightSide">
	 		<header>
	 			<h1>Titre Portfolio</h1>

	 			<img src="" alt="couverture" id="cover">
	 		</header>
	 		<div id="newsIndex">
	 			<h1>Les news</h1>

	 		</div>
	 	</section>
	 </main>
 </body>
 </html>
