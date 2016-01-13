<?php 
	require_once'composants/db.php';

	$req=$pdo_database->prepare('SELECT * FROM options');
	$req->execute();
	$result=$req->fetchAll(PDO::FETCH_ASSOC);

	$donnee=array();//variable pour recuperer les value des champs

	foreach ($result as $value) {
		$donnee[$value['data']] = $value['value'];//chaque dat donne une value
	}
?>
<section id="leftSide">
	<header>
		<img src="images/photo_exemple.jpg" alt="clientPicture">
		<ul>
			<li><i class="fa fa-user"></i> <?=$donnee['lastname'].' '.$donnee['firstname']; ?></li>
			<li><i class="fa fa-phone"></i> <?=$donnee['phone']; ?></li>
			<li><i class="fa fa-envelope"></i> <?=$donnee['email']; ?></li>
		</ul>
	</header>

	<nav>
		<ul>
			<li><a href="index.php">Accueil</a></li>
			<li><a href="actualites.php">Les news</a></li>
			<li><a href="contact.php">Contact</a></li>
		</ul>
	</nav>
</section>