<?php

// Retourne une section de formulaire
class form extends My_sql{
	// Préfixe des tables
 	public function __construct($ps_,$mysql) {
        $this->ps_ = $ps_;
		$this->mysql = $mysql; // Récupère la connexion en cours
		$this->tools_guide = new tool_guides($this->ps_,$this->mysql);
	}
	
	// Présentation du module
	function f_header_guide_logo($url_merchand,$url_img) {
		echo "
		<table>
		<tr>
			<td align=\"right\"><br /><b>A propos</b></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td align=\"right\">Sujet du module sur le forum Prestashop</td>
			<td style=\"padding-left: 15px;\" class=\"links\">
			<a href='http://www.prestashop.com/forums/viewthread/22501/modules_tiers/module_prestashop__exportleguide_dot_com__export_csv_pour_exportleguide_et_autres_comparateurs' target='_blank'>Export csv pour LeGuide et autres comparateurs</a></td>
			<td></td>
		</tr>";
	}

	function f_header_credits_doc($url_site_base_prestashop, $cronKey, &$knownCatalogues) {
		echo "
		<tr>
			<td align=\"right\">Contributeurs</td>
			<td colspan=\"2\" style=\"padding-left: 15px;\" class=\"links\">
			M1bs (idée originale),
			Neodreamer,
			Jolvil,
			Moncler (Avi),
			pppplus,
			Fabien Lahaulle,
			DSI 94,
			KTechnologie,
			Fran6t,
			Vinzter
			</td>
		</tr>
		";
		
		if (version_compare(_PS_VERSION_,'1.4.0.0','>=')) {
			/*echo "
			<tr>
				<td align=\"right\"><span style='color:red;'><b>Attention !</b></span></td>
				<td colspan=\"2\" style=\"padding-left: 15px;\">Cette version du module n'exporte pas le prix des produits avec réduction sous votre version de Prestashop</td>
			</tr>
			";*/
		}

		$url_auto = 'http://'.$url_site_base_prestashop.'modules/exportleguide/cron.php?cronKey='.$cronKey;
		echo "
		<tr>
			<td align=\"right\">Documentation</td>
			<td colspan=\"2\" style=\"padding-left: 15px;\" class=\"links\">
			<br>Pour lancer automatiquement la génération rajoutez des taches cron avec ces URL :<br>
		";
		
		foreach($knownCatalogues as $v) {
			echo "$url_auto&auto=$v<br>";
		}

		echo "
			<br>
			<span style='color:red;'><b>ATTENTION !</b></span> Si vous utilisez la génération par tranches, vous devez, pour chaque catalogue, doubler la tâche Cron autant de fois qu'il est nécessaire pour générer la totalité du catalogue.
			</td>
		</tr>";
		//	Remarques : le mode 'auto' desactive l'affichage de l'arborescence pour raccourcir le temps d'exécution du script
	}

	// Formulaire de conversion en EUR
	function f_form_devise_shop() {
		echo "
		<br>
		<tr>
			<td align=\"right\"><br /><b>Export</b></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td align=\"right\">Devise boutique</td>
			<td style=\"padding-left:15px;\">";

		// Vérifie la monnaie par défaut
		try {
			// Sur certaines version de Prestashop la valeur est CURRENCY_DEFAULT et non pas PS_CURRENCY_DEFAULT
			$Resulats = $this->mysql->TabResSQL('SELECT value FROM '.$this->ps_.'configuration where name = "PS_CURRENCY_DEFAULT"');

			foreach ($Resulats as $Valeur) {
				$id_currency = $Valeur['value'];
			}
		}
		catch (Erreur $e) {
			echo $e -> RetourneErreur('Impossible de sélectionner l\id de la devise par défaut');
		} 
		
		// Sélectionne le code ISO et le cours
		try {
			$Resulats = $this->mysql->TabResSQL("SELECT iso_code,conversion_rate FROM ".$this->ps_."currency where id_currency = '".$id_currency."'");
			foreach ($Resulats as $Valeur) {
				$iso_code			= $Valeur['iso_code'];
				$conversion_rate	= $Valeur['conversion_rate'];
				echo $iso_code;
			}
		}

		catch (Erreur $e) {
			echo $e -> RetourneErreur('Impossible de sélectionner le cours et le taux de change');
		}

		echo "
			</td>
		</tr>";
		echo "
		<tr>
			<td></td>
			<td style=\"padding-left:15px;\">
			<input type=hidden name=\"devise\" value=\"$iso_code\">
			</td>
		</tr>";

		return $id_currency;
	}

