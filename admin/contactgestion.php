<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}
// Nettoyage
$get = array_map('cleandata', $_GET);

$error=array();
$valideMessage = false;

//Verification de l'existance de la variable $_SESSION['isconnected']
if(!isset($_SESSION['isconnected'])){
	$_SESSION['isconnected'] = false;
}

//L'utilisateur est-il connecté.
if ($_SESSION['isconnected'] == false) {
	header('Location: index.php');
	die;
}

$error=array();
$valideMessage = false;

$tailleDePagination = 10;

 // On définit la variable page à 1
if (!isset($get['page'])) {
	$get['page'] = 1;
}

if (isset($get['page'])) {
	if (is_numeric($get['page'])) {
		if ($get['page'] == 0 ) {
		$get ['page'] = 1;
		}
		$offset = ($get['page'] * $tailleDePagination ) - $tailleDePagination;
	}
	else {
	$offset = 0;
	}

	 // Requète avec l'ordre décroissant par date et limité à 10 par page
	$req = $pdo_database->prepare('SELECT * FROM contact ORDER BY date DESC LIMIT :size OFFSET :offset');
	$nbmessages_req = $pdo_database->prepare('SELECT COUNT(*) FROM contact');
	$req->bindValue(':size', $tailleDePagination, PDO::PARAM_INT);
	$req->bindValue(':offset', $offset, PDO::PARAM_INT);
	if ($req->execute()) {
		$nbmessages_req->execute();
		$nbmessages_tableau = $nbmessages_req->fetch();
		$nbmessages = $nbmessages_tableau[0];
		$message = $req->fetchAll(PDO::FETCH_ASSOC);
		if(count($message) == 0){
			$error[] = 'Aucun résultat';
		}
		else {
			$valideMessage = true;
		}
	}
	else {
        $error[] = 'Erreur avec la base de donnée.';
    }
}



?>
<!DOCTYPE html>
<html>
<head>
	<title>Gestion contact</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
	<?php
	include_once '../composants/barreadmin.php';
	echo '<main class="container">';
	echo '<h1>Messages Reçus :</h1>';
	if (count($error) == 0 ) {
		foreach ($message as $value) {
			$date = 'Le '.date('d/m/Y \à\ H:i', strtotime($value['date']));
			if ($value['checked']) {
				echo '<div class="lu">'; // Classe pour les message lu
			} else  {
				echo '<div>';
			}
			echo '<p><strong>Date : </strong>'.$date.'</p>';
			echo '<p><strong>Email : </strong>'.$value['email'].'</p>';
			echo '<p><strong>Sujet : </strong>'.$value['subject'].'</p><br>';
		    echo '<p><strong>Message :</strong></p>';
			echo '<p>'.nl2br($value['message']).'</p>';
			echo '</div>';
			echo '<hr />';
		}
		if ($offset > 0 ) {
			$prev = $get['page'] - 1;
			echo '<p><a href="contactgestion.php?page='.$prev.'">Page précédente</a></p><br>';
		}
		if($offset + $tailleDePagination < $nbmessages){
			$next = $get['page'] + 1;
			echo '<p><a href="contactgestion.php?page='.$next.'">Page suivante</a></p><br>';
		}
	}

	if (count($error) > 0) {
		echo '<p>'.implode(' ', $error).'</p>';
	}
	?>
	</main>
</body>
</html>
