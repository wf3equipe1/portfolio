<?php 
session_start();
require_once 'composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

$post = array_map('clean_data', $_POST);

$error = array()
$errorForm = false;
$valideForm = false;

if (!empty($post)) {
	
	if (empty($post['pseudo'])) {
		$error[] = 'Le champ pseudo ne doit pas être vide';
	}

	if (empty($post['email'])) {
		$error[] = 'Le champ email ne doit pas être vide';
	}
	elseif (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
		$error[] = 'La syntaxe de l\'email n\'est pas correcte';
	}

	if (empty($post['password'])) {
		$error[] = 'Le champ mot de passe ne doit pas être vide';
	}

	if (count($error) > 0 ) {
		$errorForm = true;
	}
	else {
		$insertuser = $pdo_database->prepare('INSERT INTO users(email, password, username) VALUES(:email, :password, :username)');
		$insertuser->bindValue(':email', $post['email'], PDO::PARAM_STR);
		$insertuser->bindValue(':password', password_hash($post['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
		$insertuser->bindValue(':username', $post['pseudo'], PDO::PARAM_STR);

		if ($insertuser->execute()) {
			$valideForm = true;
		}

	}


}

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>
<?php
	if ($errorForm) {
		echo '<p style="color:red">'.implode('<br>', $error).'</p>';
	}

	if ($valideForm) {
		echo '<p style="color:green">Le compte a bien été crée</p>';
	}
?>


	<form method="POST">
		<label for="pseudo">pseudo</label>
		<input type="text" name="pseudo" id="pseudo" placeholder="Votre pseudo">
		<br>
		<label for="email">email</label>
		<input type="text" name="email" id="email" placeholder="votre@email.fr">
		<br>
		<label for="password">mot de passe</label>
		<input type="password" name="password" id="password">
		<br>
		<input type="submit" value="Envoyer">
	</form>	


</body>
</html>