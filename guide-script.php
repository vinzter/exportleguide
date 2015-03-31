<?php
	//////////////////////////////////////////////////////////
	//       Export LeGuide                         	    //
	//       Exports de catalogue vers comparateurs    	    //
	//       Voir fichier CHANGELOG.txt			     	    //
	//////////////////////////////////////////////////////////


	$startTime = time();
	$ps_ =	_DB_PREFIX_; // préfixe des tables spécifiques à chaque shop

	// Classes
 	require_once("class/mysql.php");
 	require_once("class/tools_guide.php");
 	require_once("class/form.php");
 	require_once("class/art_info.php");
 	require_once('class/html2text.inc.php');
	
	$knownCatalogues = array('exportleguide','shopmania','tigoon','kelkoo','shoppydoo','shopzilla','shopping');
	$module	 = new exportleguide();						// Nom du module
	$module_name 	= $module->getName();				// nom du module = répertoire
	include("fonctions.php");							// fonctions

	echo "<LINK rel=\"stylesheet\" type=\"text/css\" href=\"../modules/".$module_name."/css/styles.css\">";
	$site_base = __PS_BASE_URI__;						// préfixe du site
	$url_site = $_SERVER['HTTP_HOST'];					// url du site base Serveur
	$url_site_base_prestashop = $url_site.$site_base;	// url du site prestashop Complet pour la génération du fichier

	// Connexion à la base de donnée
	try
	{
		$Mysql = new my_sql($Serveur = _DB_SERVER_ , $Bdd = _DB_NAME_, $Identifiant = _DB_USER_, $Mdp = _DB_PASSWD_);
		$form = new form($ps_,$Mysql);
	}

	catch (Erreur $e){echo $e -> RetourneErreur();}
	include("import_parameter.php");					// importes les catégories pour les guides

	if (!isset($_GET['auto'])) {
?>
		<!-- JQUERY TREEVIEW --><head>
		<script src="../modules/<?php echo $module_name ;?>/jquery/jquery-latest.js"></script>
		<link rel="stylesheet" href="../modules/<?php echo $module_name ;?>/jquery/jquery.treeview.css" type="text/css" />
		<script type="text/javascript" src="../modules/<?php echo $module_name ;?>/jquery/jquery.treeview.js"></script>
		<script type="text/javascript">
		function chkall()
		{
			var taille = document.forms['form_extract'].elements.length;
			var element = null;
			for(i=0; i < taille; i++)
			{
				element = document.forms['form_extract'].elements[i];
				if (element.type == "checkbox" && element.name == "id_cat[]")
				{
					if (!element.checked)
					{
						element.checked = true;
						} else {
						element.checked = false;
					}
				}
			}
		}
		$(document).ready(function(){
			$("#treeview_categories").treeview({
			animated:"normal",
			collapsed: true,
			control: "#treecontrol",
			});
		});
		</script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
		<!-- JQUERY TREEVIEW -->
		<div id="treecontrol">
			<a title="Collapse the entire tree below" href="#" style="color:#268CCD; display:none;"> Tout replier</a>
			<a title="Expand the entire tree below" href="#" style="color:#268CCD; display:none;"> Tout déplier</a>
			<a title="Toggle the tree below, opening closed branches, closing open branches" href="#" style="color:#268CCD;">Tout Déplier/Replier</a> |
			<a title="Toggle selection" href="javascript: chkall();" style="color:#268CCD;">Inverser la sélection</a>
		</div>

<?php
	}
	echo "<span class='exportleguide'>";
	$form->f_form_header("POST","form_extract");
	$tool_guides = new tool_guides($ps_,$Mysql);
	$cronKey	= $tool_guides->f_get_value('cronKey','exportleguide');

	// Création du treeview
	if (!isset($_GET['auto'])) {
		base_arbre($ps_, $module_name, $tool_guides->f_get_value('lang_export','exportleguide'));
	}

	// Création du formulaire
	$form->f_header_guide_logo("http://marchand.exportleguide.com","../modules/".$module_name."/catalogue/exportleguide.com.gif");
	$form->f_header_credits_doc($url_site_base_prestashop, $cronKey, $knownCatalogues);
	$id_currency = $form->f_form_devise_shop();
  	$form->f_list_langue();

	if (version_compare(_PS_VERSION_,'1.4.0.0','>='))

	$form->f_displayGroups();
 	$form->f_disponibilite_exportleguide();
	$form->f_display_carrier();
  	$form->f_zone();
   	$form->f_delai_livraison_exportleguide();
   	$form->f_garantie_exportleguide();
   	$form->f_etat_exportleguide();
	$form->f_makedeclinaison();
	$form->f_description_courte();
	$form->f_usefreeshipping();
	//$form->f_exportallproduct();
	$form->f_actif_only();
	$form->f_Informations($module_name,$url_site_base_prestashop);
	$form->f_CaracteristiquesFichier();
	$comparateur = $form->f_DisplayComparateurs();
   	$prefixe_nom_fichier = $form->f_nom_fichier();
	$suffixe_nom_type_image = $form->f_nom_type_image();
	$destDir = $form->f_destDir();
	$sIdxFullPath = '';
	$fullDestDir = '';

	if ($destDir!='') {
		$fullDestDir = str_replace('\\', '/', realpath(dirname(__FILE__).'/../../')."/$destDir");
		$form->f_fullDestDir($fullDestDir);
	}

	if ($fullDestDir!='' && !is_dir($fullDestDir)) {
		if (!mkdir($fullDestDir)) {
			echo "<br>La création du répertoire : $fullDestDir a échoué. Créez le à la main.<br>";
		}
	}

   	//$extension_fichier = $form->f_extension_fichier();	//géré automatiquement à partir de v1.52
   	//$form->f_separateur();								// géré automatiquement
	$form->f_displayCatalog();
	$form->f_onMutu();
   	$form->f_form_submit("Sauvegarder mes pr&eacute;f&eacute;rences et cat&eacute;gories","valid_form_maj");
   	$form->f_form_submit("G&eacute;n&eacute;rer","valid_form");
   	$form->f_form_end();

