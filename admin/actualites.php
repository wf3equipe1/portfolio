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
echo '<h1>Modifier les articles</h1>';

//pagination
//par defaut elle est a 1
if(empty($get)){
	$get['page']=1;
}

if(isset($get['modify'])){
    if(!is_numeric($get['modify'])){
        unset($get['modify']);
    } else {
        if(!empty($post)){
            //traitement du formulaire:
            if(empty($post['title'])){
                echo '<p>Le titre ne peut être vide.</p>';
            } elseif(empty($post['content'])){
                echo '<p>Le contenu ne peut être vide.</p>';
            } else {
                $req=$pdo_database->prepare('UPDATE articles SET title = :title, content = :content WHERE id = :id');
                $req->bindValue(':title', $post['title']);
                $req->bindValue(':content', $post['content']);
                $req->bindValue(':id', $get['modify']);
                if($req->execute()){
                    echo '<p>Article modifié avec succes.</p>';
                    unset($get['modify']);
                }
            }
        }
    }
}


if(isset($get['page'])):
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
		echo '<p><strong>Titre :</strong> '.$val['title'].'</p>';
        echo '<p><strong>Contenu : </strong>'.nl2br($val['content']).'</p><br>';
	    $date = date('d/m/Y à H:i', strtotime($val['date']));
		echo '<em><strong>Ecrit par : </strong>'.$resultatId['username'].'<strong> le </strong>'.$date.'</em>';
	    echo '<p id="modifier"><a href="actualites.php?modify='.$val['id'].'">Modifier</a></p>';
		echo '</article><hr />';
	}

	echo '<a class="lien" href="nouvelarticle.php">Ajouter un Article</a>';
	
	$next = $get['page'] + 1;// pour passer a la pge suivante
	$back = $get['page'] - 1;

	?>
	<div class="pagination">
	<?php 
	if($get['page']==1){
		echo '<a href="actualites.php?page='.$next.'">Page suivante</a>';
	}else{
		echo '<a href="actualites.php?page='.$back.'">Page precedente</a>';
		echo ' / ';
		echo '<a href="actualites.php?page='.$next.'">Page suivante</a>';
	}
	?></div>
	<?php
    
	elseif(isset($get['modify'])):
    $req=$pdo_database->prepare('SELECT * FROM articles WHERE id=:id');
    $req->bindValue(':id', $get['modify'], PDO::PARAM_INT);
    $req->execute();
    $article = $req->fetch();
    if($article != false):
    ?>
<form method="post">
    <label for="title">Titre:</label>
    <input type="text" name="title" id="title" value="<?=$article['title'] ?>"><br>
    <label for="content">Contenu:</label><br>
    <textarea cols="150" rows="20" name="content" id="content"><?=nl2br($article['content']) ?></textarea><br>
    <input type="submit" value="Envoyer">
</form>

<?php
    else:
        echo '<p>Article introuvable</p>';
    endif;
endif; ?>


</body>
</html>
