<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}

if ($_SESSION['isconnected'] == false) {
	header('Location: index.php');
	die;
}

// vérification des champs saisis du formulaire

$error = array();
$errorForm = false;
$formValid = false;


if (!empty($_POST) && isset($_POST)) {

	$post = array_map('cleandata', $_POST); 

	if (empty($post['nom'])) {
		$error[] = 'Veuillez saisir un nom';
	}

	if (empty($post['prenom'])) {
		$error[] = 'Veuillez saisir un prénom';
	}

	if (empty($post['email'])) {
		$error[] = 'Veuillez saisir un email';
	}
	elseif (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
		$error[] = 'La syntaxe de l\'email n\'est pas correcte';
	}

	if (empty($post['telephone'])) {
		$error[] = 'Veuillez saisir un numéro de téléphone';
	}

	if (empty($post['avatar'])) {
		$error[] = 'Veuillez saisir une URL d\'avatar';
	}

	if (empty($post['image'])) {
		$error[] = 'Veuillez saisir une URL d\'image';
	}

	// Requetes UPDATE des données options client

	if (count($error) > 0 ) {
		$errorForm = true;
	}
	else {
		//UPDATE options SET value = :lastname WHERE data = lastnamename
		$req = $pdo_database->prepare('UPDATE options SET value = :lastname WHERE data = \'lastname\'');
		$req->bindValue(':lastname', $post['nom'], PDO::PARAM_STR);
		if($req->execute() == false){
			$error[] = 'Erreur base de donnée.';
		}

		$req = $pdo_database->prepare('UPDATE options SET value = :firstname WHERE data = \'firstname\'');
		$req->bindValue(':firstname', $post['prenom'], PDO::PARAM_STR);
		if($req->execute() == false){
			$error[] = 'Erreur base de donnée.';
		}

		$req = $pdo_database->prepare('UPDATE options SET value = :email WHERE data = \'email\'');
		$req->bindValue(':email', $post['email'], PDO::PARAM_STR);
		if($req->execute() == false){
			$error[] = 'Erreur base de donnée.';
		}
		
		$req = $pdo_database->prepare('UPDATE options SET value = :phone WHERE data = \'phone\'');
		$req->bindValue(':phone', $post['telephone'], PDO::PARAM_STR);
		if($req->execute() == false){
			$error[] = 'Erreur base de donnée.';
		}

			$req = $pdo_database->prepare('UPDATE options SET value = :avatar WHERE data = \'avatar\'');
		$req->bindValue(':avatar', $post['avatar'], PDO::PARAM_STR);
		if($req->execute() == false){
			$error[] = 'Erreur base de donnée.';
		}


		$req = $pdo_database->prepare('UPDATE options SET value = :main_image WHERE data = \'main_image\'');
		$req->bindValue(':main_image', $post['image'], PDO::PARAM_STR);
		if($req->execute() == false){
			$error[] = 'Erreur base de donnée.';
		}

				
		$formValid = true;

	}
}


?>

<!DOCTYPE html>
<html>
<head>
	<title>Modifier les Options Client</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<?php
include_once '../composants/barreadmin.php';

// Requete pour récupérer sous forme d'array "$données[]" le contenu de table options
// et l'insérer en attribut value="" prérempli dans les champs du formulaire  
// "Manu est dans la place! cimer"

$req = $pdo_database->prepare('SELECT * FROM options');
$req->execute();
$donnees = array();
foreach ($req->fetchAll() as $elements) {
	$donnees[$elements['data']] = $elements['value'];
}

// Messages de succès/erreurs éventuels

	if ($errorForm) {
		echo '<p style="color:red">'.implode('<br>', $error).'</p>';
	}
	elseif ($formValid) {
		echo '<p style="color:green">Modification effectuée avec succès</p>';
	}
?>
	<form method="POST">
		<h1>Modifier les Options Client</h1>
		<label for="nom">Nom</label>
		<input type="text" name="nom" id="nom" value="<?php echo $donnees['lastname']?>">
		
		<label for="prenom">Prénom</label>
		<input type="text" name="prenom" id="prenom" value="<?php echo $donnees['firstname']?>">
		
		<label for="email">Email</label>
		<input type="text" name="email" id="email" value="<?php echo $donnees['email']?>">
		
		<label for="telephone">Téléphone</label>
		<input type="text" name="telephone" id="telephone" value="<?php echo $donnees['phone']?>">

		<label for="avatar">URL de l'image</label>
		<input type="text" name="avatar" id="avatar" value="<?php echo $donnees['avatar']?>">
		
		<label for="image">URL de l'image</label>
		<input type="text" name="image" id="image" value="<?php echo $donnees['main_image']?>">
				
		<input type="submit" value="Mettre à jour">
	</form>	


</body>
</html>