	// Langues utilisées sur le shop
	function f_list_langue() {
		echo "
		<tr>
			<td align=\"right\" ><br>Langue d'export</td>
			<td style=\"padding-left:15px;\"><br>
			<select name=\"lang\">";
  
		try {
			$Resulats = $this->mysql->TabResSQL('SELECT id_lang,name,iso_code FROM '.$this->ps_.'lang WHERE active=1');
			foreach ($Resulats as $Valeur) {
				$id_lang	= $Valeur['id_lang'];
				$name_lang	= $Valeur['name'];
				$iso_code	= $Valeur['iso_code'];

				$register_lang = $this->tools_guide->f_get_value('lang_export','exportleguide');

				if ($register_lang == $id_lang) {
					$selected = "selected";
				} else {
					$selected = "";
				}

				echo "<option value=$id_lang $selected>$name_lang</option>";
			}
		}

		catch (Erreur $e) {
			echo $e -> RetourneErreur('Impossible de sélectionner les langues du site');
		} 

		echo "
			</select></td>
			<td></td>
		</tr>";
	}

	// Formulaire des transporteurs
	function f_display_carrier() {
		echo "
		<tr>
			<td align=\"right\">Livraison</td>
			<td style=\"padding-left:15px;\">
			<select name=\"carrier\">";

		try {
			$Resulats = $this->mysql->TabResSQL('SELECT id_carrier, name FROM '.$this->ps_.'carrier WHERE active=1 and deleted=0 GROUP BY name');
			foreach ($Resulats as $Valeur) {
				$id_carrier = $Valeur['id_carrier'];
				$name 		= $Valeur['name'];

				if ($name == '0') {
					$req = "SELECT value FROM ".$this->ps_."configuration WHERE name='PS_SHOP_NAME'";
					$Resulats = $this->mysql->TabResSQL($req);
					$name = $Resulats[0]['value'];
				}

				$register_carrier = $this->tools_guide->f_get_value('livreur','exportleguide');

				if ($register_carrier == $id_carrier) {
					$selected = "selected";
				} else {
					$selected = "";
				}

				//if (trim($id_carrier)<>1) {
					echo "<option value=$id_carrier $selected> $name";
				//}
			}
		}

		catch (Erreur $e) {
			echo $e -> RetourneErreur('Impossible de sélectionner le nom et l\'id du livreur');
		} 

		echo "
			</select>
			</td>
			<td></td>
		</tr>";
	}
	
	// Formulaire disponibilité en stock
	function f_disponibilite_exportleguide() {
		$disponibilite = $this->tools_guide->f_get_value('disponibilite','exportleguide');

		if ($disponibilite <> '') {
			$selected = $disponibilite;
		} else { 
			$selected = "0";
		}

		echo "
		<tr>
			<td align=\"right\">Phrase insérée renseignant la disponibilité;</td>
			<td style=\"padding-left:15px;\">
			<select name=\"disponibilite\" id=\"id_disponibilite\" style=\"display:block;\">
			<option value=\"0\" ".($selected=="0"?"selected":"").">En stock</option>
			<option value=\"1\" ".($selected=="1"?"selected":"").">1 jour</option>
			<option value=\"2\" ".($selected=="2"?"selected":"").">2 jours</option>
			<option value=\"3\" ".($selected=="3"?"selected":"").">3 jours</option>
			<option value=\"4\" ".($selected=="4"?"selected":"").">4 jours</option>
			<option value=\"5\" ".($selected=="5"?"selected":"").">5 jours</option>
			<option value=\"6\" ".($selected=="6"?"selected":"").">6 jours</option>
			<option value=\"7\" ".($selected=="7"?"selected":"").">1 semaine</option>
			<option value=\"14\" ".($selected=="14"?"selected":"").">2 semaines</option>
			<option value=\"21\" ".($selected=="21"?"selected":"").">3 semaines</option>
			<option value=\"28\" ".($selected=="28"?"selected":"").">4 semaines</option>
			<option value=\"35\" ".($selected=="35"?"selected":"").">5 semaines</option>
			</select>
			</td>
		</tr>";
   }
   
