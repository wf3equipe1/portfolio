<?php

require_once 'composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

$error = array();
$formValid = false;
$errorForm = false;

if (!empty($_POST) && isset($_POST)){
	$post = array_map('cleandata', $_POST);

	if(empty($post['email'])){
		$error[] = 'Veuillez entrer votre email';
		} else if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)){
			$error[] = 'Votre email n\'est pas au bon format';
	}

	if (empty($post['sujet'])){
		$error[] = 'Veuillez entre le sujet du message';
	}
	if (empty($post['message'])){
		$error[] = 'Veuillez entrer le message';
	}

	if(count($error > 0)){
		$errorForm = true;
	}else{
		$formValid = true;
		$req = $pdo_database('INSERT INTO contact (email, subject, message, date, checked) VALUES (:email, :sujet, :message, NOW()), false');
		$req->bindValue(':email', $post['email']);
		$req->bindValue(':sujet', $post['sujet']);
		$req->bindValue(':message', $post['message']);
		$req->execute();
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
		<?php

		include_once 'composants/menugauche.php';

		if ($errorForm == true){
			echo '<p style="color: red">'.implode('<br>', $error).'</p>';
		}
		else if($formValid == true){
			echo '<p style="color: green">Votre message a été envoyé avec succès</p>';
		}

		?>
		<section id="rightSide">
			<form method="post">
				<label for="email">Email :</label>
				<input type="text" id="email" name="email" placeholder="votre email">
				<label for="sujet">Sujet :</label>
				<input type="text" id="sujet" name="sujet" placeholder="sujet du message">
				<label for="message">Message :</label>
				<textarea id="message" name="message" placeholder="votre message ici..."></textarea>
				<input type="submit" value="Envoyer votre Message">		

			</form>
		</section>
	</main>
</body>
</html>