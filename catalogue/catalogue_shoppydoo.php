<?phpif ($manufacturer_name=="") {
	$marque = $supplier_name;
} else {
	$marque = $manufacturer_name;
}
/* TODO : transco des disponibilités autres que 0 (en stock) */
/* CODES ATTENDUS PAR Shoppydoo */
switch ($disponibilite) {
	case 0 : 
		$disponibilite = '0';		/* en stock */
		break;
	case 1 : 
		break;
}
$desc = mb_substr($desc_produit, 0, 156, 'UTF-8');
$desc = $desc . "...";

if ($displayCatalog) {
	echo "
		<tr>
			<td>$nom_produit </td>
			<td>$marque </td>
			<td>$desc</td>
			<td>$price_ttc_b</td>
			<td>$ean13</td>
			<td>$id_product</td>
			<td>$disponibilite</td>
			<td>$url_article</td>
			<td>$category_name</td>
			<td>$url_image_b</td>
			<td>$delivery_price</td>
		</tr>";
}
$sep = $separateur;
fwrite($fichier,
	$nom_produit . $sep . 
	$marque . $sep . 
	$desc . $sep . 
	$price_ttc_b . $sep . 
	$ean13 . $sep . 
	$id_product . $sep . 
	$disponibilite . $sep . 
	$url_article . $sep . 
	$category_name . $sep . 
	$url_image_b . $sep . 
	$delivery_price . 
	"\n");
?>