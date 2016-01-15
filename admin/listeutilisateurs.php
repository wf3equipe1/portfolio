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
	<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<?php 	include_once '../composants/barreadmin.php'; ?>

<h1>Liste des utilisateurs:</h1>
<?php

	$requete=$pdo_database->prepare('SELECT * FROM users');
	$requete->execute();
	$result=$requete->fetchAll(PDO::FETCH_ASSOC);
echo '<ul>';
foreach ($result as $value) {
	echo '<li>'.$value['username'].'</li>';
}
echo '</ul>';
?>
</body>
</html>
