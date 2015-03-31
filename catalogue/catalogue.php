<?php
if ($manufacturer_name==""){
	$marque = $supplier_name;
} else {
	$marque = $manufacturer_name;
}
if ($displayCatalog) {
	echo "
	<tr>
		<td>$category_name</td>
		<td>$id_product</td>
		<td>$nom_produit </td>
		<td>$desc_produit</td>
		<td>$price_ttc_b</td>
		<td>$url_article</td>
		<td>$url_image_b</td>
		<td>$delivery_price</td>
		<td>$disponibilite</td>
		<td>$delai_livraison</td>
		<td>$garantie</td>
		<td>$reference</td>
		<td>0</td>
		<td>$marque</td>
		<td>$ean13</td>
		<td>$price_barred_b</td>
		<td>&nbsp;</td>
		<td>$devise</td>
		<td>$etat</td>
		<td>&nbsp;</td>
	</tr>";
}
$sep = $separateur;
fwrite( $fichier,
	$category_name . $sep . 
	$id_product . $sep . 
	$nom_produit . $sep . 
	$desc_produit . $sep . 
	$price_ttc_b . $sep . 
	$url_article . $sep . 
	$url_image_b . $sep . 
	$delivery_price . $sep . 
	$disponibilite . $sep . 
	$delai_livraison . $sep . 
	$garantie . $sep . 
	$reference . $sep . 
	"0" . $sep . 
	$marque . $sep . 
	$ean13 . $sep . 
	$price_barred_b . $sep . 
	$sep . 
	$devise . $sep . 
	$etat . $sep . 
	"\n" );
?>