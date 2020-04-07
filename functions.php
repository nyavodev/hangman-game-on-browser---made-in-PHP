<?php

function nouvelle_partie() :bool
{
	do {
		$reponse = readline('Une nouvelle partie ? (1 pour OUI / 2 pour NON) => ');
		if($reponse == 1) return true;
		elseif($reponse == 2) return false;
		else echo "\nERROR : Réponse invalide !\n\n";
	} while($reponse != 1 and $reponse != 2);
}

function compter_nb_lignes($fichier) :int
{
	$nb_ligne = 0;
	while($line = fgets($fichier)) {
		$nb_ligne++;
	}
	rewind($fichier);
	return $nb_ligne;
}

function trouver_mot_secret($fichier, int $numero_ligne) :string
{
	for($compteur = 1; $compteur <= $numero_ligne; $compteur++) {
		$mot = fgets($fichier);
	}
	rewind($fichier);
	return $mot;
}

function moyenne_selon_mot_secret(string $mot_secret) :int
{
	$moyenne = round(strlen($mot_secret) * 3 / 2);
	return $moyenne;
}

function ecrire_le_mot_secret_dans_un_fichier($path, $mot_secret)
{
	file_put_contents($path, $mot_secret);
}

function mettre_les_lettres_in_array($fichier, int $long_mot_secret) :array
{
	$lettres_mot = [];
	for($i = 0; $i < $long_mot_secret - 2; $i++) {
		$lettres_mot[$i] = fgetc($fichier);
	}
	return $lettres_mot;
}

function etoiler_array_mot_secret(array $array_mot_secret, int $long_mot_secret) :array
{
	for ($i = 0; $i < $long_mot_secret - 2; $i++) {
		$array_mot_secret[$i] = '*';
	}
	return $array_mot_secret;
}

function lire_une_lettre() :string
{
	$lire = false;
	do {
		$caractere = readline('Veuillez entrer une lettre : ');
		if(strlen($caractere) == 0) echo 'Veuillez entrer une lettre please !' . "\n";
		elseif(strlen($caractere) > 1) echo 'Une seule lettre please, ET non accentué !' . "\n";
		else {
			if(preg_match('#^[^a-z^A-Z]$#', $caractere)) echo 'Quand je dis \'LETTRE\', c\'est LETTRE, pas autre !' ."\n";
			else $lire = true;
		}
	} while(!$lire);

	if($lire) return strtolower($caractere);
}

function si_caractere_dans_mot_secret(array $array_lettres_ms, string $caractere) :bool
{
	if(in_array($caractere, $array_lettres_ms)) return true;
	return false;
}

function remplacer_etoile_par_caractere(array $array_lettres_ms, int $long_mot_secret, array $array_mot_secret_etoile, string $caractere)
{
	for($i = 0; $i < $long_mot_secret - 2; $i++) {
		if($array_lettres_ms[$i] === $caractere) $array_mot_secret_etoile[$i] = $caractere;
	}
	return $array_mot_secret_etoile;
}

function afficher_mot_secret_etoile(array $array_mot_secret_etoile) 
{
	for($i = 0; $i < count($array_mot_secret_etoile); $i++) {
		echo strtoupper($array_mot_secret_etoile[$i]);
	}
}

function si_numero_ligne_deja_stocke(array $liste, int $numero_ligne) :bool
{
	if(count($liste) > 0) {
		for($i = 0; $i < count($liste); $i++) {
			if($liste[$i] == $numero_ligne) return true;
		}
	}
	return false;
}

function stocker_numeros_ligne_utilises(array $liste_numeros_ligne_utilises, $numero_ligne_a_stocker)
{
	$long_liste = count($liste_numeros_ligne_utilises);
	$liste_numeros_ligne_utilises[$long_liste] = $numero_ligne_a_stocker;
	return $liste_numeros_ligne_utilises;
}

