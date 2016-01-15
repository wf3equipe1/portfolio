<?php

// pour l'affichage en français

if($_SESSION['isconnected']):

	if($_SESSION['role'] == 'editor'){
		$role = 'éditeur';
	}
	else {
		$role = 'administrateur';
	}
	?>
	<header>
		<div class="container">
			<p>Bonjour <span><?php echo $_SESSION['username']?></span>, vous êtes connecté en tant qu'<span><?php echo $role?></span></p>
		</div>
		<hr />
		<nav class="container">
			<ul>
				<li><a href="nouvelarticle.php">Nouvel article</a></li>
				<li><a href="actualites.php">Modifier article</a></li>
				
				<?php if ($_SESSION['role'] == 'admin'): // réservé à l'admin ?>

				<li><a href="contactgestion.php">Messages</a></li>
				<li><a href="nouvelutilisateur.php">Nouvel utilisateur</a></li>
				<li><a href="listeutilisateurs.php">Utilisateurs</a></li>
				<li><a href="options.php">Options</a></li>

				<?php endif; ?>
				
				<li><a href="motdepasse.php">Modifier mot de passe</a></li>
				<li><a href="index.php?logout">Deconnecter</a></li>
			</ul>
		</nav>
	</header>
<?php endif; ?>