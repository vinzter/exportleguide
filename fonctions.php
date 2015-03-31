<?php
	function f_convert_text($string) {
		htmlspecialchars($string);
		$string = strip_tags(html_entity_decode(($string),ENT_QUOTES,'UTF-8'));
		strip_tags($string); 

		// Suppression des balises <br/>
		$string = preg_replace('#<br ?/?>#isU', ' ', $string);
		$string = preg_replace('#<[^>]*>#', '', $string);

		// Retire les retours à la ligne
		$string = preg_replace('/(\r\n|\n|\r)/', ' ', $string); 
		
		$string = str_replace('&agrave;', 'à', $string);
		$string = str_replace('&acirc;', 'â', $string);
		$string = str_replace('&auml;', 'ä', $string);
		$string = str_replace('&ccedil;', 'ç', $string);
		$string = str_replace('&egrave;', 'è', $string);
		$string = str_replace('&eacute;', 'é', $string);
		$string = str_replace('&Eacute;', 'é', $string);
		$string = str_replace('&ecirc;', 'ê', $string);
		$string = str_replace('&euml;', 'ë', $string);
		$string = str_replace('&icirc;', 'î', $string);
		$string = str_replace('&ocirc;', 'ô', $string);
		$string = str_replace('&ugrave;', 'ù', $string);
		$string = str_replace('&ucirc;', 'û', $string);
		$string = str_replace('&rsquo;', "'", $string);
		$string = str_replace('&deg;', '°', $string);

		// Conversion des monnaies
		$string = str_replace('&euro;', 'EUR', $string);

		// Espacement
		$string = str_replace('&nbsp;', ' ', $string);	

		// on vires les ;
		$string = str_replace(';', ':', $string);	

		return $string;	
	}

	// Fonction nettoyage de caractères html

	function f_convert_text2($strSeparateur,$string,$useHtml2text) {
		$string = trim($string);
		$string = preg_replace('#<br ?/?>#isU', ' ', $string);
		$string = preg_replace('/(\r\n|\n|\r)/', ' ', $string); 
		$string = strip_tags(html_entity_decode(($string),ENT_QUOTES,'UTF-8'));
		$string = str_replace('&euro;', '€', $string);
		$string = preg_replace('#\t+#', ' ', $string);
		$string = preg_replace('#\t+#', ' ', $string);
		$string = preg_replace('#'.CHR(10).'+#',' ',$string);
		$string = str_replace(CHR(9)," ",$string);

		if ($strSeparateur != "")
			$string = str_replace($strSeparateur," ",$string);

		$string = preg_replace('# +#', ' ', $string);

		if ($useHtml2text) {
			$string = new html2text($string);
			$string = $string->get_text();
		}

		return trim($string);
	}

	function getDeliveryPriceByRanges($rangeTable) {
		$req='
			SELECT d.`id_'.$rangeTable.'`, d.`id_carrier`, d.`id_zone`, d.`price`
			FROM `'._DB_PREFIX_.'delivery` d
			LEFT JOIN `'._DB_PREFIX_.$rangeTable.'` r ON r.`id_'.$rangeTable.'` = d.`id_'.$rangeTable.'`
			WHERE (d.`id_'.$rangeTable.'` IS NOT NULL AND d.`id_'.$rangeTable.'` != 0)
			ORDER BY r.`delimiter1` ASC';
		$rangeTable = pSQL($rangeTable);

		echo $req;
		return Db::getInstance()->ExecuteS($req);
	}

	// Forcer telecharger fichier txt
	function forcerTelechargement($nom, $situation, $poids) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Length: '. $poids);
		header('Content-disposition: attachment; filename='. $nom);
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');
		readfile($situation);
		exit();
	}

	// Compter le nombre d'articles dans une catégorie
	function count_article_categorie($ps_,$id_category) {
		$sql = 'SELECT COUNT(id_product) as nbre_product FROM '.$ps_.'category_product where id_category='.$id_category.'';
		$res = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		while($data = mysql_fetch_assoc($res)){$nbre_product = $data['nbre_product'];}
		return "<font color=blue>(".$nbre_product.")</font>";
	}

	// Crée un arbre des catégorie avec checkbox
	function base_arbre ($ps_,$module_name,$id_lang) {
		echo "\n<ul id=\"treeview_categories\" class=\"filetree\">\n";
		$previousLevel = 0;

		// Lit la catégorie parent début de l'arbre
		$sql = 'SELECT * from '.$ps_.'category LEFT JOIN '.$ps_.'category_lang ON '.$ps_.'category.id_category = '.$ps_.'category_lang.id_category WHERE level_depth=0 and id_parent=0 and id_lang='.$id_lang.' and active=1 ORDER BY position ASC';
		//echo $sql;
		//error_log('--'.$sql);
		//$trace = "$sql\r\n---\r\n";

		$res = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

		if (!mysql_num_rows($res)) {
			$sql = 'SELECT * from '.$ps_.'category LEFT JOIN '.$ps_.'category_lang ON '.$ps_.'category.id_category = '.$ps_.'category_lang.id_category WHERE level_depth=1 and id_parent=0 and id_lang='.$id_lang.' and active=1';
			echo "<br />".$sql;
			$res = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

			if (!mysql_num_rows($res)) {
				echo "<br><span class='alert'>Pas de catégorie de niveau 0 ni niveau 1 ! Impossible de construire l'arborescence</span><br><br>";
			} else {
				//echo "<br><span class='alert'>Attention, votre arborescence commence au niveau 1 au lieu de 0</span><br><br>";
			}
		}

		while($data = mysql_fetch_assoc($res)) {
			$value = $data['id_category'];
			$level = $data['level_depth'];
			$sql1 = 'SELECT parameter_value from '.$ps_.'guide_parameter WHERE parameter_guide="'.$module_name.'" and parameter_value='.$value.' and parameter_name=\'id_catego\'';

			//error_log('----'.$sql1);
			//$trace .= "$sql\r\n";

			$res1 = mysql_query($sql1) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			
			while($data1 = mysql_fetch_assoc($res1)) {
			   $parameter_value_m = "";
			   $parameter_value_m = $data1['parameter_value'];
			} if (!isset($parameter_value_m)) {
				$parameter_value_m="";
			}

			if ($parameter_value_m==$data['id_category']) {
				$parameter_value_m= "checked";
			} else {
				$parameter_value_m= "";
			}

			$nbre_product = count_article_categorie($ps_,$value);

			echo str_pad(' ', $level)."<li>\n";
			echo "  <span class=\"folder\"><input type='checkbox' $parameter_value_m value=$value name=id_cat[]> ".$data['id_category']." ".$data['name']." $nbre_product</span>\n";
			$id_category = $data['id_category'];

			// On crée la branche
			branche($id_category, $level, $ps_, $module_name, $id_lang);
		}

		//error_log($trace);
		//echo "$trace<br>";
	}

	// Crée une branche pour l'arbre
	function branche($id_category, $level, $ps_, $module_name, $id_lang) {
		//global $trace;

		$spacer = str_pad('  ', $level, ' ');
		$level_next_category = $level+1;
		$style='';
		if ($level_next_category == 1) {
			$style = 'level1';
		}

		$sql = 'SELECT * from '.$ps_.'category LEFT JOIN '.$ps_.'category_lang ON '.$ps_.'category.id_category = '.$ps_.'category_lang.id_category WHERE '.$ps_.'category.id_parent = '.$id_category.' and id_lang='.$id_lang.' and active=1';

		//$trace .= "-branche $sql\r\n";

		$res = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

		while($data = mysql_fetch_assoc($res)) {
			$value = $data['id_category'];
			$sql1 = 'SELECT parameter_value from '.$ps_.'guide_parameter WHERE parameter_guide="'.$module_name.'" and parameter_value='.$value.' and parameter_name=\'id_catego\'';

			//$trace .= "---branche $sql1\r\n";

			$res1 = mysql_query($sql1) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			
			while($data1 = mysql_fetch_assoc($res1)) {
				$parameter_value = "";
				$parameter_value = $data1['parameter_value'];
			}
			
			if (!isset($parameter_value)) {
				$parameter_value="";
			}
			if ($parameter_value==$data['id_category']) {
				$parameter_value= "checked";
			} else {
				$parameter_value= "";
			}

			$nbre_product = count_article_categorie($ps_,$value);
			echo "\n$spacer<ul>";
			echo "\n $spacer<li>"; 
			echo "\n  $spacer<span class=\"folder $style\"><input type='checkbox' $parameter_value value=$value name=id_cat[]> ".$data['id_category']."  ".$data['name']." $nbre_product</span>";

			// Récursif
			$next_id_category = $data['id_category'];
			branche($next_id_category, $level_next_category, $ps_, $module_name, $id_lang);
		}

		echo "\n$spacer </li>\n$spacer</ul>\n";
	}

	// Retourne le prix TTC
	function f_prix_ttc($price,$taux_tva,$price_supp_decl,$reduction_price,$reduction_percent) {
		$price_ttc	=(($price*$taux_tva)/100)+$price+$price_supp_decl;

		// Si réduction en %
		if ($reduction_percent<>0 && $reduction_price==0) {
			$price_ttc=$price_ttc*(1.0-($reduction_percent/100.0));
		}

		// Si montant de réduction
		if ($reduction_price<>0 && $reduction_percent==0) {
			$price_ttc	=  $price_ttc-$reduction_price;
		}

		$price_ttc	= number_format(round($price_ttc,2),2,".","");
		return $price_ttc;	
	}

	// Retourne le prix sans la réduction
	function f_prix_barre($reduction_percent,$reduction_price,$price,$taux_tva,$price_supp_decl) {
		if ($reduction_percent<>0 OR $reduction_price<>0) {
			return f_prix_ttc($price,$taux_tva,$price_supp_decl,0,0);
		}
		return "";
	}

	// Calul du prix des fdp
	function f_calc_fdp($usefreeshipping,$shipping_method,$delivery_price,$seuil_prix_fdp_offert,$seuil_poids_fdp_offert,$prix,$poids) {
		//echo "prix=$prix<br/>";
		//echo "poids=$poids<br/><br/>";

		if ($shipping_method==0  && $prix>=$seuil_prix_fdp_offert && $usefreeshipping && $seuil_prix_fdp_offert!=0) {
			return 0;
		}
		if ($shipping_method==1  && $poids>=$seuil_poids_fdp_offert && $usefreeshipping && $seuil_poids_fdp_offert!=0) {
			return 0;
		}

		// Si dans l'admin module on coche utiliser la gratuité des frais de port
		//echo "usefreeshipping=".$usefreeshipping;
		if ($usefreeshipping!=""){
			return 0;
		}
		return $delivery_price;
	}

	// Retourne le lien pour télécharger le fichier
	function url_file_download ($nom_guide, $url_site_base_prestashop, $libelle_guide, $nom_fichier, $extension_fichier, $destDir='') {
		echo "<p>Lien vers le fichier à spécifier sur \"".ucfirst($libelle_guide)."\" :<p>";

		if ($destDir == '') {
			$url = "http://".$url_site_base_prestashop."modules/$nom_guide/exports/$nom_fichier.$extension_fichier";
		} else {
			$url = "http://".$url_site_base_prestashop."$destDir/$nom_fichier.$extension_fichier";
		}

		echo "<a href=\"$url\" target=\"_blank\">";
		echo "<span style=\"color:#268CCD\">$url</span>";
		echo "</a><p>Le $extension_fichier peut faire appara&icirc;tre des probl&egrave;mes d&rsquo;accents s&rsquo;il est vu sur le navigateur (ouvrir avec un &eacute;diteur de texte)</p>"; 
	}

	// Retourne l'url dans la barre de naviguation
	function f_url_actuelle() {
		return "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}

	// Ecrit dans le fichier
	function f_write_file($filename,$somecontent) {
		// Assurons nous que le fichier est accessible en écriture
		if (is_writable($filename)) {
			// Dans notre exemple, nous ouvrons le fichier $filename en mode d'ajout
			// Le pointeur de fichier est placé à la fin du fichier
			// c'est là que $somecontent sera placé
			if (!$handle = fopen($filename, 'a')) {
				 echo "Impossible d'ouvrir le fichier ($filename)";
				 exit;
			}

			// Ecrivons quelque chose dans notre fichier.
			if (fwrite($handle, $somecontent) === FALSE) {
			   echo "Impossible d'écrire dans le fichier ($filename)";
			   exit;
			}

			//echo "L'écriture de ($somecontent) dans le fichier ($filename) a réussi";
			fclose($handle);
		} else {
			echo "Le fichier $filename n'est pas accessible en écriture.";
		}	
	}

	function getSeparateurFromCode($code) {
		switch ($code) {
			case 0 : 
				return "," ;
				break;
			case 1 : 
				return  ";" ;
				break;
			case 2 : 
				return "|" ;
				break;
			case 3 : 
				return "\t" ;
				break;
		}
	}

	function getSavedCategories($ps_, $module_name, &$categories) {
		$sql1 = 'SELECT parameter_value from '.$ps_.'guide_parameter WHERE parameter_guide="'.$module_name.'" and parameter_name=\'id_catego\' ORDER BY parameter_value';
		$res1 = mysql_query($sql1) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		while($data1 = mysql_fetch_assoc($res1)) {
		   $categories[] = $data1['parameter_value'];
		}
	}
?> 
