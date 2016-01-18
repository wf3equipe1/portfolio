<?php

require_once 'composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

$error = array();
$formValid = false;
$errorForm = false;

if (!empty($_POST)){
	$post = array_map('cleandata', $_POST);

	if(empty($post['email'])){
		$error[] = 'Veuillez entrer votre email';
	} 
	elseif (preg_match("/^[\w\-\.]+@[\w\-\.]+\.[a-z]{2,}$/i", $post['email'])) {
		  		
	}
	else {
		$error[] = 'La syntaxe de l\'email n\'est pas correcte';
	}

	if (empty($post['sujet'])){
		$error[] = 'Veuillez entre le sujet du message';
	}
	if (empty($post['message'])){
		$error[] = 'Veuillez entrer le message';
	}
	if(count($error) > 0){
		$errorForm = true;
	}else{
		$req = $pdo_database->prepare('INSERT INTO contact (email, subject, message, date, checked) VALUES (:email, :sujet, :message, NOW(), FALSE)');
		$req->bindValue(':email', $post['email']);
		$req->bindValue(':sujet', $post['sujet']);
		$req->bindValue(':message', $post['message']);
		if($req->execute()){
			$formValid = true;
		}else{
			$error[] = 'Erreur base de données';
		}

	}
}





?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Contact</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<main>
		<?php include_once 'composants/menugauche.php'; ?>
		<section id="rightSide">
            <h1>Contact</h1>
		<?php
		if (count($error)>0){
			echo '<p class="popup" style="color: red">'.implode('<br>', $error).'</p>';
		}
		else if($formValid){
			echo '<p class="popup" style="color: green">Votre message a été envoyé avec succès</p>';
		}

		?>
            <div id="blocNews">
			<form method="post">
				<label for="email">Email :</label><br />
				<input type="text" id="email" name="email" placeholder="votre email" class="champContact"><br />
				<label for="sujet">Sujet :</label><br />
				<input type="text" id="sujet" name="sujet" placeholder="sujet du message" class="champContact"><br />
				<label for="message">Message :</label><br />
				<textarea id="message" name="message" rows="20" cols="70" placeholder="votre message ici..."></textarea><br />
				<input type="submit" value="Envoyer" id="envoyerMessage">

			</form>
            </div>
		</section>
	</main>
</body>
</html>
