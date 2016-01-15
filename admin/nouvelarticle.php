<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}
//Nettoyage des inputs utilisateur
$post = array_map('cleandata', $_POST);

//Verification de l'existance de la variable $_SESSION['isconnected']
if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}
//L'utilisateur est-il connecté.
if ($_SESSION['isconnected'] == false) {
	header('Location: index.php');
	die;
}

$error = array();
$errorForm = false;
$valideForm = false;
	//  TRAITEMENT DU FORMULAIRE
if (!empty($post)) {
	if (isset($post['titre']) && isset($post['contenu'])) {
		if (empty($post['titre'])) {
			$error[] = 'Le champ titre ne doit pas être vide.';
		}
		elseif (strlen($post['titre']) > 150) {
			$error[] = 'Le titre ne doit pas dépasser 150 caractères.';
		}

		if (empty($post['contenu'])) {
			$error[] = 'Le champ contenu ne doit pas être vide.';
		}

		if (count($error) > 0) {
			$errorForm = true;
		}

		else { // Insertion dans la base de données si il n'y a pas d'erreur
			$insertart = $pdo_database->prepare('INSERT INTO articles(title, content, date, author_id) 
				VALUES(:title, :content, NOW(), :user_id)');
			$insertart->bindValue(':title', $post['titre']);
			$insertart->bindValue(':content', $post['contenu']);
			$insertart->bindValue(':user_id', $_SESSION['user_id']);
			if ($insertart->execute()) {
				$formValide = true;
			}
		}
	}
}





?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Nouvelle actualité</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<?php
	// barre de tache admin/user

	include_once '../composants/barreadmin.php';


	// Affichage des erreurs
	if ($errorForm) {
		echo '<p style="color:red">'.implode('<br>', $error).'</p>';
	}
	// Message de succès
	elseif ($valideForm) {
		echo '<p style="color:green">L\'article a bien été envoyé</p>';
	}

?>
	<h1>Créer un Nouvel Article</h1>
	<form method="POST">
		<label for="titre">Titre :</label>
		<input type="text" name="titre" id="titre">	
		
		<label>Contenu :</label>
		<textarea name="contenu" id="contenu"></textarea>
		
		<input type="submit" value="Publier l'actualité">

	</form>

</body>
</html>