<?php

// pour l'affichage en français

if($_SESSION['role'] == 'editor'){
	$role = 'éditeur';
}
else {
	$role = 'administrateur';
}

echo '<div><p>bonjour '.$_SESSION['username'].' vous êtes connecté en tant que '.$role.'<p><div>';
?>
<nav>
	<ul>
		<li>Créer un nouvel article</li>
		<li>Consulter/Modifier un article</li>
		
		<?php if ($_SESSION['role'] == 'admin'): // réservé à l'admin ?>

		<li>Créer un nouvel utilisateur</li>
		<li>Voir le liste des utilisateurs</li>
		<li>Modifier les options du site</li>

		<?php endif; ?>
		
		<li>Modifier votre mot de passe</li>
		<li>Se deconnecter</li>
	</ul>
</nav>
