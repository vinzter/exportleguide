<?php
$prefix 	= _DB_PREFIX_;
$db_name 	= _DB_NAME_;
$sql = "CREATE TABLE IF NOT EXISTS `".$prefix."guide_parameter` (
		`id_parameter` INT NOT NULL AUTO_INCREMENT ,
		`parameter_name` TEXT NOT NULL ,
		`parameter_value` TEXT NOT NULL ,
		`parameter_guide` TEXT NOT NULL ,
		PRIMARY KEY ( `id_parameter` )
		) DEFAULT CHARSET=utf8 ENGINE = MYISAM";
$result = mysql_query($sql);
$sql 	= "SELECT COUNT(*) AS count FROM ".$prefix."guide_parameter";
$result = mysql_query($sql);

if ($result === FALSE) {
    die('Erreur SQL : '.mysql_error());
}

$count = mysql_result($result,0);

// Ajout de 1 pour la langue car sinon les requetes SQL sont en erreur par la suite.
if ($count==0) {
	$sql = "INSERT INTO `".$db_name."`.`".$prefix."guide_parameter` (
		`id_parameter` ,
		`parameter_name` ,
		`parameter_value`,
		`parameter_guide`
		) VALUES
		(NULL , 'lang_export', '1','$module_name'),
		(NULL , 'url_rewriting', 'on','$module_name'),
		(NULL , 'disponibilite', '','$module_name'),
		(NULL , 'livreur', '','$module_name'),
		(NULL , 'frais', '','$module_name'),
		(NULL , 'delai_livraison', '','$module_name'),
		(NULL , 'garantie', '','$module_name'),
		(NULL , 'etat', '','$module_name'),
		(NULL , 'separateur', '','$module_name'),
		(NULL , 'extension_fichier', 'txt','$module_name'),
		(NULL , 'nom_fichier', 'produits_','$module_name'),
		(NULL , 'nom_type_image', 'large_default','$module_name'),
		(NULL , 'nom_comparateur', 'leguide','$module_name'),
		(NULL , 'parameter_save', '1','$module_name'),
		(NULL , 'makedeclinaison', 'on','$module_name'),
		(NULL , 'description_courte', 'on','$module_name'),
		(NULL , 'actif_only', 'on','$module_name'),
		(NULL , 'usefreeshipping', '','$module_name'),
		(NULL , 'exportallproduct', 'on','$module_name'),
		(NULL , 'displayCatalog', '','$module_name'),
		(NULL , 'onMutu', '','$module_name'),
		(NULL , 'onMutuStep', '2','$module_name'),
		(NULL , 'onMutuOnceaDay', 'on','$module_name'),
		(NULL , 'destDir', '','$module_name'),
		(NULL , 'id_group', 1,'$module_name')
		;";

	$result = mysql_query($sql);
}

?>
