<h2>Liste utilisateurs</h2>

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

<?php 
$requete=$pdo_database->prepare('SELECT * FROM users WHERE ');
$requete->execute();
$resultat=$requete->fetchAll(PDO::FETCH_ASSOC);


?>