	// Formulaire des frais de ports
	function f_zone() {
		echo "
		<tr>
			<td align=\"right\">Frais par d&eacute;faut</td>
			<td style=\"padding-left:15px;\">
			<select name=\"zone\">";

			// Affichage des différentes zones
			try {
				$Resulats = $this->mysql->TabResSQL("SELECT id_zone,name from ".$this->ps_."zone where active = 1");
				foreach ($Resulats as $Valeur) {
					$id_zone 	= $Valeur['id_zone'];
					$name_zone 	= $Valeur['name'];
					$frais = $this->tools_guide->f_get_value('frais','exportleguide');

					if ($frais == $id_zone) {
						$selected = "selected";
					} else {
						$selected = "";
					}

					echo "<option value=$id_zone $selected>$name_zone</option>";
				}
			}

			catch (Erreur $e) {
				echo $e -> RetourneErreur('Impossible de sélectionner le nom et l\'id de la zone');
			}

		echo "
			</select>
			</td>
		</tr>";
	}
   
	// Délai de livraison exportleguide
	function f_delai_livraison_exportleguide() {
		$delai_livraison = $this->tools_guide->f_get_value('delai_livraison','exportleguide');

		if ($delai_livraison <> '') {
			$selected = $delai_livraison;
		} else {
			$selected = "1 jour";
		}

		echo "
		<tr>
			<td align=\"right\">Délai de livraison</td>
			<td style=\"padding-left:15px;\">
			<select name=\"delai-livraison\">
			<option value=\"1 jour\" ".($selected=="1 jour"?"selected":"").">1 jour</option>
			<option value=\"2 jours\" ".($selected=="2 jours"?"selected":"").">2 jours</option>
			<option value=\"3 jours\" ".($selected=="3 jours"?"selected":"").">3 jours</option>
			<option value=\"4 jours\" ".($selected=="4 jours"?"selected":"").">4 jours</option>
			<option value=\"5 jours\" ".($selected=="5 jours"?"selected":"").">5 jours</option>
			<option value=\"6 jours\" ".($selected=="6 jours"?"selected":"").">6 jours</option>
			<option value=\"1 semaine\" ".($selected=="1 semaine"?"selected":"").">1 semaine</option>
			<option value=\"2 semaines\" ".($selected=="2 semaines"?"selected":"").">2 semaines</option>
			<option value=\"3 semaines\" ".($selected=="3 semaines"?"selected":"").">3 semaines</option>
			<option value=\"4 semaines\" ".($selected=="4 semaines"?"selected":"").">4 semaines</option>
			<option value=\"5 semaines\" ".($selected=="5 semaines"?"selected":"").">5 semaines</option>
			</select>
			</td>
		</tr>";
	}
  
	function isSelected($value,$selected) {
		return $value == $selected ? "selected" : "";
	}

