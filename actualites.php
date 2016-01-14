<?php
session_start();
require_once'composants/db.php';

function cleandata($data){
    return trim(htmlentities($data));
}

$get = array_map('cleandata', $_GET);

$errors = array();
$validArticles = false;

if(count($get)==0){
    $get['page'] = 0;
}

if(isset($get['page'])){
    if(is_numeric($get['page'])){
        $offset = $get['page'] * 10;
    } else {
        $offset = 0;
    }

    $req = $pdo_database->prepare('SELECT * FROM articles ORDER BY date DESC LIMIT 10 OFFSET :offset');
    $req->bindValue(':offset', $offset, PDO::PARAM_INT);
    if($req->execute()){
        $articles = $req->fetchAll();
        if(count($articles)==0){
            $errors[] = 'Aucun résultat.';
        } else {
            $validArticles = true;
        }
    } else {
        $errors[] = 'Erreur avec la base de donnée.';
    }
}






?>

<!DOCTYPE html>
<html>
<head>
   <title>Page principale</title>
   <meta charset="utf-8">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
    <main>
       <?php include_once 'composants/menugauche.php'; //MENU DE GAUCHE?>
        <section id="rightSide">
        <?php if($validArticles): ?>
            <div id="blocNews">
            <?php foreach($articles as $article): ?>
                <article>
                    <h3><?= $article['title']?></h3>
                    <p><?= $article['content']?></p>
                </article>
                <?php
                    if($offset>0){
                        echo '<a href="actualites.php?page=';
                        echo $offset-1;
                        echo '">Page précédente</a>';
                    }
                    echo '<a href="actualites.php?page=';
                    echo $offset+1;
                    echo '">Page suivante</a>';
                 ?>
            <?php endforeach;?>
            </div>
            <?php elseif(count($errors)>0): ?>
            <div id="blocNews">

                <p><?= implode(' ', $errors) ?></p>
                <a href="actualites.php">Retour</a>
            </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
