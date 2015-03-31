<?php
if ($manufacturer_name=="") {
	$marque = $supplier_name;
} else {
	$marque = $manufacturer_name;
}
if ($displayCatalog) {
	echo "
	<tr>
		<td>$category_name</td>		<td>$id_product_attribute</td>
		<td>$description_short</td>
		<td>$description</td>
		<td>$price_ttc_d</td>
		<td>$url_article</td>
		<td>$url_image_d</td>
		<td>$delivery_price</td>
		<td>$disponibilite</td>
		<td>$delai_livraison</td>
		<td>$garantie</td>
		<td>$reference_d</td>
		<td>0</td>
		<td>$marque</td>
		<td>$ean13_d</td>
		<td>$price_barred_d</td>
		<td>&nbsp;</td>
		<td>$devise</td>
		<td>$etat</td>
		<td>&nbsp;</td>
	</tr>";
}
$sep = $separateur;
fwrite($fichier,$category_name . $sep . 
	$id_product_attribute . $sep . 
	$description_short . $sep . 
	$description . $sep . 
	$price_ttc_d . $sep . 
	$url_article . $sep . 
	$url_image_d . $sep . 
	$delivery_price . $sep . 
	$disponibilite . $sep . 
	$delai_livraison . $sep . 
	$garantie . $sep . 
	$reference_d . $sep . 
	"0" . $sep . 
	$marque . $sep . 
	$ean13_d . $sep . 
	$price_barred_d . $sep . 
	$sep . 
	$devise . $sep . 
	$etat . $sep . 
	"\n");
?>