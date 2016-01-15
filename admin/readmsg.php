<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}
// Nettoyage
$get = array_map('cleandata', $_GET);

//Verification de l'existance de la variable $_SESSION['isconnected']
if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}

//L'utilisateur est-il connecté.
if ($_SESSION['isconnected'] == false) {
	die;
}

if(isset($get['checked']) && isset($get['article'])){
    if($get['checked'] == 'lu'){
        $req = $pdo_database->prepare('UPDATE contact SET checked = TRUE WHERE id = :id');
        $req->bindValue(':id', $get['article'], PDO::PARAM_INT);
        if($req->execute()){
            echo 'True';
        } else {
            echo 'False';
        }
    } elseif($get['checked'] == 'nonlu'){
        $req = $pdo_database->prepare('UPDATE contact SET checked = FALSE WHERE id = :id');
        $req->bindValue(':id', $get['article'], PDO::PARAM_INT);
        if($req->execute()){
            echo 'True';
        } else {
            echo 'False';
        }
    }
}

?>
