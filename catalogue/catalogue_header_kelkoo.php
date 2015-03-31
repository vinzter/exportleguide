<?php
/*
Fabien LAHAULLE - 17/11/2009
ajout de l'export vers le comparateur Kelkoo
*/

fwrite($fichier, "#type=basic" . "\n");
fwrite($fichier, "#update=NO" . "\n");
fwrite($fichier, "#quoted=NO" . "\n");

fwrite($fichier, "#country=fr" . "\n");
fwrite($fichier, "#currency=EUR" . "\n");


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
	echo"
	<tr>
		<td>url</td>
		<td>Title</td>
		<td>offerID</td>
		<td>description</td>
		<td>price</td>
		<td>deliverycost</td>
		<td>image</td>
		<td>availability</td>
	</tr>
	";
}

$sep = $separateur;

fwrite($fichier, "url" . $sep . 
	"Title" . $sep . 
	"offerID" . $sep . 
	"description" . $sep . 
	"price" . $sep . 
	"deliverycost" . $sep . 
	"image" . $sep . 
	"availability" . 
	"\n");

?>