<?php
//include('fonctions.php');

error_reporting(0);
$nom_fichier = basename($_GET['file']);
$dossier = "./exports/";

 if (($nom_fichier != "") && (file_exists( $dossier. $nom_fichier))) {
    $fichier = $dossier.$nom_fichier;
    $taille_fichier = filesize($fichier);
    //forcerTelechargement($nom_fichier, $fichier, $taille_fichier);
		header('Content-Type: application/octet-stream');
		header('Content-Length: '. $taille_fichier);
		header('Content-disposition: attachment; filename='. $nom_fichier);
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');
		readfile($fichier);
    } else {
		echo "<big><b>No files !</b></big>";
	}

?>