<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

$post = array_map('cleandata', $_POST);

$error = array();
$errorForm = false;
$valideForm = false;

if (!empty($post)) {
	
	if (empty($post['email'])) {
		$error[] = 'Veuillez saisir votre email';
	}

	if (empty($post['password'])) {
		$error[] = 'Veuillez saisir votre mot de passe';
	}

	if (count($error) > 0) {
		$errorForm = true;
	}
	else {
		$req = $pdo_database->prepare('SELECT * FROM users WHERE email = :login LIMIT 1')
		$req->bindValue(':login', $post['email']);

		if ($req->execute()) {
			$user = $req->fetch();
			
			if ($user==false) {
				$error[] = 'Adresse email invalide';
			}
			elseif (password_verify($post['password'], $user['password'])) {
				$_SESSION = array(
					'isconnected'   =>  true,
					'email'         => $user['eamil'],
					'username'      => $user['username']
					);
			}
			else {
				$error[] = 'Mot de passe incorrecte';
			}
		}
	}

}



?>

<!DOCTYPE html>
<html>
<head>
	<title>Connexion/deconnexion</title>
	<meta charset="utf-8";>
</head>
<body>
	<form method="POST">
		<label for="email">Email</label>
		<input type="email" name="email" id="email">
		<br>
		<label for="password">Mot de passe</label>
		<input type="password" name="password" id="password">
		<br>
		<input type="submit" value="Se connecter">

	</form>

</body>
</html>