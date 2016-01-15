<?php // code by bert
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

$post = array_map('cleandata', $_POST);
$get = array_map('cleandata', $_GET);

// vérification mode session

if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}

if ($_SESSION['isconnected'] == false && !isset($_GET['token'])) {
	header('Location: index.php');
	die;
}

// vérification mode "oubli"

if(isset($_GET['token'])){

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
$formValid = false;
$error = array();

if (!empty($_POST)){

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

	if (count($error) > 0){
		$errorForm = true;
	}
	else{
		// En mode connecté:
        if(!isset($get['token'])){
            //On récupère et vérifie l'ancien mot de passe
            $req = $pdo_database->prepare('SELECT password FROM users WHERE id = :id');
            $req->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            if($req->execute()){
                $password = $req->fetch();
            }

            if(!password_verify($post['password'], $password['password'])){
    			$error[] = 'mot de passe incorrect';
    		}
    		else{
    			// update du password dans SQL
    			$formValid = true;

    			$req = $pdo_database->prepare('UPDATE users SET password = :password WHERE id = :id');
				$req->bindValue(':password', password_hash($post['new2'], PASSWORD_DEFAULT));
				$req->bindValue(':id', $_SESSION['user_id']);
				if(!$req->execute()){
					$error[] = 'Erreur base de donnée';
				}
            }
        } else {

            $req = $pdo_database->prepare('UPDATE users SET password = :password WHERE id = :id');
            $req->bindValue(':password', password_hash($post['new2'], PASSWORD_DEFAULT));
            $req->bindValue(':id', $oubli['user_id']);

            if(!$req->execute()){
                $error[] = 'Erreur base de donnée';
            } else {
                $delete_token = $pdo_database->prepare('DELETE FROM password_token WHERE user_id = :id ');
                $delete_token->bindValue(':id', $oubli['user_id'], PDO::PARAM_INT);
                $delete_token->execute();
                $formValid = true;
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

	if (count($error)>0) {
		echo '<p style="color:red">'.implode('<br>', $error).'</p>';
	}
	elseif ($formValid) {
		echo '<p style="color:green">Mot de passe mis à jour avec succès</p>';
	}
?>
	<h1>Modifier votre Mot de Passe</h1>
	<form method="post">
		<?php
		if (!isset($_GET['token'])):?>
			<label for="motdepasse">Mot de passe actuel:</label>
			<input type="password" id="motdepasse" name="password" placeholder="">
		<?php endif ?>
			<label for="new1">Nouveau mot de passe :</label>
			<input type="password" id="new1" name="new1" placeholder="">
			<label for="new2">Confirmer le mot de passe :</label>
			<input type="password" id="new2" name="new2" placeholder="">
			<input type="submit" value="Modifier">
	</form>

</body>
</html>