	// Garantie exportleguide  
	function f_garantie_exportleguide() {
		$garantie = $this->tools_guide->f_get_value('garantie','exportleguide');

		if ($garantie <> '') {
			$selected = $garantie;
		} else {
			$selected = "1 mois";
		}

		echo "
		<tr>
			<td align=\"right\">Garantie</td>
			<td style=\"padding-left:15px;\">
			<select name=\"garantie\">
			<option value=\"1 mois\" ".($selected=="1 mois"?"selected":"").">1 mois</option>
			<option value=\"2 mois\" ".($selected=="2 mois"?"selected":"").">2 mois</option>
			<option value=\"3 mois\" ".($selected=="3 mois"?"selected":"").">3 mois</option>
			<option value=\"4 mois\" ".($selected=="4 mois"?"selected":"").">4 mois</option>
			<option value=\"5 mois\" ".($selected=="5 mois"?"selected":"").">5 mois</option>
			<option value=\"6 mois\" ".($selected=="6 mois"?"selected":"").">6 mois</option>
			<option value=\"7 mois\" ".($selected=="7 mois"?"selected":"").">7 mois</option>
			<option value=\"8 mois\" ".($selected=="8 mois"?"selected":"").">8 mois</option>
			<option value=\"9 mois\" ".($selected=="9 mois"?"selected":"").">9 mois</option>
			<option value=\"10 mois\" ".($selected=="10 mois"?"selected":"").">10 mois</option>
			<option value=\"11 mois\" ".($selected=="11 mois"?"selected":"").">11 mois</option>
			<option value=\"1\" ".($selected=="1"?"selected":"").">1 an</option>
			<option value=\"2\" ".($selected=="2"?"selected":"").">2 ans</option>
			<option value=\"3\" ".($selected=="3"?"selected":"").">3 ans</option>
			<option value=\"4\" ".($selected=="4"?"selected":"").">4 ans</option>
			</select>
			</td>
		</tr>";
	}
   
	// Etat exportleguide
	function f_etat_exportleguide() {
		$etat = $this->tools_guide->f_get_value('etat','exportleguide');

		echo "
		<tr>
			<td align=\"right\">Etat</td>
			<td style=\"padding-left:15px;\">";

		if ($etat <> '') {
			if ($etat == 0) {
				echo "<input  TYPE=RADIO name=\"etat\" value=0 CHECKED>  	Neuf ";
				echo "<input  TYPE=RADIO name=\"etat\" value=1>  			Occasion";
			} else {
				echo "<input  TYPE=RADIO name=\"etat\" value=0>  Neuf ";
				echo "<input  TYPE=RADIO name=\"etat\" value=1 CHECKED>  			Occasion";
			}    
		} else {
			echo "<input  TYPE=RADIO name=\"etat\" value=0 CHECKED>  	Neuf ";
			echo "<input  TYPE=RADIO name=\"etat\" value=1>  			Occasion";
		}

		echo "
			</td>
		</tr>";
	}

	// Validation
	function f_form_submit($libelle,$name) {
		echo "
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td style=\"padding-left:15px;\"><input value=\"$libelle\" type=\"submit\" name=\"$name\" class=\"button\" /></td>
			<td>";
		if ($name == 'valid_form') { }
		echo "
			</td>
		</tr>";
	}
   
	function f_form_end() {
		echo"
			</table>
			</form>
			</table>";
	}
   
	// Header formulaire
	function f_form_header($method,$name) {
		// Menu des guides
		echo "<form action=\"\" method=\"$method\" name=\"$name\">";
	}

	// Url actif - inactif
	function f_url_rewriting() {
		$url_rewriting = $this->tools_guide->f_get_value('url_rewriting','exportleguide');
		
		if ($url_rewriting != "") {
			$checked = "checked";
		} else {
			$checked = "";
		}
		
	  	echo "
		<tr>
			<td align=\"right\">URL rewriting</td>
			<td style=\"padding-left:15px;\">
			<INPUT TYPE=CHECKBOX NAME=\"url-rewriting\" $checked> Actif
			</td>
		</tr>";
	}

	// Export par déclinaison
	function f_makedeclinaison() {
		$makedeclinaison = $this->tools_guide->f_get_value('makedeclinaison','exportleguide');
		
		if ($makedeclinaison != "") {
			$checked = "checked";
		} else {
			$checked = "";
		}
		
	  	echo "
		<tr>
			<td align=\"right\"><br /><br /><b>Articles</b></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td align=\"right\">Exporter par déclinaison</td>
			<td style=\"padding-left:15px;\"><INPUT TYPE=CHECKBOX NAME=\"makedeclinaison\" $checked> Actif</td>
			<td></td>
		</tr>";
	}

