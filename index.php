<?php//Page d'accueil du site
session_start();
require_once'composants/db.php';
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
 	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
 </head>
 <body>
	 <main>
	 	<?php include_once 'composants/menugauche.php'; //MENU DE GAUCHE?>

	 	<section id="rightSide">
	 		<h1>Titre Portfolio</h1>
	 		<img src="images/cover_exemple.jpg" alt="couverture" id="cover">
	 		<div id="blocNews">
	 			<h2>Les news</h2>
				<article>
					<h3>titre1</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fuga maiores, placeat? Consequatur corporis possimus, consectetur ducimus veniam accusamus fugiat perspiciatis, sed nemo, nobis quam explicabo similique necessitatibus quasi ullam doloribus.</p>
				</article>
				<article>
					<h3>titre2</h3>
					<p><span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia atque maxime ipsam laudantium velit eligendi aut, qui commodi alias sed at ad eveniet aspernatur sapiente id, nobis voluptas distinctio quas!</span><span>Consectetur placeat recusandae ut consequuntur mollitia cupiditate dicta, ex at repellendus suscipit praesentium voluptatem illo! Ipsum iste quos accusantium quasi harum dicta dignissimos assumenda rerum, libero recusandae eligendi amet. Incidunt.</span></p>
				</article>
				<article>
					<h3>titre3</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fuga maiores, placeat? Consequatur corporis possimus, consectetur ducimus veniam accusamus fugiat perspiciatis, sed nemo, nobis quam explicabo similique necessitatibus quasi ullam doloribus.</p>
				</article>
	 		</div>
	 	</section>
	 </main>
 </body>
 </html>
