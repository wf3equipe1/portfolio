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
} elseif (isset($get['logout'])) {
    header('Location: index.php');
}

//Affichage du formulaire d'oubli de mot de passe
if (isset($get['demandetoken'])){
    $creation_token = true;
} else {
    $creation_token = false;
}

//Creation du token d'oubli de mot de passe
if (isset($get['create_token'])){
    $req = $pdo_database->prepare('SELECT * FROM users WHERE email = :email');
    $req->bindValue(':email', $get['email']);
    $req->execute();
    $utilisateur = $req->fetch();
    if($utilisateur != false){
        $req = $pdo_database->prepare('INSERT INTO password_token (user_id, token) VALUES (:id, :token)');
        $req->bindValue(':id', $utilisateur['id']);
        $req->bindValue(':token',uniqid(rand(), true));
        if($req->execute() == false ){
            $req = $pdo_database->prepare('UPDATE password_token SET token = :token WHERE user_id = :id');
            $req->bindValue(':id', $utilisateur['id']);
            $req->bindValue(':token',md5(uniqid(rand(), true)));
            $req->execute();
        };
        //SEND EMAIL HERE
        // substr() + $_SERVER['REQUEST_URI']
        // http://site/admin/motdepasse.php?token=$token
    }
}

$error = array();
$errorForm = false;
$valideForm = false;

//Verification du contenu du formulaire
if(!empty($post)){
    if (!isset($post['email']) || !isset($post['password'])){
        $error[] = 'Formulaire invalide';
    } else {
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
        //Verification de l'existance de l'utilisateur
            //REQUETE DE JOINTURE:
            //u = table users, r = table roles
            //On ajoute le champ role de la table roles au résultat de la requête pour là où roles.id_user = users.id
    		$req = $pdo_database->prepare('SELECT u.*, r.role FROM users AS u LEFT JOIN roles AS r ON r.id_user = u.id WHERE u.email = :login LIMIT 1');
    		$req->bindValue(':login', $post['email']);

    		if ($req->execute()) {
    			$user = $req->fetch();

    			if ($user==false) {
    				$error[] = 'Adresse email invalide';
    			}
          //Verification du mot de passe:
    			elseif (password_verify($post['password'], $user['password'])) {

            //Création de la session
    				$_SESSION = array(
    					'isconnected'   =>  true,
            			'user_id'       => $user['id'],
    					'email'         => $user['email'],
    					'username'      => $user['username'],
    					'role'          => $user['role']
    					);

    			}
    			else {
    				$error[] = 'Mot de passe incorrect';
    			}
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
	<h1>Connection Espace Utilisateur</h1>
<?php
		include_once '../composants/barreadmin.php';

		if ($_SESSION['isconnected'] == false): ?>
    <?php if($creation_token): ?>

    <form method="get" action="index.php">
        <input type="hidden" name="create_token">
        <label for="email">Email :</label>
        <input type="email" name="email" id="email">
        <br>
        <input type="submit" value="Mot de passe oublié">
    </form>
    <?php elseif(count($error) == 0): ?>
	<form id="loginform" method="POST">
		<label for="email">Email :</label>
		<input type="email" name="email" id="email">

		<label for="password">Mot de passe :</label>
		<input type="password" name="password" id="password">

		<input type="submit" value="Se connecter">
		<p><a href="index.php?demandetoken">Mot de passe oublié</a></p>
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