if (isset($_POST['valid_form']) || isset($_GET['auto'])) {

	// Paramètres généraux appliqués à tous les articles
	if (isset($_GET['auto'])) {
		$categories = array();
		getSavedCategories($ps_, $module_name, $categories);
		$id_lang				= $tool_guides->f_get_value('lang_export','exportleguide');
		$delai_livraison		= $tool_guides->f_get_value('delai_livraison','exportleguide');
		$garantie				= $tool_guides->f_get_value('garantie','exportleguide');
		$id_carrier				= $tool_guides->f_get_value('livreur','exportleguide');
		$id_zone				= $tool_guides->f_get_value('frais','exportleguide');			                      
		$etat					= $tool_guides->f_get_value('etat','exportleguide');
		//$separateur				= $tool_guides->f_get_value('separateur','exportleguide');
		//$extension_fichier		= $tool_guides->f_get_value('extension_fichier','exportleguide');
		$prefixe_nom_fichier	= $tool_guides->f_get_value('nom_fichier','exportleguide');
		$suffixe_nom_type_image	= $tool_guides->f_get_value('nom_type_image','exportleguide');
		$comparateur			= $tool_guides->f_get_value('nom_comparateur','exportleguide');
		$disponibilite			= $tool_guides->f_get_value('disponibilite','exportleguide');		
		$makedeclinaison		= $tool_guides->f_get_value('makedeclinaison','exportleguide');
		$description_courte		= $tool_guides->f_get_value('description_courte','exportleguide');
		$usefreeshipping		= $tool_guides->f_get_value('usefreeshipping','exportleguide');
		$exportallproduct		= $tool_guides->f_get_value('exportallproduct','exportleguide');
		$actif_only				= $tool_guides->f_get_value('actif_only','exportleguide');
		$displayCatalog			= $tool_guides->f_get_value('displayCatalog','exportleguide');
		$onMutu					= $tool_guides->f_get_value('onMutu','exportleguide');
		$onMutuStep				= $tool_guides->f_get_value('onMutuStep','exportleguide');
		$onMutuOnceaDay			= $tool_guides->f_get_value('onMutuOnceaDay','exportleguide');
		$id_group			= $tool_guides->f_get_value('id_group','exportleguide');

		if ($_GET['auto'] != '')
			$comparateur = strtolower(strip_tags($_GET['auto']));

	} else {
		$categories				= isset($_POST['id_cat'])?$_POST['id_cat']:NULL;
		$id_lang				= $_POST['lang'];
		$delai_livraison		= $_POST['delai-livraison'];
		$garantie				= $_POST['garantie'];
		$id_carrier				= $_POST['carrier'];
		$id_zone				= $_POST['zone'];
		$etat					= $_POST['etat'];
		//$separateur				= $_POST['separateur'];
		//$extension_fichier		= $_POST['extension_fichier'];
		$prefixe_nom_fichier	= $_POST['nom_fichier'];
		$suffixe_nom_type_image	= $_POST['nom_type_image'];
		$comparateur 			= $_POST['comparateur'];
		$disponibilite			= $_POST['disponibilite'];
		$makedeclinaison		= isset($_POST['makedeclinaison'])?1:0; //valeur à "on" mais changer à 1
		$description_courte		= isset($_POST['description_courte'])?1:0; //valeur à "on" mais changer à 1
		$usefreeshipping		= isset($_POST['usefreeshipping'])?1:0; //valeur à "on" mais changer à 1
		$exportallproduct		= isset($_POST['exportallproduct'])?1:0; //valeur à "on" mais changer à 1
		$actif_only				= isset($_POST['actif_only'])?1:0; //valeur à "on" mais changer à 1
		$displayCatalog			= isset($_POST['displayCatalog'])?1:0; //valeur à "on" mais changer à 1
		$onMutu					= isset($_POST['onMutu'])?1:0; //valeur à "on" mais changer à 1
		$onMutuStep				= isset($_POST['onMutuStep'])?$_POST['onMutuStep']:2;
		$onMutuOnceaDay			= isset($_POST['onMutuOnceaDay'])?1:0; //valeur à "on" mais changer à 1
		$id_group			= isset($_POST['id_group'])?$_POST['id_group']:0;
	}

	$devise		= isset($_POST['devise'])?$_POST['devise']:'EUR';

	if (!$id_carrier) {
		echo "<span class='alert'>Transporteur/Livraison non définie. Impossible de continuer.</span>";
		return;
	}

	switch($comparateur) {
		case 'leguide':
			$extension_fichier = "txt";
			$separateur = "|" ;
			break;
		case 'shopmania' : 
			$extension_fichier = "csv";
			$separateur = "|" ;
			break;
		case 'tigoon' : 
			$extension_fichier = "csv";
			$separateur = ";" ;
			break;
		case 'kelkoo' : 
			$extension_fichier = "txt";
			$separateur = "\t" ;
			break;
		case 'shoppydoo' : 
			$extension_fichier = "txt";
			$separateur = "|" ;
			break;
		case 'shopzilla' : 
			$extension_fichier = "txt";
			$separateur = "|" ;
			break;
		case 'shopping' : 
			$extension_fichier = "txt";
			$separateur = "," ;
			break;
		default :
			echo "<span class='alert'>Comparateur non reconnu !</span>";
			return;
	}

	$path_parts = pathinfo(__FILE__);
	$fullfilename = $path_parts['dirname']."/exports/".$prefixe_nom_fichier.$comparateur.".".$extension_fichier;

	// Fichier pour sauvegarder les articles déjà traités
	$sExportedArticles = $path_parts['dirname']."/exports/$comparateur.art";

	if ($displayCatalog) {
		echo "<table class=\"stats\" border=1>
		<tr><td class=\"hed\" colspan=\"8\">Liste des produits</td></td>";
	}

	$rand=new UniqueRand();
	$strSeparateur = getSeparateurFromCode($separateur);
	global $link;

	// Nombre de catégories à extraire
	$total_catego	= count($categories);

	if (!$total_catego) {
		echo "<br><span class='alert'>Aucune catégorie ne semble avoir été sélectionné</span><br>";
		return;
	}

	$lastIndex = 0;
	$startedIndex = 0;
	$nbLinesCatalog = 0;

	if (!isset($productsExported))
		$productsExported=array();

	if ($onMutu) {
		$idx_filename = "$comparateur.idx";
		$path_parts = pathinfo(__FILE__);
		$sIdxFullPath = $path_parts['dirname']."/exports/".$idx_filename;

		// Si une seule génération par jour
		clearstatcache();

		if ($onMutuOnceaDay && file_exists($fullfilename) && !file_exists($sIdxFullPath) && ( time() <= strtotime('tomorrow', filemtime($fullfilename)))) {
			echo '<br><b>Le catalogue existe déjà : </b> '.basename($fullfilename).' - '.date('j/n/Y - H:i:s', filemtime($fullfilename))."</b><br><br>";
			url_file_download($module_name, $url_site_base_prestashop, $comparateur, $prefixe_nom_fichier.$comparateur, $extension_fichier, $destDir);
			return;
		}

		// Récupération du dernier index traité
		if (file_exists($sIdxFullPath)) {
			if ($hIdxFullPath = fopen($sIdxFullPath, 'r')) {
				$lastIndex = fread($hIdxFullPath, filesize($sIdxFullPath));

				// Stockage de l'index de reprise
				if (!$startedIndex)
					$startedIndex = $lastIndex;

				fclose($hIdxFullPath);

				if ($hIdxFullPath = fopen($sExportedArticles, 'r')) {
					while (!feof($hIdxFullPath)) {
						$productsExported[] = fgets($hIdxFullPath, 1024);
					}
					fclose($hIdxFullPath);
				}
			}
		} else {
			@unlink($sIdxFullPath);
		}

		$hExportedArticles = fopen($sExportedArticles, 'a');
		if (!$hExportedArticles) {
			echo "<span class='alert'>Impossible de créer le fichier $sExportedArticles</span><br>";
			return;
		}
		echo "<br><b>Tranche</b> ".(floor($startedIndex/$onMutuStep)+1)."/".ceil($total_catego/$onMutuStep)." <b>Catégorie(s) :</b>";
	}

	if (!file_exists($sIdxFullPath)) {
		@unlink($fullfilename);
	}

	// Création du fichier
	$fichier = fopen($fullfilename,"a");
	if (!$fichier) {
		echo "<span class='alert'>Impossible d'ouvrir le fichier $fullfilename en écriture</span><br>";
		return;
	}
	fwrite($fichier, "\xEF\xBB\xBF");

	// Création header du fichier
	if (filesize ($fullfilename) < 4) {
		if ($comparateur == 'kelkoo') {
			include('catalogue/catalogue_header_kelkoo.php');
		} elseif ($comparateur == 'shoppydoo') {
			include('catalogue/catalogue_header_shoppydoo.php');
		} elseif ($comparateur == 'shopzilla') {
			include('catalogue/catalogue_header_shopzilla.php');
		} elseif ($comparateur == 'shopping') {
			include('catalogue/catalogue_header_shopping.php');
		} else {
			include('catalogue/catalogue_header.php');
		}
	}

	if (!defined('_PS_VERSION_')) {
		echo "<span class='alert'>Version de Prestashop indéfinie ! Vérifiez le fichier config.inc</span>";
		return;
	}

	if (version_compare(_PS_VERSION_,'1.4.0.0','>=')){
		$req = "SELECT value FROM ".$ps_."configuration WHERE name='PS_COUNTRY_DEFAULT'";
		$Resulats = $Mysql->TabResSQL($req);
		$ps_country_default = $Resulats[0]['value'];
	}

	for($i=$lastIndex; $i<$total_catego; $i++) {
		// Traitement sur la catégorie en cours
		$id_catego 	=  $categories[$i];

		// IMPORTANT :: Sélection des articles à extraire (basé sur id_product)
		// Catégorie Home exclue
		try {
			$req = "SELECT id_product FROM ".$ps_."category_product where id_category = $id_catego ORDER BY id_product";
			$Resulats = $Mysql->TabResSQL($req);

			if ($onMutu) {
				if ($lastIndex != $i)
					echo ',';
				echo " $id_catego";
			}

			foreach ($Resulats as $Valeur) {
				$id_product_r	= $Valeur['id_product'];

				// IMPORTANT :: Sélection des informations liées aux articles
				try {
					if (version_compare(_PS_VERSION_,'1.4.0.0','>=')) {
						$req = "SELECT * FROM ".$ps_."product p
						LEFT JOIN ".$ps_."product_lang pl ON p.id_product = pl.id_product 
						LEFT JOIN (SELECT id_tax_rule, id_tax_rules_group, id_country, id_state, zipcode_from, zipcode_to, id_tax, behavior FROM `".$ps_."tax_rule`) tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = ".$ps_country_default." AND tr.`id_state` = 0) LEFT JOIN `".$ps_."tax` t ON (t.`id_tax` = tr.`id_tax`)
						WHERE p.id_product = $id_product_r".($actif_only?" AND p.available_for_order = 1 AND p.active = 1":"")." AND pl.id_lang=$id_lang";
					} else {
						$req = "SELECT * FROM ".$ps_."product 
						LEFT JOIN ".$ps_."product_lang ON ".$ps_."product.id_product = ".$ps_."product_lang.id_product 
						WHERE ".$ps_."product.id_product = $id_product_r".($actif_only?" and ".$ps_."product.active = 1":"")." and ".$ps_."product_lang.id_lang=$id_lang";
					}

					$Resulats = $Mysql->TabResSQL($req);

					$suffixe_nom_type_image	= $tool_guides->f_get_value('nom_type_image','exportleguide');

					foreach ($Resulats as $Valeur) {
						// Informations générales selon article de base
						$id_product			= $Valeur['id_product'];
						$nom_produit		= f_convert_text2("",$Valeur['name'],false);
						$desc_produit		= f_convert_text2($strSeparateur,$description_courte ? $Valeur['description_short'] : $Valeur['description'], false);
						$weight_base		= $Valeur['weight'];
						$price				= $Valeur['price'];
						$price_s_tva		= $Valeur['price'];
						$price_s_red		= $Valeur['price'];
						$ean13				= $Valeur['ean13'];
						$reference			= $Valeur['reference'];
						$link_rewrite		= $Valeur['link_rewrite'];
						$id_manufacturer	= $Valeur['id_manufacturer'];
						$id_category_default= $Valeur['id_category_default'];
						$ecotax				= $Valeur['ecotax'];;
						$id_tax				= $Valeur['id_tax'];

						if (version_compare(_PS_VERSION_,'1.4.0.0','>=')) {
							$price_with_reduc = SpecificPrice::getSpecificPrice($id_product, (int)(Shop::getCurrentShop()), $id_currency, $ps_country_default, $id_group, 1);

							if ($price_with_reduc['reduction_type'] == 'percentage') {
								$reduction_price	= 0;
								$reduction_percent	= $price_with_reduc['reduction']*100;
							} else {
								$reduction_price	= $price_with_reduc['reduction'];
								$reduction_percent	= 0;
							}

							$date_reduction_s	= $price_with_reduc['from'];
							$date_reduction_e	= $price_with_reduc['to'];;

						} else {
							$reduction_price	= $Valeur['reduction_price'];
							$reduction_percent	= $Valeur['reduction_percent'];
							$date_reduction_s	= $Valeur['reduction_from'];
							$date_reduction_e	= $Valeur['reduction_to'];
						}

						$supplier_reference = $Valeur['supplier_reference'];
						//$shopping_cat		= $Valeur['shopping_cat'];
						$id_supplier		= $Valeur['id_supplier'];
						$quantity_stock		= $Valeur['quantity'];
						$type_promotion		= $Valeur['on_sale'];

						// Nouvelle instance
						$article = new art_info($ps_,$Mysql);

						// Nom du shop
						$nom_shop = $article->f_shop_name();

						// Taux de TVA
						if ($id_tax) {
							$taux_tva		= $article->f_tva_taux($id_tax);
						} else {
							$taux_tva		= 0;
						}

						// Nom de la catégorie
						$category_name = $article->f_category_name($id_catego,$id_lang);

						// Url de l'article
						$catrewrite=Category::getLinkRewrite($id_category_default, intval($id_lang));
						$url_article = $this->context->link->getProductLink($id_product,$link_rewrite,$catrewrite);						

						// Url de l'image
						$url_image_b = $article->f_url_image($url_site_base_prestashop,$id_product,$suffixe_nom_type_image);

						// Sélection du prix pour la livraison
						$delivery_price= $article->f_delivery_price($weight_base,$id_carrier,$id_zone);

						// Récuperation des fdp offert par seuil de prix et poids
						$seuil_prix_fdp_offert=preg_replace("#(,| )#","",(Configuration::get('PS_SHIPPING_FREE_PRICE')));
						$seuil_poids_fdp_offert=preg_replace("#(,| )#","",(Configuration::get('PS_SHIPPING_FREE_WEIGHT')));
						$shipping_method= intval(Configuration::get('PS_SHIPPING_METHOD'));

						// Nom fournisseur
						$supplier_name = $article->f_fournisseur_name($id_supplier);

						// Nom fabricant
						$manufacturer_name = $article->f_fabricant($id_manufacturer);

						// Sélection des déclinaisons
						try {
							if ($makedeclinaison) {
								$Resulats = $Mysql->TabResSQL("SELECT * FROM ".$ps_."product_attribute LEFT JOIN ".$ps_."product_lang ON ".$ps_."product_attribute.id_product = ".$ps_."product_lang.id_product  WHERE ".$ps_."product_attribute.id_product= $id_product AND id_lang = $id_lang");

								//$Resulats = $Mysql->TabResSQL("SELECT * FROM ".$ps_."product_attribute LEFT JOIN ".$ps_."product_lang ON ".$ps_."product_attribute.id_product = ".$ps_."product_lang.id_product LEFT JOIN (SELECT id_image, id_product_attribute as ida FROM ".$ps_."product_attribute_image) pai ON ".$ps_."product_attribute.id_product_attribute = pai.ida WHERE ".$ps_."product_attribute.id_product= $id_product AND id_lang = $id_lang");

								foreach ($Resulats as $Valeur) {
									$attributes_detail		= $article->f_attribute_name($Valeur['id_product_attribute'],$id_lang);
									$description_short		= f_convert_text($Valeur['name'])." ".$attributes_detail;
									$description			= f_convert_text($description_courte?$Valeur['description_short']:$Valeur['description']);
									$description 			= str_replace($strSeparateur," ",$description);
									$description_short 		= str_replace($strSeparateur," ",$description_short);

									// Ajout d'une fonction d'unicité pour l'id du produit en déclinaison
									//$id_product_attribute 	= uniqid(); //$id_product-$Valeur['id_product_attribute'];

									$id_product_attribute = $rand->uRand(100000000,999999999);
									
									if (isset ($Valeur['id_image'])) {
										$id_image			 = $Valeur['id_image'];
									} else {
										$id_image			 = 0;
									}

									if ($id_image<>0) {
										$url_image_d		= $article->f_url_image($url_site_base_prestashop,$id_image,$suffixe_nom_type_image);
									} else {
										$url_image_d		= $url_image_b;
									}

									$id_product			 	= $Valeur['id_product'];
									$reference_d			= $Valeur['reference'];
									$supplier_reference		= $Valeur['supplier_reference'];
									$location				= $Valeur['location'];
									$ean13_d				= $Valeur['ean13'];
									$wholesale_price		= $Valeur['wholesale_price'];
									$price_supp_decl		= $Valeur['price'];
									$price_barred_d			= f_prix_barre($reduction_percent,$reduction_price,$price,$taux_tva,$price_supp_decl);
									$price_ttc_d			= f_prix_ttc($price,$taux_tva,$price_supp_decl,$reduction_price,$reduction_percent);
									$ecotax					= $Valeur['ecotax'];
									$quantity				= $Valeur['quantity'];
									$weight_attribute		= $weight_base+$Valeur['weight'];

									if (!in_array($id_product,$productsExported))
									{
										$productsExported[]=$id_product;

										if ($onMutu)
											fwrite($hExportedArticles, "$id_product\n");
										
										if ($comparateur == 'kelkoo') {
											include("catalogue/catalogue_declinaisons_kelkoo.php");
										} else {
											include("catalogue/catalogue_declinaisons.php");
										}
										
										$nbLinesCatalog++;
									}
									else continue;
								}
							}

							// Si il n'y a pas de déclinaison on prend les valeurs par défaut
							if (empty($Resulats) || !$makedeclinaison) {
								$price_ttc_b = f_prix_ttc($price,$taux_tva,"",$reduction_price,$reduction_percent);
								$price_barred_b	= f_prix_barre($reduction_percent,$reduction_price,$price,$taux_tva,0);
								$delivery_price = f_calc_fdp($usefreeshipping,$shipping_method,$delivery_price,$seuil_prix_fdp_offert,$seuil_poids_fdp_offert,$price_ttc_b,$weight_base);

								if (!in_array($id_product,$productsExported))
								{
									$productsExported[]=$id_product;
									
									if ($onMutu)
										fwrite($hExportedArticles, "$id_product\n");

									if ($comparateur == 'kelkoo') {
										include("catalogue/catalogue_kelkoo.php");
									} elseif ($comparateur == 'shoppydoo') {
										include('catalogue/catalogue_shoppydoo.php');
									} elseif ($comparateur == 'shopzilla') {
										include('catalogue/catalogue_shopzilla.php');
									} elseif ($comparateur == 'shopping') {
										include('catalogue/catalogue_shopping.php');
									} else {
										include('catalogue/catalogue.php');
									}

									$nbLinesCatalog++;
								} else {
									continue;
								}
							}
						}

						catch (Erreur $e) {
							echo $e -> RetourneErreur('Impossible de sélectionner les déclinaisons');
						}
					}
				}

				catch (Erreur $e) {
					echo $e -> RetourneErreur('Impossible de sélectionner les informations produits');
				}
			}

			if ($onMutu) {
				// Sauvegarde de la catégorie traitée
				if (!$hIdxFullPath = fopen($sIdxFullPath, 'w')) {
					echo "<span class='alert'>Impossible d'ouvrir le fichier '$sIdxFullPath'</span><br>";
					return;
				}

				if (fwrite($hIdxFullPath, $i+1) === FALSE) {
				   echo "<span class='alert'>Impossible d'écrire dans le fichier '$sIdxFullPath'</span><br>";
				   return;
				}

				fclose($hIdxFullPath);

				// arrêt si nombre de catégories à traiter atteint
				if (($i+1) >= ($startedIndex+$onMutuStep)) {
					echo " traitée(s)<br><br>$nbLinesCatalog produits insérés dans le catalogue en ".(mktime()-$startTime)."s (limite du serveur : ".ini_get('max_execution_time')."s)";
					echo "<br><br><span class='alert'>Relancez le script pour continuer à créer le catalogue</span><br><br>";
					fclose($hExportedArticles);
					return;
				}
			}
		}

		catch (Erreur $e) {
			echo $e -> RetourneErreur('Impossible de sélectionner les id_product');
		}
	} // END FOR

	fclose($fichier);	//Fabien LAHAULLE - 19/11/2009 - fermeture du fichier d'export.

	if ($onMutu) {
		unlink($sIdxFullPath);
		fclose($hExportedArticles);
		unlink($sExportedArticles);
		echo '<br><br><b>Traitement terminé</b><br><br>';
	}

	url_file_download($module_name, $url_site_base_prestashop, $comparateur, $prefixe_nom_fichier.$comparateur, $extension_fichier, $destDir);
}

