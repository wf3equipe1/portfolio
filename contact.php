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
            <div id="blocNews">
			<form method="post">
				<label for="email">Email :</label><br />
				<input type="text" id="email" name="email" placeholder="votre email"><br />
				<label for="sujet">Sujet :</label><br />
				<input type="text" id="sujet" name="sujet" placeholder="sujet du message"><br />
				<label for="message">Message :</label><br />
				<textarea id="message" name="message" rows="20" cols="70" placeholder="votre message ici..."></textarea><br />
				<input type="submit" value="Envoyer votre Message">

			</form>
            </div>
		</section>
	</main>
</body>
</html>
