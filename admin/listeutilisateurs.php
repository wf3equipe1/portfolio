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

	$requete=$pdo_database->prepare('SELECT u.*, r.role FROM users AS u LEFT JOIN roles AS r ON r.id_user = u.id');
	$requete->execute();
	$result=$requete->fetchAll(PDO::FETCH_ASSOC);
echo '<table>';
    echo '<thead><tr><td>ID</td><td>Nom d\'utilisateur</td><td>Email</td><td>Rôle</td></tr></thead>';
    echo '<tbody>';
foreach ($result as $value) {
    echo '<tr>';
    echo '<td>'.$value['id'].'</td>';
	echo '<td>'.$value['username'].'</td>';
    echo '<td>'.$value['email'].'</td>';
    echo '<td>'.$value['role'].'</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
?>
</body>
</html>