// Mise à jour, sauvegarde des paramètres
if (isset($_POST['valid_form_maj'])) {
	$p_maj_lang 												= $_POST['lang'];
	isset($_POST['url-rewriting'])		? $p_maj_url_rewriting 	= $_POST['url-rewriting'] :	$p_maj_url_rewriting = '';
	$p_maj_disponibilite 										= $_POST['disponibilite'];
	$p_maj_carrier									 			= $_POST['carrier'];
	$p_maj_zone 												= $_POST['zone'];
	$p_maj_delai_livraison									 	= $_POST['delai-livraison'];
	$p_maj_garantie 											= $_POST['garantie'];
	$p_maj_etat 												= $_POST['etat'];
	//$p_maj_separateur											= $_POST['separateur'];
	//$p_maj_extension_fichier									= $_POST['extension_fichier'];
	$p_maj_comparateur											= $_POST['comparateur'];
	$p_maj_nom_fichier											= $_POST['nom_fichier'];
	$p_maj_nom_type_image										= $_POST['nom_type_image'];
	$makedeclinaison											= $_POST['makedeclinaison'];
	$description_courte											= $_POST['description_courte'];
	isset($_POST['usefreeshipping'])	?	$usefreeshipping 	= $_POST['usefreeshipping']		:	$usefreeshipping = '';
	isset($_POST['actif_only'])			?	$actif_only			= $_POST['actif_only']			:	$actif_only = '';
	isset($_POST['exportallproduct'])	?	$exportallproduct 	= $_POST['exportallproduct']	:	$exportallproduct = '';
	isset($_POST['displayCatalog'])		?	$displayCatalog 	= $_POST['displayCatalog']		:	$displayCatalog = '';
	isset($_POST['onMutu'])				?	$onMutu 			= $_POST['onMutu']				:	$onMutu = '';
	$onMutuStep													= $_POST['onMutuStep'];
	isset($_POST['onMutuOnceaDay'])		?	$onMutuOnceaDay 	= $_POST['onMutuOnceaDay']		:	$onMutuOnceaDay = '';
	isset($_POST['destDir'])			?	$destDir		 	= $_POST['destDir']				:	$destDir = '';
	isset($_POST['id_group'])			?	$id_group			= $_POST['id_group']			:	$id_group = 1;

	$tool_guides->f_update_value("lang_export",$p_maj_lang,$module_name);
	$tool_guides->f_update_value("url_rewriting",$p_maj_url_rewriting,$module_name);
	$tool_guides->f_update_value("disponibilite",$p_maj_disponibilite,$module_name);
	$tool_guides->f_update_value("livreur",$p_maj_carrier,$module_name);
	$tool_guides->f_update_value("frais",$p_maj_zone,$module_name);
	$tool_guides->f_update_value("delai_livraison",$p_maj_delai_livraison,$module_name);
	$tool_guides->f_update_value("garantie",$p_maj_garantie,$module_name);
	$tool_guides->f_update_value("etat",$p_maj_etat,$module_name);
	//$tool_guides->f_update_value("separateur",$p_maj_separateur,$module_name);
	//$tool_guides->f_update_value("extension_fichier",$p_maj_extension_fichier,$module_name);
	$tool_guides->f_update_value("nom_fichier",$p_maj_nom_fichier,$module_name);
	$tool_guides->f_update_value("nom_type_image",$p_maj_nom_type_image,$module_name);
	$tool_guides->f_update_value("nom_comparateur",$p_maj_comparateur,$module_name);
	$tool_guides->f_update_value("parameter_save","1",$module_name);
	$tool_guides->f_update_value("makedeclinaison",$makedeclinaison,$module_name);
	$tool_guides->f_update_value("description_courte",$description_courte,$module_name);
	$tool_guides->f_update_value("actif_only",$actif_only,$module_name);
	$tool_guides->f_update_value("usefreeshipping",$usefreeshipping,$module_name);
	$tool_guides->f_update_value("exportallproduct",$exportallproduct,$module_name);
	$tool_guides->f_update_value("displayCatalog",$displayCatalog,$module_name);
	$tool_guides->f_update_value("onMutu",$onMutu,$module_name);
	$tool_guides->f_update_value("onMutuStep",$onMutuStep,$module_name);
	$tool_guides->f_update_value("onMutuOnceaDay",$onMutuOnceaDay,$module_name);
	$tool_guides->f_update_value("destDir",$destDir,$module_name);
	$tool_guides->f_update_value("id_group",$id_group,$module_name);

	// On ne peut pas sauvegarder les catégories si elles n'ont pas été affichées
	if (!isset($_GET['auto'])) {
		// On vide les préférences des catégories
		$tool_guides->f_get_delete_category($module_name);

		// On insert les id des catégories a mémoriser
		if (isset($_POST['id_cat'])) {
			$categories			= $_POST['id_cat'];
			$total_catego		= count($categories);
			echo $total_catego;
			for($i=0;$i<$total_catego;$i++)
			{
				$id_catego 	=  $categories[$i];
				$tool_guides->f_get_insert_category($id_catego,$module_name);
			}
		}
	}

	header ("location: ".f_url_actuelle());
	exit;
}

