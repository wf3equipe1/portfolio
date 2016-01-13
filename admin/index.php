<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

$post = array_map('cleandata', $_POST);
$get = array_map('cleandata', $_GET);

if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}
if (isset($get['logout']) && $_SESSION['isconnected']) {
	$_SESSION = array('isconnected' => false);
}

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
		$req = $pdo_database->prepare('SELECT * FROM users WHERE email = :login LIMIT 1');
		$req->bindValue(':login', $post['email']);

		if ($req->execute()) {
			$user = $req->fetch();
			
			if ($user==false) {
				$error[] = 'Adresse email invalide';
			}
			elseif (password_verify($post['password'], $user['password'])) {
				$req = $pdo_database->prepare('SELECT role FROM roles WHERE id_user = :id');
				$req->bindValue(':id', $user['id']);
				$req->execute();
				$role = $req->fetch();
				if ($role == false) {
					$error[] = 'Erreur de permission';
				}
				else {
					$_SESSION = array(
						'isconnected'   =>  true,
						'email'         => $user['email'],
						'username'      => $user['username'],
						'role'          => $role['role']
						);
				}



			}
			else {
				$error[] = 'Mot de passe incorrect';
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
<?php if ($_SESSION['isconnected'] == false): ?>
	<form method="POST">
		<label for="email">Email</label>
		<input type="email" name="email" id="email">
		<br>
		<label for="password">Mot de passe</label>
		<input type="password" name="password" id="password">
		<br>
		<input type="submit" value="Se connecter">

	</form>
<?php elseif (isset($get['logout']) && $_SESSION['isconnected']): ?>
	<p>Vous avez été déconnecté.</p>
<?php else: 
	header('Location: actualites.php');
	die;
endif; ?>

</body>
</html>