<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

$post = array_map('cleandata', $_POST);

//nettoyer get
$get = array_map('cleandata', $_GET);


if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}

if ($_SESSION['isconnected'] == false){
	header('Location: index.php');
	die;
} ?>


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

//pagination
//par defaut elle est a 1
if(!isset($get['page'])){
	$get['page']=1;
}

if(isset($get['page'])){
    if(is_numeric($get['page'])){
        if($get['page'] == 0){
            $get['page'] = 1;
        }
        $offset = ($get['page'] * 10) - 10; //INITIALISE LE DEBUT DES ARTICLES par decalage
    } else {
        $offset = 0;
    }


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
		echo '<p><strong>Titre:</strong> '.$val['title'].'</p>';
        echo '<p><strong>Contenu:</strong></p>';
		echo '<p>'.nl2br($val['content']).'</p><br>';
	    $date = date('d/m/Y à H:i', strtotime($val['date']));
		echo '<em><strong>Ecrit par: </strong>'.$resultatId['username'].'<strong> le </strong>'.$date.'</em>';
	    echo '<p><a href="actualites.php?modify='.$val['id'].'">Modifier</a></p>';
		echo '</article><hr />';
	}

	$next = $get['page'] + 1;// pour passer a la pge suivante
	$back = $get['page'] - 1;

	if($get['page']==1){
		echo '<a href="actualites.php?page='.$next.'">Page suivante</a>';
	}else{
		echo '<a href="actualites.php?page='.$back.'">Page precedente</a>';
		echo ' / ';
		echo '<a href="actualites.php?page='.$next.'">Page suivante</a>';
	}
}
?>
<p><a href="nouvelarticle.php">Nouvel article</a></p>

</body>
</html>