$prefixe_nom_fichier	= $tool_guides->f_get_value('nom_fichier','exportleguide');

if (!isset($comparateur))
	$comparateur		= $tool_guides->f_get_value('nom_comparateur','exportleguide');

switch($comparateur) {
	case 'leguide':
		$extension_fichier = "txt";
		break;
	case 'shopmania' : 
		$extension_fichier = "csv";
		break;
	case 'tigoon' : 
		$extension_fichier = "csv";
		break;
	case 'kelkoo' : 
		$extension_fichier = "txt";
		break;
	case 'shoppydoo' : 
		$extension_fichier = "txt";
		break;
	case 'shopzilla' : 
		$extension_fichier = "txt";
		break;
	case 'shopping' : 
		$extension_fichier = "txt";
		break;
	default :
		echo "<span class='alert'><b>Comparateur non reconnu !</b></span>";
		return;
}

$path_parts				= pathinfo(__FILE__);
$catalogFilename		= $prefixe_nom_fichier.$comparateur.".".$extension_fichier;
$fullfilename			= $path_parts['dirname']."/exports/".$catalogFilename;
$destFilename			= $fullDestDir."/".$catalogFilename; // $fullDestDir.$catalogFilename;
clearstatcache();

if (file_exists($fullfilename) && !file_exists($sIdxFullPath)) {
	echo "<br><br>Date du fichier <b>$catalogFilename</b> : ".date('j/n/Y - H:i:s', filemtime($fullfilename)).'<br><br>';
	
	if ($fullDestDir != '') {
		if (!copy($fullfilename, "$fullDestDir/$catalogFilename")) {
			echo "<span class='alert'>La copie du catalogue</span> de $fullfilename vers<br>$destFilename a échoué<br>";
		} else {
			echo "<span class=''>Copie du catalogue $catalogFilename vers <b>$destFilename</b> OK</span><br>";
			@unlink($fullfilename);
		}
	}
}

if (!isset($_SERVER['WINDIR'])) {
	$perms = substr(sprintf('%o', fileperms($path_parts['dirname']."/exports/")), -4);
	echo "<br>Permissions du répertoire exports : $perms<br>";

	if ($fullDestDir != '') {
		$perms = substr(sprintf('%o', fileperms($fullDestDir)), -4);
		echo "Permissions du répertoire $fullDestDir : $perms<br>";
	}
}

echo "</span>";

class UniqueRand {
	var $alreadyExists = array();

	function uRand($min = NULL, $max = NULL) {
		$break = 'false';
		while ($break == 'false') {
			$rand=mt_rand($min,$max);

			if (array_search($rand,$this->alreadyExists)===false) {
				$this->alreadyExists[]=$rand;
				$break='stop';
			} else {
				echo " $rand already!  ";
				print_r($this->alreadyExists);
			}
		}

		return $rand;
	}
}

?>
