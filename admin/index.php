<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

//Nettoyage des inputs utilisateur
$post = array_map('cleandata', $_POST);
$get = array_map('cleandata', $_GET);

//Verification de l'existance de la variable $_SESSION['isconnected']
if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}
//Déconnexion de l'utilisateur.
if (isset($get['logout']) && $_SESSION['isconnected']) {
	$_SESSION = array('isconnected' => false);
}

$error = array();
$errorForm = false;
$valideForm = false;

//Verification du contenu du formulaire
if (!empty($post)) {
	if (!isset($post['email']) || !isset($post['password'])){
    $error[] = 'Formulaire invalide';
  } else {
    if (empty($post['email'])) {
      $error[] = 'Veuillez saisir votre email';
    }

    if (empty($post['password'])) {
      $error[] = 'Veuillez saisir votre mot de passe';
    }
  }

	if (count($error) > 0) {
		$errorForm = true;
	}
	else {
    //Verification de l'existance de l'utilisateur
		$req = $pdo_database->prepare('SELECT * FROM users WHERE email = :login LIMIT 1');
		$req->bindValue(':login', $post['email']);

		if ($req->execute()) {
			$user = $req->fetch();

			if ($user==false) {
				$error[] = 'Adresse email invalide';
			}
      //Verification du mot de passe:
			elseif (password_verify($post['password'], $user['password'])) {
        //Récupération des droits de l'utilisateur
				$req = $pdo_database->prepare('SELECT role FROM roles WHERE id_user = :id');
				$req->bindValue(':id', $user['id']);
				$req->execute();
				$role = $req->fetch();
        //Si aucun droit ne correspond a l'utilisateur
				if ($role == false) {
					$error[] = 'Erreur de permission';
				}
				else {
					$_SESSION = array(
						'isconnected'   =>  true,
            			'user_id'       => $user['id'],
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
	<meta charset="utf-8">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<?php if ($_SESSION['isconnected'] == false): ?>
    <?php if(count($error) == 0): ?>
	<form id="loginform" method="POST">
		<label for="email">Email</label>
		<input type="email" name="email" id="email">
		<br>
		<label for="password">Mot de passe</label>
		<input type="password" name="password" id="password">
		<br>
		<input type="submit" value="Se connecter">

	</form>
    <?php else: ?>
    <p><?= implode(' ', $error); ?></p>
    <p><a href="index.php">Retour</a></p>
    <?php endif; ?>
<?php elseif (isset($get['logout']) && $_SESSION['isconnected']): ?>
	<p>Vous avez été déconnecté.</p>
    <p><a href="../index.php">Accueil</a></p>
<?php else:
	header('Location: actualites.php');
	die;
endif; ?>

</body>
</html>
