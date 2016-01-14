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

if ($_SESSION['isconnected'] == false):
	header('Location: index.php');
	die;
else: ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Gestion des actualités</title>
        <link rel="stylesheet" href="../css/admin.css">
    </head>
    <body>
<?php
include_once '../composants/barreadmin.php';
endif;

$offset = 0;
//Affichge des articles
$requete=$pdo_database->prepare('SELECT * FROM articles ORDER BY date DESC LIMIT 10 OFFSET :offset');
$requete->bindValue(':offset', $offset, PDO::PARAM_INT);
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
    $date = date('d/m/Y à H:i', strtotime($val['date']));
	echo '<em>Ecrit par: '.$resultatId['username'].' le '.$date.'</em>';
    echo '<p><a href="actualites.php?modify='.$val['id'].'">Modifier</a></p>';
	echo '</article><hr />';
}
?>
<p><a href="nouvelarticle.php">Nouvel article</a></p>

</body>
</html>
