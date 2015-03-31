<?php
if ($manufacturer_name=="") {
	$marque = $supplier_name;
} else {
	$marque = $manufacturer_name;
}

/* TODO : transco des disponibilités autres que 0 (en stock) */
/* CODES ATTENDUS PAR KELKOO
001 ("En stock")
002 ("Stock en cours de renouvellement")
003 ("Voir site")
004 ("En pré-commande")
005 ("Disponible sur commande")
*/

switch ($disponibilite) {
	case 0 : 
		$disponibilite = '001';		/* en stock */
		break;
	case 1 : 
		break;
}

$desc = mb_substr($desc_produit, 0, 156, 'UTF-8');
$desc = $desc . "...";	

if ($displayCatalog) {
	echo "
	<tr>
		<td>$url_article</td>
		<td>$description_short</td>
		<td>$id_product_attribute</td>
		<td>$desc</td>
		<td>$price_ttc_d</td>
		<td>$delivery_price</td>
		<td>$url_image_d</td>
		<td>$disponibilite</td>
	</tr>";
}

$sep = $separateur;

fwrite($fichier,$url_article . $sep . 
	$description_short . $sep . 
	$id_product_attribute . $sep . 
	$desc . $sep . 
	$price_ttc_d . $sep . 
	$delivery_price . $sep . 
	$url_image_d . $sep . 
	$disponibilite . 
	"\n");

?>