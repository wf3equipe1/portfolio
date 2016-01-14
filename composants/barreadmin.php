<?php

// pour l'affichage en français



if($_SESSION['role'] == 'editor'){
	$role = 'éditeur';
}
else {
	$role = 'administrateur';
}

echo '<div><p>bonjour '.$_SESSION['username'].' vous êtes connecté en tant qu\''.$role.'<p><div>';
?>
<nav>
	<ul>
		<li><a href="nouvelarticle.php">Créer un nouvel article</a></li>
		<li><a href="actualites.php">Consulter/Modifier un article</a></li>
		
		<?php if ($_SESSION['role'] == 'admin'): // réservé à l'admin ?>

		<li><a href="contactgestion.php">Afficher les messages</a></li>
		<li><a href="nouvelutilisateur.php">Créer un nouvel utilisateur</a></li>
		<li><a href="listeutilisateurs.php">Voir la liste des utilisateurs</a></li>
		<li><a href="options.php">Modifier les options du site</a></li>

		<?php endif; ?>
		
		<li><a href="motdepasse">Modifier votre mot de passe</a></li>
		<li><a href="index.php?logout">Se deconnecter</a></li>
	</ul>
</nav>
