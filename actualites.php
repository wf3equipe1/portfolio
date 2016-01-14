<?php
session_start();
require_once'composants/db.php';

function cleandata($data){
    return trim(htmlentities($data));
}

$get = array_map('cleandata', $_GET);

$errors = array();
$validArticles = false;

if(!isset($get['page'])){
    $get['page'] = 1;
}

if(isset($get['page'])){
    if(is_numeric($get['page'])){
        if($get['page'] == 0){
            $get['page'] = 1;
        }
        $offset = ($get['page'] * 10) - 10;
    } else {
        $offset = 0;
    }

    if(isset($get['search'])){
        $req = $pdo_database->prepare('SELECT * FROM articles WHERE content LIKE :recherche ORDER BY date DESC LIMIT 10 OFFSET :offset');
        $req->bindValue(':recherche', '%'.$get['search'].'%', PDO::PARAM_STR);
    } else {
        $req = $pdo_database->prepare('SELECT * FROM articles ORDER BY date DESC LIMIT 10 OFFSET :offset');
    }
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
                <div class="recherche">
                    <form method="get">
                        <?php
                            if(isset($get['search'])){
                                $placeholder_search = $get['search'];
                            } else {
                                $placeholder_search = '';
                            }

                         ?>
                        <input type="text" id="search" name="search" placeholder="Recherche" value="<?=$placeholder_search ?>">
                        <input type="submit" value="Rechercher">
                    </form>
                </div>
                <?php foreach($articles as $article): ?>
                <article>
                    <h3><?= $article['title']?></h3>
                    <?php
                        if(isset($get['search'])){
                            echo '<p>'.preg_replace('/'.$get["search"].'/i', '<strong style="color:red;">$0</strong>' , nl2br($article["content"])).'</p>';
                        } else {
                            echo '<p>'.nl2br($article['content']).'</p>';
                        }
                     ?>

                </article>
                <?php endforeach;?>
                <?php
                    if($offset>0){
                        $prev = $get['page'] - 1;
                        if(isset($get['search'])){
                            echo '<p><a href="actualites.php?page='.$prev.'&search='.$get['search'].'">Page précédente</a></p>';
                        } else {
                            echo '<p><a href="actualites.php?page='.$prev.'">Page précédente</a></p>';
                        }
                    }
                    $next = $get['page'] + 1;
                    if(isset($get['search'])){
                        echo '<p><a href="actualites.php?page='.$next.'&search='.$get['search'].'">Page suivante</a></p>';
                    } else {
                        echo '<p><a href="actualites.php?page='.$next.'">Page suivante</a></p>';
                    }

                 ?>
            </div>
            <?php elseif(count($errors)>0): ?>
            <div id="blocNews">

                <p><?= implode(' ', $errors) ?></p>
                <br />
                <a href="actualites.php">Retour</a>
            </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
