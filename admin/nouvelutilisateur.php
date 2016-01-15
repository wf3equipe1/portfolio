<?php
session_start();
require_once '../composants/db.php';


if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}

if ($_SESSION['isconnected'] == false) {
	header('Location: index.php');
	die;
}

 // Nettoyage du formulaire
function cleandata($data){
	return trim(htmlentities($data));
}

$post = array_map('cleandata', $_POST);

$error = array();
$errorForm = false;
$valideForm = false;

	// TRAITEMENT DU FORMULAIRE
if (!empty($post)) {
	if(isset($post['pseudo']) && isset($post['email']) && isset($post['password']) && isset($post['password2']) && isset($post['role'])){
		if (empty($post['pseudo'])) {
			$error[] = 'Le champ pseudo ne doit pas être vide';
		}
		elseif (strlen($post['pseudo']) > 40) {
			$error[] = 'Le pseudo ne doit pas dépasser 40 caractères';
		}

	  	if (empty($post['email'])) {
	  		$error[] = 'Le champ email ne doit pas être vide';
	  	}
	  	elseif (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
	  		$error[] = 'La syntaxe de l\'email n\'est pas correcte';
	  	}
	  	elseif (strlen($post['email']) > 255) {
	  		$error[] = 'L\'email ne doit pas dépasser 255 caractères';
	  	}

	  	if (empty($post['password'])) {
	  		$error[] = 'Le champ mot de passe ne doit pas être vide';
	  	}
	  	elseif (strlen($post['password']) > 255) {
	  		$error[] = 'Le mot de passe ne doit pas dépasser 255 caractères';
	  	}

	  	if (strlen($post['password2']) > 255) {
	  		$error[] = 'Le mot de passe ne doit pas dépasser 255 caractères';
	  	}

	  	if ($post['password'] != $post['password2']) {
	  		$error[] = 'Les deux mots de passe doivent être identique';
	  	}

	  	if (isset($post['role'])) {
	  		if (!($post['role'] == 'admin' || $post['role'] == 'editor')) {
	  			$error[] = 'Role incorrect';
	  		}
  	} else {
  			$error[] = 'Role incorrect';
  		}
  } else {
   	 $error[] = 'Formulaire invalide.';
  	}

	 // Insertion dans la base de donnée si il n'y a pas d'erreur
	if(count($error) == 0) {
		$insertuser = $pdo_database->prepare('INSERT INTO users(email, password, username) VALUES(:email, :password, :username)');
		$insertuser->bindValue(':email', $post['email'], PDO::PARAM_STR);
		$insertuser->bindValue(':password', password_hash($post['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
		$insertuser->bindValue(':username', $post['pseudo'], PDO::PARAM_STR);

		if ($insertuser->execute()) {
			$insertrole =$pdo_database->prepare('INSERT INTO roles(role, id_user) VALUES(:role, :id)');
			$insertrole->bindValue(':role',$post['role']);
			$insertrole->bindValue(':id', $pdo_database->lastInsertId(), PDO::PARAM_INT);
			if ($insertrole->execute()) {
				$valideForm = true;
			} else {
				$error[] = "Erreur base de donnée.";
			}
		} else {
			$error[] = "L'email est déjà utilisée.";
		}
	}

	if (count($error) > 0 ) {
		$errorForm = true;
	}


}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Nouvel utilisateur</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<?php
	include_once '../composants/barreadmin.php';	

	// Affichage des erreurs
	if ($errorForm) {
		echo '<p style="color:red">'.implode('<br>', $error).'</p>';
	}
	// Message de succès
	elseif ($valideForm) {
		echo '<p style="color:green">Le compte a bien été créé</p>';
	}
?>
	<h1>Créer un Nouvel Utilisateur</h1>
	<!-- Formulaire -->
	<form method="POST">
		<label for="pseudo">Pseudo :</label>
		<input type="text" name="pseudo" id="pseudo" placeholder="Votre pseudo">
		
		<label for="email">Email :</label>
		<input type="email" name="email" id="email" placeholder="votre@email.fr">
		
		<label for="password">Mot de passe :</label>
		<input type="password" name="password" id="password">
		
		<label for="password2">Confirmer le mot de passe :</label>
		<input type="password" name="password2" id="password2">
		<label for="role">Role :</label>
		<select name="role" id="role">
			<option value="admin">Admin</option>
			<option value="editor">Editeur</option>
		</select>
		<input type="submit" value="Créer l'Utilisateur">
	</form>


</body>
</html>
