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
		<td>Catégorie </td>
		<td>Fabricant </td>
		<td>Titre</td>
		<td>Description</td>
		<td>Lien</td>
		<td>image</td>
		<td>SKU</td>
		<td>Stock</td>
		<td>Condition</td>
		<td>Poids</td>
		<td>Frais de livraison</td>
		<td>Enchère</td>
		<td>Offre spéciale</td>
		<td>EAN/UPC</td>
		<td>Prix</td>
	</tr>
	";
}

$sep = $separateur;
	
fwrite($fichier,  
	"Catégorie" . $sep .
	"Fabricant" . $sep . 
	"Titre" . $sep . 
	"Descriptiont" . $sep . 
	"Lien" . $sep . 
	"image" . $sep . 
	"SKU" . $sep . 
	"Stock" . $sep . 
	"Condition" . $sep . 
	"Poids" . $sep . 
	"Frais de livraison" . $sep . 
	"Enchère" . $sep . 
	"Offre spéciale" . $sep .
	"EAN/UPC" . $sep .
	"Prix" . $sep .	
	"\n");

?>