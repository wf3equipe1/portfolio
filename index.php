<?php //Page d'accueil du site
session_start();
require_once'composants/db.php';
//REQUETE POUR table OPTIONS
$req=$pdo_database->prepare('SELECT * FROM options');
$req->execute();
$result=$req->fetchAll(PDO::FETCH_ASSOC);

$donnee=array();//variable pour recuperer les value des champs

foreach ($result as $value) {
	$donnee[$value['data']] = $value['value'];//chaque data donne une value
}

//REQUETE POUR table ARTICLES
$requete=$pdo_database->prepare('SELECT * FROM articles ORDER BY date DESC LIMIT 3');
$requete->execute();
$resultat=$requete->fetchAll(PDO::FETCH_ASSOC);

?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Page principale</title>
 	<meta charset="utf-8">
 	<link rel="stylesheet" href="css/flexslider.css" type="text/css">
 	<link rel="stylesheet" href="css/style.css">
 </head>
 <body>
	 <main>
	 	<?php include_once 'composants/menugauche.php'; //MENU DE GAUCHE?>
		<?php
		 	require_once 'composants/db.php';

		 	$req=$pdo_database->prepare('SELECT * FROM pictures_cover');
		 	$req->execute();
		 	$result=$req->fetchAll(PDO::FETCH_ASSOC);

		?>
	 	<section id="rightSide">
	 		<h1><?= $donnee['title']; ?></h1>


	 		<aside id="slideshow">
			    <div class="container flexslider" id="cover">
			        <!-- <figure>
			            <img src="img/slide1.png" alt="design clean crisp memorable icons">
			            <figcaption>We design clean, crisp & memorable icons</figcaption>
			        </figure> -->
			        <ul class="slides">
			        	<li><img src="<?php  
			        					foreach ($result as $valCover) {
			        						echo $valCover['url'];
			        					}
			        				  ?>" alt=""></li>
			            <li><img src="http://www.louisetzeliemartin.org/medias/images/chat-1.jpg" alt=""></li>
			            <li><img src="http://www.louisetzeliemartin.org/medias/images/chat.jpg" alt=""></li>
			            <li><img src="http://media.virginradio.fr/article-2505914-fb-f1415609183/chat-mignon-petit-chaton-therapie-detente.jpg" alt=""></li>
			            <li><img src="https://www.lepetiterudit.com/wp-content/uploads/2014/08/chat.jpg" alt=""></li>
			        </ul>
			        <!--
			        <div class="points">
			            <span></span>
			            <span></span>
			            <span></span>
			            <span></span>
			            <span></span>
			        </div> -->
			    </div>
			</aside>



	 		<div id="blocNews">
	 			<h2>Les news</h2>
				<?php
				foreach ($resultat as $val){
					echo '<article>';
					echo '<h3>'.$val['title'].'</h3>';
					echo '<p>'.substr($val['content'],0,200).'</p><br>';
					echo '<a href="actualites.php?id='.$val['id'].'">Suite...</a>';//ENVOI A LA PAGE DE VISUALISATION par id puis GETdans l'autre page
					echo '</article>';
				}
				?>
	 		</div>
	 	</section>
	 </main>

	 <script src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
	 <script src="js/jquery.flexslider.js"></script>
	 <script src="js/main.js"></script>
 </body>
 </html>
