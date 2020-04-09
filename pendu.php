<?php 
	session_start();

	require 'functions_pendu.php';

	if(isset($_POST['new_round'])) {
		unset($_SESSION);
		session_destroy();
		session_start();
	}
	if(!isset($_SESSION['liste_numeros_lignes_utilises'])) $_SESSION['liste_numeros_lignes_utilises'] = [];
	
	$liste_numeros_lignes_utilises = [];
	$liste_numeros_lignes_utilises = $_SESSION['liste_numeros_lignes_utilises'];


	$fichier_dictionary = __DIR__ . DIRECTORY_SEPARATOR . 'dictionary.txt';
	$ressource_dictionary = fopen($fichier_dictionary, 'r'); // Ouvrir le fichier 'dictionary.txt'

	$fichier_mot_secret = __DIR__ . DIRECTORY_SEPARATOR . 'mot_secret.txt';
	$ressource_mot_secret = fopen($fichier_mot_secret, 'r');

	if(!isset($_SESSION['mot_secret'])) {
		$nb_ligne = compter_nb_lignes($ressource_dictionary); // compter le nb de mots existant dans 'dictionary.txt'

		do {
			$mot_secret_trouve = false;
			$numero_ligne_mot_secret = mt_rand(1, $nb_ligne);
			
			if(!si_numero_ligne_deja_stocke($liste_numeros_lignes_utilises, $numero_ligne_mot_secret)) {
				$liste_numeros_lignes_utilises = stocker_numeros_ligne_utilises($liste_numeros_lignes_utilises, $numero_ligne_mot_secret);
				// On update la session liste des numéros utilisés
				$_SESSION['liste_numeros_lignes_utilises'] = $liste_numeros_lignes_utilises;

				$mot_secret_trouve = true;
			}
			
			$_SESSION['mot_secret'] = trouver_mot_secret($ressource_dictionary, $numero_ligne_mot_secret);
		} while(!$mot_secret_trouve);
	}
	if(isset($_SESSION['mot_secret'])) {
		ecrire_le_mot_secret_dans_un_fichier($fichier_mot_secret, $_SESSION['mot_secret']);

		$_SESSION['array_lettres_mot'] = [];
		$_SESSION['array_lettres_mot'] = mettre_les_lettres_in_array($ressource_mot_secret, strlen($_SESSION['mot_secret']));

		if(!isset($_SESSION['mot_secret_etoile'])) {
			$_SESSION['mot_secret_etoile'] = [];
			
			$_SESSION['mot_secret_etoile'] = etoiler_array_mot_secret($_SESSION['mot_secret_etoile'], strlen($_SESSION['mot_secret']));
		}
	}

	if(!empty($_POST['lettre'])) {
		if(strlen($_POST['lettre']) == 1) {
			if(preg_match('#^[a-zA-Z]$#', $_POST['lettre']))	{
				$partie_finie = false;
				if(!isset($_SESSION['tentative_restante'])) $_SESSION['tentative_restante'] = 10;
				$tentative_restante = $_SESSION['tentative_restante'];

				$caractere_entre = strtolower($_POST['lettre']);
				$une_saisie_reussie = false;

				if(si_caractere_dans_mot_secret($_SESSION['array_lettres_mot'], $caractere_entre))
				{
					$une_saisie_reussie = true;

					$_SESSION['mot_secret_etoile'] = remplacer_etoile_par_caractere($_SESSION['array_lettres_mot'], strlen($_SESSION['mot_secret']), $_SESSION['mot_secret_etoile'], $caractere_entre);
					
					$afficher_resultat_tentative = 'BRAVO ! "<strong>' .strtoupper($caractere_entre). '</strong>" fait bien partie du mot secret !<br/>
						Votre nombre de tentatives restantes ne bouge pas, c\'est ' .$tentative_restante;
				}
				else {
					$_SESSION['tentative_restante']--;
					$tentative_restante = $_SESSION['tentative_restante'];
					$afficher_resultat_tentative = 'DESOLE ! La lettre "<strong>' .strtoupper($caractere_entre). '</strong>" n\'est pas dans le mot secret !<br/>
						Il vous reste ' .$tentative_restante. ' tentatives pour trouver ce mot !';
					}

				if($_SESSION['mot_secret_etoile'] == $_SESSION['array_lettres_mot']) $partie_finie = true;
				

				if($partie_finie) {
					$succes = true;
					$afficher_succes_partie = 'BRAVO baby, vous avez trouvé le mot secret, c\'était : <br/> <strong class="text-success px-3 h4">\'' .strtoupper($_SESSION['mot_secret']). '\'</strong>.';
				}

				if($tentative_restante <= 0) {
					$game_over = true;
					$afficher_game_over = 'GAME OVER !<br/>' . 
					 'Il ne vous reste plus de tentative ! Dommage, j\'aurais aimé que vous réussissiez :( :D <br/>' .
					 'Le mot secret était : <stron>' .strtoupper($_SESSION['mot_secret']) .'</strong>';
				}
			}

			else $afficher_incident = 'UNE LETTRE et NON ACCENTUE svp !';
		}
		else $afficher_incident = 'UNE SEULE LETTRE SVP !';
			
	} else /*$afficher_incident = 'ERROR : Veuillez saisir une lettre svp, UNE SEULE, et non accentué !';*/;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Jeu de PENDU</title>
	<link rel="stylesheet" type="text/css" href="bootstrap.min.css"/>
