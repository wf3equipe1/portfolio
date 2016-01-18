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
				echo '<article class="lu">'; // Classe pour les message lu
			} else  {
				echo '<article>';
			}
			echo '<span><strong>Envoyé : </strong>'.$date.'</span>';
			echo '<span><strong>Par : </strong>'.$value['email'].'</span>';
			echo '<span><strong>Sujet : </strong>'.$value['subject'].'</span><br>';
			echo '<p id="message"><strong>Message :</strong> '.nl2br($value['message']).'</p>';

			?>
			<form action="<?=$value['id'] ?>">
			<input type="radio" class="readmsg" name="checked" value="lu" <?php if($value['checked']){ echo "checked";} ?>> LU
			<input type="radio" class="readmsg" name="checked" value="nonlu" <?php if(!$value['checked']){ echo "checked";} ?>> NON LU
			</form>
			<?php
			echo '</article>';
			echo '<hr />';
		}
		?>
		<div class="pagination">
		<?php
		if ($offset > 0 ) {
			$prev = $get['page'] - 1;
			echo '<p><a href="contactgestion.php?page='.$prev.'">Page précédente</a></p><br>';
		}
		if($offset + $tailleDePagination < $nbmessages){
			$next = $get['page'] + 1;
			echo '<p><a href="contactgestion.php?page='.$next.'">Page suivante</a></p><br>';
		}
		?></div><?php
	}

	if (count($error) > 0) {
		echo '<p>'.implode(' ', $error).'</p>';
	}
	?>
    <script src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="../js/jquery.flexslider.js"></script>
    <script src="../js/main.js"></script>
	</main>
</body>
</html>
