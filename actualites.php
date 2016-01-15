<?php
session_start();
require_once'composants/db.php';

function cleandata($data){
    return trim(htmlentities($data));
}

//Clean des input $_GET
$get = array_map('cleandata', $_GET);

$errors = array();
$validArticles = false;

$tailleDePagination = 10;

//Page 1 par défaut.
if(!isset($get['page'])){
    $get['page'] = 1;
}

//Validation de l'input de page
if(isset($get['page'])){
    if(is_numeric($get['page'])){
        if($get['page'] == 0){
            $get['page'] = 1;
        }
        $offset = ($get['page'] * $tailleDePagination) - $tailleDePagination;
    } else {
        $offset = 0;
    }

    //Requête spécifique a la recherche
    if(isset($get['search'])){
        $nbarticles_req = $pdo_database->prepare('SELECT COUNT(*) FROM articles WHERE content LIKE :recherche');
        $req = $pdo_database->prepare('SELECT * FROM articles WHERE content LIKE :recherche ORDER BY date DESC LIMIT :size OFFSET :offset');
        $req->bindValue(':recherche', '%'.$get['search'].'%', PDO::PARAM_STR);
        $nbarticles_req->bindValue(':recherche', '%'.$get['search'].'%', PDO::PARAM_STR);
    } else {
        //Requête sans la recherche
        $req = $pdo_database->prepare('SELECT * FROM articles ORDER BY date DESC LIMIT :size OFFSET :offset');
        $nbarticles_req = $pdo_database->prepare('SELECT COUNT(*) FROM articles');
    }
    $req->bindValue(':size', $tailleDePagination, PDO::PARAM_INT);
    $req->bindValue(':offset', $offset, PDO::PARAM_INT);
    if($req->execute()){
        $nbarticles_req->execute();
        $nbarticles = $nbarticles_req->fetchColumn();
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
            <h1>News</h1>
        <?php if($validArticles): ?>
            <div id="blocNews">
                <div class="recherche">
                    <form method="get">
                        <?php
                            //Sauvegarde la recherche de l'utilisateur quand on affiche le résultat
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
                <?php //Création des liens page suivante et page précédente.
                    //Affichage des pages
                    echo '<p>';
                    $nbpages = ceil($nbarticles/$tailleDePagination);
                    for($i=1; $i <= $nbpages; $i++){
                        if(isset($get['search'])){ //Lien avec paramètre de recherche
                            echo '<a href="actualites.php?page='.$i.'&search='.$get['search'].'">'.$i.'</a>';
                        } else { //Lien sans paramètre de recherche
                            echo '<a href="actualites.php?page='.$i.'">'.$i.'</a>';
                        }
                    }
                    echo '</p>';

                    if($offset>0){
                        $prev = $get['page'] - 1;
                        if(isset($get['search'])){ //Lien avec paramètre de recherche
                            echo '<p><a href="actualites.php?page='.$prev.'&search='.$get['search'].'">Page précédente</a></p>';
                        } else { //Lien sans paramètre de recherche
                            echo '<p><a href="actualites.php?page='.$prev.'">Page précédente</a></p>';
                        }
                    }

                    //Affiche la page suivante uniquement si la page suivante contient des articles ayant un offset inferieur au nombre maximal
                    if($offset + $tailleDePagination < $nbarticles){
                        $next = $get['page'] + 1;
                        if(isset($get['search'])){ //Lien avec paramètre de recherche
                            echo '<p><a href="actualites.php?page='.$next.'&search='.$get['search'].'">Page suivante</a></p>';
                        } else { //Lien sans paramètre de recherche
                            echo '<p><a href="actualites.php?page='.$next.'">Page suivante</a></p>';
                        }
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