</head>
<body class="bg-dark text-light">

	<div class="container mt-3">
		<div class="row">
			<h1 class="col-6 offset-3 py-3 d-flex justify-content-center bg-primary">Jeu de PENDU : </h1>
		</div>

		<div class="row">
			<?php require 'consignes.php'; ?>

			<div class="col-6 pl-5">
				<p class="h3 d-flex justify-content-center text-danger my-3">ICI LE DEROULEMENT DU JEU</p>
				
				<?php if(isset($succes) or isset($game_over)) : ?>
					<?php if(isset($succes)) : ?>
						<p class="alert alert-success"><?= $afficher_succes_partie ?></p>
						<div class="row">
							<form class="col-6 offset-3" method="post">
								<button type="submit" name="new_round" class="btn btn-success form-control h3">Une nouvelle partie ?</button>
							</form>
						</div>
					<?php elseif(isset($game_over)) : ?>
						<p class="alert alert-danger"><?= $afficher_game_over ?></p>
						<div class="row">
							<form class="col-6 offset-3" method="post">
								<button type="submit" name="new_round" class="btn btn-success form-control h3">Une nouvelle partie ?</button>
							</form>
						</div>
					<?php endif; ?>


				<?php else : ?>

					<?php if(!isset($afficher_succes_partie)) : ?>
						<p>
							<strong class="text-warning">INDICE : </strong><br/>
							Voici le mot secret pour l'instant :D (Au total : <?php echo strlen($_SESSION['mot_secret']) - 2; ?> lettres) : 
							<strong class="text-success px-3 h4" style="position: relative; top: 5px;">
								<br/><?php afficher_mot_secret_etoile($_SESSION['mot_secret_etoile']); ?>
							</strong> <br/>
							Bonne chance à vous de le découvrir en entier !
						</p>

						<?php if(isset($afficher_resultat_tentative)) : ?>
							<p class="alert alert-warning text-info"><?= $afficher_resultat_tentative ?></p>
						<?php endif; ?>
						
						<?php if(isset($afficher_incident)): ?>
							<p class="alert alert-danger">
								<?php echo $afficher_incident; ?>
							</p>
						<?php endif ; ?>
						
						<?php require 'form.php'; ?>

						<?php if(isset($afficher_tentative_echoue)) : ?>
							<p class="alert alert-danger"><?= $afficher_tentative_echoue ?></p>
						<?php endif; ?>

					<?php else : ?>
						<p class="alert alert-success">
							<?= $afficher_succes_partie ?>
						</p>			
						<div class="row">
							<form class="col-6 offset-3" method="post">
								<button type="submit" name="new_round" class="btn btn-success form-control h3">Une nouvelle partie ?</button>
							</form>
						</div>
					<?php endif; ?>
				<?php endif; ?>

			</div>

		</div>

	</div>

</body>
</html>

<?php 
	fclose($ressource_dictionary); // fermer le fichier
	fclose($ressource_mot_secret);
?>
