<?php // code by bert
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

// vérification mode session

if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}

if ($_SESSION['isconnected'] == false && !isset($_GET['token'])) {
	header('Location: index.php');
	die;
}

// vérification mode "oubli"

if(!empty($_GET) && isset($_GET['token'])){
	$get = array_map('cleandata', $_GET);

	$req = $pdo_database->prepare('SELECT * FROM password_token WHERE token = :token');
	$req->bindValue(':token', $get['token']);
	if($req->execute()){
		$oubli = $req->fetch();
		if($oubli == false){
			header('Location: index.php');
			die;
		}
	}

}

$errorForm = false;
$formValid = true;
$error = array();

if (!empty($_POST) && isset($_POST)){
	$post = array_map('cleandata', $_POST);

	// vérification des champs

	if (empty($post['password']) && !isset($get['token'])){
		$error[] = 'Vous n\'avez pas saisi votre mot de passe';
	} // inutile en mode oubli

	if (empty($post['new1'])){
		$error[] = 'Veuillez saisir votre nouveau mot de passe';
	}
	else if (strlen($post['new1']) < 1){
		$error[] = 'Votre mot de passe doit contenir minimum 8 caractères';
	}

	if (empty($post['new2'])){
		$error[] = 'vous devez confirmer votre nouveau mot de passe';
	}
	else if ($post['new2'] != $post['new1']){
		$error[] = 'Confirmation de mot de passe incorrecte';
	}


	if ($error > 0){
		$errorForm = true;
	}
	else{
		// vérification de la validité du "futur ex" mot de passe

		$req = $pdo_database->prepare('SELECT password FROM users WHERE id = :id');
		$req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
		if($req->execute()){
			$password = $req->fetch();
		}

		if(!password_verify($post['password'], $password)){
			$err[] = 'mot de passe incorrect';
		}
		else{
			// update du password dans SQL
			$formValid = true;

			$req = $pdo_database('UPDATE users SET value = :password WHERE id = :id');
			// en mode session
			if ($_SESSION['isconnected'] == true || !isset($_GET['token'])){
				$req->bindValue(':password', $post['new2']);
				$req->bindValue(':id', $_SESSION['id']);
				if($req->execute()){
					$err[] = 'Erreur base dedonnée';
				}
			}
			// en mode oubli de mot de passe
			else {
				$req->bindValue(':password', $post['new2']);
				$req->bindValue(':id', $oubli['user_id']);
				if($req->execute()){
					$err[] = 'Erreur base dedonnée';
				}
			}

		}
	}
}



?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Mot de passe</title>
	<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
	<?php
	include_once '../composants/barreadmin.php';

	if ($errorForm) {
		echo '<p style="color:red">'.implode('<br>', $error).'</p>';
	}
	elseif ($formValid) {
		echo '<p style="color:green">Mot de passe mis à jour avec succès</p>';
	}
?>
	<form method="post">
		<?php
		if (!isset($_GET['token'])):?>
			<label for="motdepasse"></label>
			<input type="password" id="motdepasse" name="motdepasse" placeholder="">
		<?php endif ?>
			<label for="new1"></label>
			<input type="password" id="new1" name="new1" placeholder="">
			<label for="new2"></label>
			<input type="password" id="new2" name="new2" placeholder="">
			<input type="submit" value="Modifier">
	</form>

</body>
</html>
