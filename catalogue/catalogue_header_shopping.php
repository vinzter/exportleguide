<?php
/*
Daniel JOURNO - 14/12/2009
ajout de l'export vers le comparateur Shopzilla
*/

/* mandatory fields
url
Title
offerID
description
price
deliverycost
image
availability
*/		
	
if ($displayCatalog) {
	echo "
	<tr>
		<td>Numéro d'article stock marchand</td>
		<td>MPN</td>
		<td>EAN</td>
		<td>ISBN</td>
		<td>Fabricant</td>
		<td>Nom du produit</td>
		<td>URL du produit</td>
		<td>Prix</td>
		<td>Numéro de la catégorie</td>
		<td>Nom de la catégorie</td>
		<td>Nom de la sous catégorie</td>
		<td>Description du produit</td>
		<td>Description du stock</td>
		<td>Puce produit 1</td>
		<td>Puce produit 2</td>
		<td>Puce produit 3</td>
		<td>Puce produit 4</td>
		<td>Puce produit 5</td>
		<td>URL de l'image</td>
		<td>autres URL d'images</td>
		<td>Type de produit</td>
		<td>Sous-type</td>
		<td>Style</td>
		<td>Etat</td>
		<td>Sexe</td>
		<td>Section</td>
		<td>Tranche d'âge</td>
		<td>Couleur</td>
		<td>Matière</td>
		<td>Format</td>
		<td>Equipe</td>
		<td>Ligue</td>
		<td>Type d'accessoire pour supporters</td>
		<td>Plate-forme</td>
		<td>Type de logiciel</td>
		<td>Type d'affichage</td>
		<td>Type de téléphone</td>
		<td>Opérateur</td>
		<td>Type de formule tarifaire</td>
		<td>Profil de l'utilisateur</td>
		<td>Taille</td>
		<td>Unité de mesure de la taille</td>
		<td>Longueur du produit</td>
		<td>Unité de mesure de la longueur</td>
		<td>Largeur du produit </td>
		<td>Unité de mesure de la largeur</td>
		<td>Hauteur du produit</td>
		<td>Unité de mesure de la hauteur</td>
		<td>Poids du produit</td>
		<td>Unité de mesure du poids</td>
		<td>Disponibilité</td>
		<td>Frais de port</td>
		<td>Date d'envoi estimée</td>
		<td>Prix promotionnel</td>
		<td>Date de début de la promotion</td>
		<td>Date de fin de la promotion</td>
		<td>Code avantage</td>
		<td>Description du code avantage</td>
		<td>Type de marchandisage</td>
		<td>Offre groupée</td>
		<td>Personnalisation</td>
	</tr>
	";
}

$sep = $separateur;
	
fwrite($fichier,  
	"Numéro d'article stock marchand" . $sep .
	"MPN" . $sep .
	"EAN" . $sep .
	"ISBN" . $sep .
	"Fabricant" . $sep .
	"Nom du produit" . $sep .
	"URL du produit" . $sep .
	"Prix" . $sep .
	"Numéro de la catégorie" . $sep .
	"Nom de la catégorie" . $sep .
	"Nom de la sous catégorie" . $sep .
	"Description du produit" . $sep .
	"Description du stock" . $sep .
	"Puce produit 1" . $sep .
	"Puce produit 2" . $sep .
	"Puce produit 3" . $sep .
	"Puce produit 4" . $sep .
	"Puce produit 5" . $sep .
	"URL de l'image" . $sep .
	"autres URL d'images" . $sep .
	"Type de produit" . $sep .
	"Sous-type" . $sep .
	"Style" . $sep .
	"Etat" . $sep .
	"Sexe" . $sep .
	"Section" . $sep .
	"Tranche d'âge" . $sep .
	"Couleur" . $sep .
	"Matière" . $sep .
	"Format" . $sep .
	"Equipe" . $sep .
	"Ligue" . $sep .
	"Type d'accessoire pour supporters" . $sep .
	"Plate-forme" . $sep .
	"Type de logiciel" . $sep .
	"Type d'affichage" . $sep .
	"Type de téléphone" . $sep .
	"Opérateur" . $sep .
	"Type de formule tarifaire" . $sep .
	"Profil de l'utilisateur" . $sep .
	"Taille" . $sep .
	"Unité de mesure de la taille" . $sep .
	"Longueur du produit" . $sep .
	"Unité de mesure de la longueur" . $sep .
	"Largeur du produit " . $sep .
	"Unité de mesure de la largeur" . $sep .
	"Hauteur du produit" . $sep .
	"Unité de mesure de la hauteur" . $sep .
	"Poids du produit" . $sep .
	"Unité de mesure du poids" . $sep .
	"Disponibilité" . $sep .
	"Frais de port" . $sep .
	"Date d'envoi estimée" . $sep .
	"Prix promotionnel" . $sep .
	"Date de début de la promotion" . $sep .
	"Date de fin de la promotion" . $sep .
	"Code avantage" . $sep .
	"Description du code avantage" . $sep .
	"Type de marchandisage" . $sep .
	"Offre groupée" . $sep .
	"Personnalisation" . $sep .
	"\n");

?>