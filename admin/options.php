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

$mimeTypeAllowed= array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'); //fichier possible
$finfo = new finfo();
	
	if(isset($_FILES['image'])){
			$maxSize = 5*1000*1024; //5Mo

			// On vérifie que le mime type soit le bon
			$fileMimeType = $finfo->file($_FILES['image']['tmp_name'], FILEINFO_MIME_TYPE);
			if(!in_array($fileMimeType, $mimeTypeAllowed)){ // 
				$error[]= "Le fichier n'est pas une image";
			}

			// On vérifie la taille du fichier
			if($_FILES['image']['size'] <= $maxSize){
 				$uploads_dir_image = '../images';
				$tmp_name = $_FILES['image']['tmp_name'];
				$nameCover = $_FILES['image']['name'];

				// On upload le fichier
				$uploadCover=move_uploaded_file($tmp_name, $uploads_dir_image.'/'.$nameCover);
		    }
		    else{
		    	$error[]='<p style="color:red">fichier trop volumineux</p>';
		    }
	}


	//fichier image et avatar	
	if(isset($_FILES['avatar'])){
			$maxSize = 5*1000*1024; //5Mo

			// On vérifie que le mime type soit le bon
			$fileMimeType = $finfo->file($_FILES['avatar']['tmp_name'], FILEINFO_MIME_TYPE);
			if(!in_array($fileMimeType, $mimeTypeAllowed)){ // 
				$error[]= "Le fichier n'est pas une image";

			}

			// On vérifie la taille du fichier
			if($_FILES['avatar']['size'] <= $maxSize){
 				$uploads_dir_avatar = '../images';//insertion dans le dossier
				$tmp_name = $_FILES['avatar']['tmp_name'];
				$nameAvatar = $_FILES['avatar']['name'];

				// On upload le fichier
				$uploadAvatar=move_uploaded_file($tmp_name, $uploads_dir_avatar.'/'.$nameAvatar);
		    }
		    else{
		    	$error[]='<p style="color:red">fichier trop volumineux</p>';
		    }
	}

	

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

		$req = $pdo_database->prepare('UPDATE options SET value = :avatar WHERE data = \'avatar\''); //insertion du nom de l'image dans la bdd
		$req->bindValue(':avatar', 'images'.'/'.$nameAvatar, PDO::PARAM_STR);
		if($req->execute() == false){
			$error[] = 'Erreur base de donnée.';
		}


		$req = $pdo_database->prepare('INSERT INTO pictures_cover(url) VALUES(:url)');
		$req->bindValue(':url', 'images'.'/'.$nameCover, PDO::PARAM_STR);
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
?>  <h1>Modifier les Options Client</h1>
	<form method="POST" enctype="multipart/form-data">
		
		<label for="nom">Nom :</label>
		<input type="text" name="nom" id="nom" value="<?php echo $donnees['lastname']?>">
		
		<label for="prenom">Prénom :</label>
		<input type="text" name="prenom" id="prenom" value="<?php echo $donnees['firstname']?>">
		
		<label for="email">Email :</label>
		<input type="text" name="email" id="email" value="<?php echo $donnees['email']?>">
		
		<label for="telephone">Téléphone :</label>
		<input type="text" name="telephone" id="telephone" value="<?php echo $donnees['phone']?>">

		<label for="avatar">Fichier image personnelle :</label>
		<input type="file" name="avatar" id="avatar" >
		
		<label for="image">Fichier image de couverture :</label>
		<input type="file" name="image" id="image" >
				
		<input type="submit" value="Mettre à jour les Options">
	</form>	


</body>
</html>