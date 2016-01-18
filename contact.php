<?php

require_once 'composants/db.php';
require 'vendor/autoload.php';

$mail = new PHPMailer;

$mail->isSMTP();                                      
$mail->Host = 'smtp.mailgun.org';  
$mail->SMTPAuth = true;                              
$mail->Username = 'equipe1@wf3.axw.ovh';                
$mail->Password = '7TV3Gtaue4F6d2';                          
$mail->SMTPSecure = 'tls';                           
$mail->Port = 587;

//recuperation mail
$reqMail = $pdo_database->prepare('SELECT email FROM options');
$reqMail->execute();
$resultatMail=$reqMail->fetch(PDO::FETCH_ASSOC);

$mail->setFrom('equipe1@wf3.axw.ovh', 'portfolio');
$mail->addAddress($resultatMail['email'], 'formulaire de contact');

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
		$mail->isHTML(true); 

		$mail->Subject = $post['sujet'];
		$mail->Body    = $post['message'];
		$mail->send(); // Pas besoin de check les erreurs vu que au pire il est dans la db

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