	function f_usefreeshipping() {
		$usefreeshipping = $this->tools_guide->f_get_value('usefreeshipping','exportleguide');
		
		if ($usefreeshipping != "") {
			$checked = "checked";
		} else {
			$checked = "";
		}
		
	  	echo "
		<tr>
			<td align=\"right\">Utiliser la gratuité des frais de port</td>
			<td style=\"padding-left:15px;\"><INPUT TYPE=CHECKBOX NAME=\"usefreeshipping\" $checked> Actif</td>
			<td></td>
		</tr>";
	}

	function f_exportallproduct() {
		$exportallproduct = $this->tools_guide->f_get_value('exportallproduct','exportleguide');

		if ($exportallproduct != "") {
			$checked = "checked";
		} else {
			$checked = "";
		}
		
	  	echo "
		<tr>
			<td align=\"right\">Exporter tous les produits (évite les problemes de doublons)</td>
			<td style=\"padding-left:15px;\"><INPUT TYPE=CHECKBOX NAME=\"exportallproduct\" $checked> Actif</td>
			<td></td>
		</tr>";
	}

	// Choix type de description
	function f_description_courte() {
		$description_courte = $this->tools_guide->f_get_value('description_courte','exportleguide');

		if ($description_courte != "") {
			$checked = "checked";
		} else {
			$checked = "";
		}

	  	echo "
		<tr>
			<td align=\"right\">Descriptions courtes (non coché=longues)</td>
			<td style=\"padding-left:15px;\"><INPUT TYPE=CHECKBOX NAME=\"description_courte\" $checked> Actif</td>
			<td></td>
		</tr>";
	}

	// Choix export des produits inactifs également
	function f_actif_only() {
			$actif_only = $this->tools_guide->f_get_value('actif_only','exportleguide');

		if ($actif_only != "") {
			$checked = "checked";
		} else {
			$checked = "";
		}

	  	echo "
		<tr>
			<td align=\"right\">Uniquement les produits actifs</td>
			<td style=\"padding-left:15px;\"><INPUT TYPE=CHECKBOX NAME=\"actif_only\" $checked> Actif</td>
			<td></td>
		</tr>";
	}

	function f_CaracteristiquesFichier() {
		echo "
		<tr>
			<td align=\"right\"><br /><br /><b>Caract&eacute;ristiques Fichier</b></td>
			<td></td>
			<td></td>
		</tr>";
	}
   
	// Nom du fichier
	function f_nom_fichier() {
		$nom_fichier = $this->tools_guide->f_get_value('nom_fichier','exportleguide');
		
		if ($nom_fichier <> '') {
			$selected = $nom_fichier;
		} else {
			$selected = "produits_";
		}

		echo "
		<tr>
			<td align=\"right\">Préfixe du fichier (ex: produits_)</td>
			<td style=\"padding-left:15px;\"><input size=\"30\" type=\"text\" name=\"nom_fichier\" value=\"$selected\" /></td>
			<td></td>
		</tr>";

		return $nom_fichier;
	}

	// Nom type image
	function f_nom_type_image() {
		$nom_type_image = $this->tools_guide->f_get_value('nom_type_image','exportleguide');

		if ($nom_type_image <> '') {
			$selected = $nom_type_image;
		} else {
			$selected = "large_default";
		}

		echo "
		<tr>
			<td align=\"right\">Type des images (ex: large_default)</td>
			<td style=\"padding-left:15px;\"><input size=\"30\" type=\"text\" name=\"nom_type_image\" value=\"$selected\" /></td>
			<td></td>
		</tr>";

		return $nom_type_image;
	}
   
