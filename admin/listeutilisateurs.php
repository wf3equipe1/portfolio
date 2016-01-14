<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

$post = array_map('cleandata', $_POST);

if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}

if ($_SESSION['isconnected'] == false) {
	header('Location: index.php');
	die;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	
<h2>Liste utilisateurs:</h2>
<?php 
echo '<ul>';
foreach ($result as $value) {
	$requete=$pdo_database->prepare('SELECT * FROM users');
	$requete->execute();
	$resultat=$requete->fetch(PDO::FETCH_ASSOC);

	echo '<li>'.$value['username'].'</li>';	
}
echo '</ul>';
?>
</body>
</html>