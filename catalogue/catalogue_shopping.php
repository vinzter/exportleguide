<?php
if ($manufacturer_name=="") {
	$marque = $supplier_name;
} else {
	$marque = $manufacturer_name;
}
$desc = mb_substr($desc_produit, 0, 1000, 'UTF-8');
$desc = $desc . "...";
$MPN ='' ;
$isbn = '' ;
$shopping_cat_name = '' ;
$shopping_cat_name2 = '' ;
$desc_stock = mb_substr($desc_produit, 0, 50, 'UTF-8');
$puce1 = '' ;
$puce2 = '' ;
$puce3 = '' ;
$puce4 = '' ;
$puce5 = '' ;
$url_image_more = '' ;
$Soustype = '' ;
$Style = '' ;
$Etat = 'Neuf' ;
$Sexe = '' ;
$Section = '' ;
$age = '' ;
$Couleur = '' ;
$Matière = '' ;
$Format = '' ;
$Equipe = '' ;
$Ligue = '' ;
$Typeaccesup = '' ;
$Plateforme = '' ;
$Typesoft = '' ;
$displaytype = '' ;
$phonetype = '' ;
$Operateur = '' ;
$Typetarif = '' ;
$userprofile = '' ;
$Sizeunit = '' ;
$Longueur = '' ;
$Unitelongueur = '' ;
$Largeur = '' ;
$Unitelargeur = '' ;
$Hauteur = '' ;
$Unitehauteur = '' ;
$weightunit = 'kg' ;
$Disponibilité = 'Oui' ;
$Datenvoi = '' ;
$Prixpromo = '' ;
$Codeavantage = '' ;
$Descriptioncodeavantage = '' ;
$Typemarchandisage = '' ;
$Offregroupee = '' ;
$Personnalisation = '' ;
if ($displayCatalog) {
echo "
	<tr>
		<td>$id_product</td>
		<td>$MPN</td>
		<td>$ean13</td>
		<td>$isbn</td>
		<td>$marque</td>
		<td>$nom_produit</td>
		<td>$url_article</td>
		<td>$price_ttc_b</td>
		<td>$shopping_cat</td>
		<td>$shopping_cat_name</td>
		<td>$shopping_cat_name2</td>
		<td>$desc</td>
		<td>$desc_stock</td>
		<td>$puce1</td>
		<td>$puce2</td>
		<td>$puce3</td>
		<td>$puce4</td>
		<td>$puce5</td>
		<td>$url_image_b</td>
		<td>$url_image_more</td>
		<td>$categorie</td>
		<td>$Soustype</td>
		<td>$Style</td>
		<td>$Etat</td>
		<td>$Sexe</td>
		<td>$Section</td>
		<td>$age</td>
		<td>$Couleur</td>
		<td>$Matière</td>
		<td>$Format</td>
		<td>$Equipe</td>
		<td>$Ligue</td>
		<td>$Typeaccesup</td>
		<td>$Plateforme</td>
		<td>$Typesoft</td>
		<td>$displaytype</td>
		<td>$phonetype</td>
		<td>$Operateur</td>
		<td>$Typetarif</td>
		<td>$Userprofile</td>
		<td>$size</td>
		<td>$sizeunit</td>
		<td>$Longueur</td>
		<td>$Unitelongueur</td>
		<td>$Largeur</td>
		<td>$Unitelargeur</td>
		<td>$Hauteur</td>
		<td>$Unitehauteur</td>
		<td>$weight</td>
		<td>$weightunit</td>
		<td>$Disponibilité</td>
		<td>$delivery_price</td>
		<td>$Dateenvoi</td>
		<td>$Prixpromo</td>
		<td>$reduction_from</td>
		<td>$reduction_to</td>
		<td>$Codeavantage</td>
		<td>$Descriptioncodeavantage</td>
		<td>$Typemarchandisage</td>
		<td>$Offregroupee</td>
		<td>$Personnalisation</td>
	</tr>";
}
$sep = $separateur;
fwrite($fichier,
	$categorie . $sep .
	$id_product  . $sep .
	$MPN  . $sep .
	$ean13  . $sep .
	$isbn  . $sep .
	$marque  . $sep .
	$nom_produit  . $sep .
	$url_arrticle  . $sep .
	$prix  . $sep .
	$shopping_cat  . $sep .
	$shopping_cat_name  . $sep .
	$shopping_cat_name2  . $sep .
	$desc  . $sep .
	$desc_stock  . $sep .
	$puce1  . $sep .
	$puce2  . $sep .
	$puce3  . $sep .
	$puce4  . $sep .
	$puce5  . $sep .
	$url_image_b  . $sep .
	$url_image_more  . $sep .
	$categorie  . $sep .
	$Soustype  . $sep .
	$Style  . $sep .
	$Etat  . $sep .
	$Sexe  . $sep .
	$Section  . $sep .
	$age  . $sep .
	$Couleur  . $sep .
	$Matière  . $sep .
	$Format  . $sep .
	$Equipe  . $sep .
	$Ligue  . $sep .
	$Typeaccesup  . $sep .
	$Plateforme  . $sep .
	$Typesoft  . $sep .
	$displaytype  . $sep .
	$phonetype  . $sep .
	$Operateur  . $sep .
	$Typetarif  . $sep .
	$Userprofile  . $sep .
	$size  . $sep .
	$sizeunit  . $sep .
	$Longueur  . $sep .
	$Unitelongueur  . $sep .
	$Largeur  . $sep .
	$Unitelargeur  . $sep .
	$Hauteur  . $sep .
	$Unitehauteur  . $sep .
	$weight  . $sep .
	$weightunit  . $sep .
	$Disponibilité  . $sep .
	$delivery_price  . $sep .
	$Dateenvoi  . $sep .
	$Prixpromo  . $sep .
	$reduction_from  . $sep .
	$reduction_to  . $sep .
	$Codeavantage  . $sep .
	$Descriptioncodeavantage  . $sep .
	$Typemarchandisage  . $sep .
	$Offregroupee  . $sep .
	$Personnalisation . $sep .
	"\n");
?>