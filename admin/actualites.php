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
//A faire => page suivante
//REQUETE POUR table ARTICLES
$requete=$pdo_database->prepare('SELECT * FROM articles ORDER BY date DESC LIMIT 10 OFFSET :offset');
$requete->execute();
$resultat=$requete->fetchAll(PDO::FETCH_ASSOC);

foreach ($resultat as $val){
	//requete pour nom d'utilisateur par id
	$requeteId=$pdo_database->prepare('SELECT * FROM users WHERE id=:id');
	$requeteId->bindValue(':id', $val['author_id']);
	$requeteId->execute();
	$resultatId=$requeteId->fetch(PDO::FETCH_ASSOC);

	echo '<article>';
	echo '<h3>'.$val['title'].'</h3>';
	echo '<p>'.nl2br($val['content']).'</p><br>';
	echo '<em>ecrit par '.$resultatId['username'].'<em>';
	echo '</article>';
}
?>