	function f_destDir() {
		$destDir = $this->tools_guide->f_get_value('destDir','exportleguide');
		$ex = 'catalog_'.chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));

		echo "
		<tr>
			<td align=\"right\">Répertoire ouvert aux comparateurs. Exemple :<br>$ex<br></td>
			<td style=\"padding-left:15px;\"><input size=\"30\" type=\"text\" name=\"destDir\" value=\"$destDir\" /></td>
			<td>Si ce répertoire est défini, le catalogue généré sera copié dedans. Le but est de :<br>
			1/ éviter que le comparateur ne télécharge le fichier alors qu'il est en cours de création<br>
			2/ avoir un répertoire situé endehors de l'arborescence admin<br>
			3/ avoir un répertoire avec un nom unique à votre installation Prestashop
			</td>
		</tr>";

		return $destDir;
	}

	function f_fullDestDir($fullDestDir) {
		echo "
		<tr>
			<td align=\"right\">chemin complet calculé &rarr;</td>
			<td colspan=\"2\" style=\"padding-left:15px;\">$fullDestDir</td>
		</tr>";
	}

	// Séparateur dans le fichier csv (Obsolète)
	function f_separateur() {
		$separateur = $this->tools_guide->f_get_value('separateur','exportleguide');
		echo "
		<tr>
			<td align=\"right\">Séparateur</td>
			<td colspan=\"2\" style=\"padding-left:15px;\">";

		if ($separateur<>'') {
			switch ($separateur) {
				case 0 :
					echo "<input  TYPE=RADIO name=\"separateur\" value=0 CHECKED>  	Virgule \",\" " ;
					echo "<input  TYPE=RADIO name=\"separateur\" value=1>  			Point-Virgule \";\" ";
					echo "<input  TYPE=RADIO name=\"separateur\" value=2>  			Pipe \"|\" ";
					echo "<input  TYPE=RADIO name=\"separateur\" value=3>		  	Tabulation \\t ";
					break;
				case 1 :
					echo "<input  TYPE=RADIO name=\"separateur\" value=0>  			Virgule \",\" " ;
					echo "<input  TYPE=RADIO name=\"separateur\" value=1 CHECKED>  	Point-Virgule \";\" ";
					echo "<input  TYPE=RADIO name=\"separateur\" value=2>  			Pipe \"|\" ";
					echo "<input  TYPE=RADIO name=\"separateur\" value=3>		  	Tabulation \\t ";
					break;
				case 2 :
					echo "<input  TYPE=RADIO name=\"separateur\" value=0>  			Virgule \",\" " ;
					echo "<input  TYPE=RADIO name=\"separateur\" value=1>  			Point-Virgule \";\" ";
					echo "<input  TYPE=RADIO name=\"separateur\" value=2 CHECKED>  	Pipe \"|\" ";
					echo "<input  TYPE=RADIO name=\"separateur\" value=3>		  	Tabulation \\t ";
					break;
				case 3 :
					echo "<input  TYPE=RADIO name=\"separateur\" value=0>  			Virgule \",\" " ;
					echo "<input  TYPE=RADIO name=\"separateur\" value=1>  			Point-Virgule \";\" ";
					echo "<input  TYPE=RADIO name=\"separateur\" value=2>  			Pipe \"|\" ";
					echo "<input  TYPE=RADIO name=\"separateur\" value=3 CHECKED>  	Tabulation \\t ";
					break;
			}
		} else {
			echo "<input  TYPE=RADIO name=\"separateur\" value=0 CHECKED>  	Virgule \",\" " ;
			echo "<input  TYPE=RADIO name=\"separateur\" value=1>  			Point-Virgule \";\" ";
			echo "<input  TYPE=RADIO name=\"separateur\" value=2>  			Pipe \"|\" ";
			echo "<input  TYPE=RADIO name=\"separateur\" value=3>		  	Tabulation \\t ";
		}

		echo "
			</td>
		</tr>";   
	}

	// Extension du fichier
	function f_extension_fichier() {
		$extension_fichier = $this->tools_guide->f_get_value('extension_fichier','exportleguide');

		if ($extension_fichier<>'') {
			$selected = $extension_fichier;
		} else {
			$selected = "csv";
		}

		echo "
		<tr>
		   <td align=\"right\">Extension du fichier g&eacute;n&eacute;r&eacute;</td>
		   <td style=\"padding-left:15px;\">
		   <select name=\"extension_fichier\">
		   <option value=\"$selected\">$selected</option>
		   <option value=\"csv\">csv</option>
		   <option value=\"txt\">txt</option>
		   </select>
		   </td>
		   <td></td>
		</tr>";
		return $selected;
	}

	function f_DisplayComparateurs() {
		$nom_comparateur = $this->tools_guide->f_get_value('nom_comparateur','exportleguide');

		if ($nom_comparateur<>'') {
			$selected = $nom_comparateur;
		} else {
			$selected = "leguide";
		}

		echo "
		<tr>
			<td align=\"right\">Comparateur :</td>
			<td style=\"padding-left:15px;\">
			  <select name=\"comparateur\" id=\"comparateur\">
				<option value=\"$selected\">$selected</option>
				<option value=\"leguide\">LeGuide</option>
				<option value=\"shopmania\">Shopmania</option>
				<option value=\"tigoon\">Tigoon</option>
				<option value=\"kelkoo\">Kelkoo</option>
				<option value=\"shoppydoo\">Shoppydoo</option>
				<option value=\"shopzilla\">Shopzilla</option>
				<option value=\"shopping\">Shopping</option>
			  </select>
			</td>
			<td><span style='color:red;'><b>ATTENTION !</b></span> Pour ShopZilla, le champ 'Référence collection' des fiches produit est utilisé pour renseigner la catégorie que le comparateur attend. Il doit donc être rempli pour un export correct vers ce comparateur.</td>
		</tr>";
		return $selected;
	}

	function f_Informations($module_name, $url_site_base_prestashop) {
		echo "
			<tr>
				<td align=\"right\"><br /><br /><b>Informations sur les comparateurs</b></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
			</tr>
			<tr>
				<td align=\"right\">LeGuide</td>
				<td style=\"padding-left:15px;\">Fichier .txt avec séparateur pipe <b>|</b> </td>
				<td style=\"padding-left:15px;\" class=\"links\"><a href=\"http://".$url_site_base_prestashop."modules/$module_name/docs/faq_leguide.pdf\" TARGET=\"_blank\">Voir la doc pdf de LeGuide</a></td>
			</tr>
			<tr>
				<td align=\"right\">Shopmania</td>
				<td style=\"padding-left:15px;\">Fichier .csv avec séparateur pipe <b>|</b> </td>
				<td style=\"padding-left:15px;\" class=\"links\"><a href=\"http://".$url_site_base_prestashop."modules/$module_name/docs/faq_shopmania.pdf\" TARGET=\"_blank\">Voir la doc pdf de ShopMania</a></td>
			</tr>
			<tr>
				<td align=\"right\">Tigoon</td>
				<td style=\"padding-left:15px;\">Fichier .csv avec séparateur point-virgule <b>;</b> </td>
				<td style=\"padding-left:15px;\" class=\"links\"><a href=\"http://".$url_site_base_prestashop."modules/$module_name/docs/faq_tigoon.pdf\" TARGET=\"_blank\">Voir la doc pdf de Tigoon</a></td>
			</tr>
			<tr>
				<td align=\"right\">Kelkoo</td>
				<td style=\"padding-left:15px;\">Fichier .txt avec séparateur tabulation <b>\\t</b> </td>
				<td style=\"padding-left:15px;\" class=\"links\"><a href=\"http://".$url_site_base_prestashop."modules/$module_name/docs/ExtranetMarchandKelkoo.pdf\" TARGET=\"_blank\">Voir la doc pdf de Kelkoo</a>
				<a href=\"http://".$url_site_base_prestashop."modules/$module_name/docs/GuideKelkoo.pdf\" TARGET=\"_blank\">ou ce guide pdf</a></td>
			</tr>
			<tr>
				<td align=\"right\">ShoppyDoo</td>
				<td style=\"padding-left:15px;\">Fichier .txt avec séparateur pipe <b>|</b>  </td>
				<td style=\"padding-left:15px;\"></td>
			</tr>
			<tr>
				<td align=\"right\">ShopZilla</td>
				<td style=\"padding-left:15px;\">Fichier .txt avec séparateur pipe <b>|</b> ou <b>\\t</b> </td>
				<td style=\"padding-left:15px;\" class=\"links\"><a href=\"http://merchant.shopzilla.fr/oa/general/taxonomy.xpml\" target=\"_blank\">Voir la liste des catégories Shopzilla</a></td>
			</tr>
			<tr>
				<td align=\"right\">Shopping</td>
				<td style=\"padding-left:15px;\">Fichier .csv avec séparateur virgule <b>,</b></td>
				<td style=\"padding-left:15px;\" class=\"links\"><a href=\"http://www.shopping.com\" target=\"_blank\">Voir la liste des catégories Shopping</a></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>";
	}

	// Génération par tranches pour serveurs mutualisés
	function f_displayCatalog() {
		$displayCatalog = $this->tools_guide->f_get_value('displayCatalog','exportleguide');
		(($displayCatalog) ? $checked = 'checked' : $checked = '');
	  	echo "
		<tr>
			<td align=\"right\"><br /><br /><b>Affichage</b></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td align=\"right\">Afficher le catalogue généré à l'écran</td>
			<td style=\"padding-left:15px;\"><INPUT TYPE=CHECKBOX NAME=\"displayCatalog\" $checked> Actif</td>
			<td></td>
		</tr>";
	}

	// Génération par tranches pour serveurs mutualisés
   function f_onMutu() {
		$onMutu = $this->tools_guide->f_get_value('onMutu','exportleguide');
		($onMutu != "") ? $onMutuChecked = 'checked' : $onMutuChecked = '';
		$onMutuStep = $this->tools_guide->f_get_value('onMutuStep','exportleguide');
		$onMutuOnceaDay = $this->tools_guide->f_get_value('onMutuOnceaDay','exportleguide');
		($onMutuOnceaDay != "") ? $onMutuOnceaDayChecked = 'checked' : $onMutuOnceaDayChecked = '';
		echo "
		<tr>
			<td nowrap=\"nowrap\" align=\"right\"><br /><br /><b>Serveurs mutualisés</b></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td align=\"right\">Générer par tranches</td>
			<td style=\"padding-left:15px;\"><INPUT TYPE=CHECKBOX NAME=\"onMutu\" $onMutuChecked> Actif</td>
			<td>Le script s'arrète volontairement après l'export de chaque tranche. Il doit donc être relancé autant de fois qu'il y a de tranches</td>
		</tr>
		<tr>
			<td align=\"right\">Nombre de catégories par tranche</td>
			<td style=\"padding-left:15px;\"><INPUT TYPE=TEXT NAME=\"onMutuStep\" VALUE=\"$onMutuStep\" SIZE=\"3\"><td>
			</td></td>
		</tr>
		<tr>
			<td align=\"right\">Lors d'une génération par tranches, ne pas générer plus d'un export par jour</td>
			<td style=\"padding-left:15px;\"><INPUT TYPE=CHECKBOX NAME=\"onMutuOnceaDay\" $onMutuOnceaDayChecked> Actif</td>
			<td>Utile pour ne pas écraser le fichier lors d'une génération par tranches appelée plusieurs fois par une tâche cron</td>
		</tr>";
	}

	function f_displayGroups() {
		global $cookie;

		$id_group = $this->tools_guide->f_get_value('id_group','exportleguide');
		echo "
		<tr>
			<td align=\"right\">Sélection du Groupe</td>
			<td style=\"padding-left:15px;\">
				<select name=\"id_group\">\r";

			$groups = Group::getGroups((int)($cookie->id_lang));

			if (sizeof($groups))
			{
				foreach ($groups as $group) {
					$selected = '';
					if ($group['id_group'] == $id_group)
						$selected = 'selected';
					echo "<option value='{$group['id_group']}' $selected>{$group['name']}</option>\r";
				}
			}

		echo "
				</select>\r
			</td>
			<td></td>
		</tr>";
	}
}

?>
