<?php
if ($displayCatalog) {
	echo"
	<tr>
		<td>categorie</td>
		<td>identifiant_unique</td>
		<td>titre</td>
		<td>description</td>
		<td>prix</td>
		<td>url_produit</td>
		<td>url_image</td>
		<td>frais de livraison</td>
		<td>disponibilite</td>
		<td>delai de livraison</td>
		<td>garantie</td>
		<td>reference_modele</td>
		<td>D3E</td>
		<td>marque</td>
		<td>ean</td>
		<td>prix_barre</td>
		<td>type_promotion</td>
		<td>devise</td>
		<td>occasion</td>
		<td>url_mobile</td>
	</tr>
	";
}

$sep = $separateur;

fwrite($fichier, "categorie" . $sep . 
	"identifiant_unique" . $sep . 
	"titre" . $sep . 
	"description" . $sep . 
	"prix" . $sep . 
	"url_produit" . $sep . 
	"url_image" . $sep . 
	"frais de livraison" . $sep . 
	"disponibilite" . $sep . 
	"delai de livraison" . $sep . 
	"garantie" . $sep . 
	"reference_modele" . $sep . 
	"D3E" . $sep . 
	"marque" . $sep . 
	"ean" . $sep . 
	"prix_barre" . $sep . 
	"type_promotion" . $sep . 
	"devise" . $sep . 
	"occasion" . $sep . 
	"url_mobile" . $sep . 
	"\n");

?>