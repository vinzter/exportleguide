<?php
/*
Hubert BENETEAU - 14/12/2009
http://hb50.fr
ajout de l'export vers le comparateur Shoppydoo
*/

if ($displayCatalog) {	
	echo"
	<tr>
		<td>nom_produit </td>
		<td>marque </td>
		<td>desc</td>
		<td>price_ttc</td>
		<td>ean13</td>
		<td>id_product</td>
		<td>disponibilite</td>
		<td>url_article</td>
		<td>category</td>
		<td>url_image</td>
		<td>delivery_price</td>
	</tr>
	";
}

$sep = $separateur;

fwrite($fichier, "url" . $sep . 
	"nom_produit" . $sep . 
	"marque" . $sep . 
	"desc" . $sep . 
	"price_ttc" . $sep . 
	"ean13" . $sep . 
	"id_product" . $sep . 
	"offerID" . $sep . 
	"disponibilite" . $sep . 
	"url_article" . $sep . 
	"category" . $sep . 
	"url_image" . $sep . 
	"delivery_price" . 
	"\n");

?>