<?php

//
// Affiche des informations liées à l'article
//

class art_info extends My_sql {

 	// Préfix des tables
 	public function __construct($ps_, $mysql) {
        $this->ps_ = $ps_;
		$this->mysql = $mysql; // Récupère la connexion en cours
    }

    // Nom du shop
    function f_shop_name() {
		try {
			$Resulats = $this->mysql->TabResSQL("SELECT value FROM ".$this->ps_."configuration WHERE name = 'PS_SHOP_NAME'");
			foreach ($Resulats as $Valeur)
			{$nom_shop 	= $Valeur['value'];}
		}
		catch (Erreur $e) { echo $e -> RetourneErreur('Impossible de sélectionner le nom du magasin'); }
    }

	// Retourne le Taux de TVA
	function f_tva_taux($id_tax) {
		try {
			$Resulats = $this->mysql->TabResSQL("SELECT rate FROM ".$this->ps_."tax where id_tax =$id_tax");
			foreach ($Resulats as $Valeur) {
				return $taux_tva = $Valeur['rate'];
			}
		}
		catch (Erreur $e) { echo $e -> RetourneErreur('Impossible de sélectionner le taux de TVA'); }
	}

	// Nom de la catégorie
	function f_category_name($id_catego, $id_lang) {
		try {
			$Resulats = $this->mysql->TabResSQL("SELECT name FROM ".$this->ps_."category_lang WHERE id_category = $id_catego and id_lang=$id_lang");
			foreach ($Resulats as $Valeur) {
				$category_name		= $Valeur['name'];

				// catégorie sans le .01, .02
				$longueur_chaine 	= strlen($category_name);
				$position_point 	= strpos($category_name,".");

				if ($position_point<>"") {
					$category_name	= substr($category_name, $position_point+1, $longueur_chaine);
				}

				return $category_name;
			}
		}
		catch (Erreur $e) { echo $e -> RetourneErreur('Impossible de sélectionner le nom de la catégorie'); }
	}

	// URL de l'article
	function f_url_article($site_base_prestashop, $link_rewrite, $id_product, $id_lang, $id_category_default) {
		try {
			if ($id_category_default) {
                $Category = $this->mysql->TabResSQL("SELECT link_rewrite FROM ".$this->ps_."category_lang WHERE id_lang = $id_lang AND id_category = $id_category_default");
                $cat_rewrite = $Category[0]['link_rewrite'];
            }
			$Resulats = $this->mysql->TabResSQL("SELECT description,description_short,name,link_rewrite FROM ".$this->ps_."product_lang where id_lang = $id_lang and id_product= $id_product");

			foreach ($Resulats as $Valeur) {
				$link_rewrite = $Valeur['link_rewrite'];
				$hote = $_SERVER['HTTP_HOST'];

				// Si l'url rewriting est activé on affiche le chemin en lettre
				if (!empty($_POST['url-rewriting'])) {
                    if ($cat_rewrite) {
						$link_rewrite = "http://".$site_base_prestashop.$cat_rewrite."/".$id_product."-".$link_rewrite.".html";
                    } else {
                        $link_rewrite = "http://".$site_base_prestashop.$id_product."-".$link_rewrite.".html";
					}
                    return $link_rewrite;
				}
			}
		}
		catch (Erreur $e) { echo $e -> RetourneErreur('Impossible de sélectionner l\'image de l\'article'); }
	}

	// URL de l'image
	function f_url_image($site_base_prestashop, $id_product,$suffixe_nom_type_image) {
		try {
			// ajout dans la requête du critère cover=1 afin de sélectionner par défaut du produit
			$Resulats = $this->mysql->TabResSQL("SELECT id_image FROM ".$this->ps_."image where id_product = $id_product and cover = 1");

			foreach ($Resulats as $Valeur) {
				// ajout dans la requête récupérant le lien reécris pour les images
				$Resultlienimg = $this->mysql->TabResSQL("SELECT link_rewrite FROM ".$this->ps_."product_lang where id_product= $id_product");

				foreach ($Resultlienimg as $row) {
					$lienimage = $row['link_rewrite'];
				}

				$id_image = $Valeur['id_image'];

				//$url_image = "http://".$site_base_prestashop."img/p/".$id_product."-".$id_image."-large.jpg";
				$url_image = "http://".$site_base_prestashop."".$id_image."-".$suffixe_nom_type_image."/".$lienimage.".jpg";
				return $url_image;
			}
		}
		catch (Erreur $e) {echo $e -> RetourneErreur('Impossible de sélectionner l\'image de l\'article');}
	}

	// Prix de la livraison
	function f_delivery_price($weight, $id_carrier, $id_zone) {
		$id_range = 0;
		try {
			$Resulats = $this->mysql->TabResSQL("SELECT id_range_weight FROM ".$this->ps_."range_weight where delimiter1 <= $weight and delimiter2 >= $weight and id_carrier = $id_carrier");
			//error_log("SELECT id_range_weight FROM ".$this->ps_."range_weight where delimiter1 <= $weight and delimiter2 >= $weight and id_carrier=$id_carrier");

			foreach ($Resulats as $Valeur) {
				$id_range = $Valeur['id_range_weight'];
			}
		}
		catch (Erreur $e) {
			//echo $e -> RetourneErreur('Impossible de sélectionner id_range_weight dans ps_range_weight');
			$id_range = 0;
		}

		//Sélection du Prix
		try
			{
			$Resulats = $this->mysql->TabResSQL("SELECT price FROM ".$this->ps_."delivery where id_carrier = $id_carrier and id_zone = $id_zone ".( $id_range!=0?" and id_range_weight = $id_range":""));
			if ($Resulats) {
				foreach ($Resulats as $Valeur)
				{
					$delivery_price_ht = $Valeur['price'];
				}
			} else { $delivery_price_ht = 0; }

			$Resulats = $this->mysql->TabResSQL("SELECT DISTINCT rate FROM ".$this->ps_."tax t, ".$this->ps_."tax_rule tr, ".$this->ps_."carrier_tax_rules_group_shop ct WHERE t.id_tax = tr.id_tax and tr.id_tax_rules_group = ct.id_tax_rules_group and ct.id_carrier = $id_carrier");
			
			if ($Resulats) {
				foreach ($Resulats as $Valeur)
				{
						$delivery_price = round(($delivery_price_ht * (1+$Valeur['rate']/100)), 2);
				}
			} else { $delivery_price = round($delivery_price_ht, 2); }

			return $delivery_price;
		}
		catch (Erreur $e) { echo $e -> RetourneErreur('Impossible de sélectionner les prix de livraison'); }
	}

	// Récupération des seuils des fdp
	function f_get_seuil_prix_fdp_offert() {
		try {
			$Resulats = $this->mysql->TabResSQL("SELECT value FROM ".$this->ps_."configuration where name='PS_SHIPPING_FREE_PRICE'");

			foreach ($Resulats as $Valeur) {
				$seuil_prix_fdp_offert = $Valeur['value'];
			}
		}
		catch (Erreur $e) {
			//echo $e -> RetourneErreur('Impossible de sélectionner id_range_weight dans ps_range_weight');
			$seuil_prix_fdp_offert ='null';
		}
		return $seuil_prix_fdp_offert;
	}

	// Récupération des seuils des fdp
	function f_get_seuil_poids_fdp_offert() {
		try
		{
			$Resulats = $this->mysql->TabResSQL("SELECT value FROM ".$this->ps_."configuration where name='PS_SHIPPING_FREE_WEIGHT'");
			foreach ($Resulats as $Valeur) {
				return  $Valeur['value'];
			}
		}
		catch (Erreur $e) {
			echo $e -> RetourneErreur('Impossible de sélectionner id_range_weight dans ps_range_weight');
			$seuil_poids_fdp_offert ='null';
		}
		return $seuil_poids_fdp_offert;
	}

	// Nom du fournisseur
	function f_fournisseur_name($id_supplier) {
		try {
			$Resulats = $this->mysql->TabResSQL("SELECT name FROM ".$this->ps_."supplier WHERE id_supplier = $id_supplier");
			foreach ($Resulats as $Valeur) {
				$name_supplier = $Valeur['name'];
				return $name_supplier;
			}
		}
		catch (Erreur $e) { echo $e -> RetourneErreur('Impossible de sélectionner le nom du fournisseur'); }
	}

	// Marque
	function f_fabricant($id_manufacturer) {
		try {
			$Resulats = $this->mysql->TabResSQL("SELECT name FROM ".$this->ps_."manufacturer where id_manufacturer = $id_manufacturer");

			foreach ($Resulats as $Valeur) {
				$name_manufacturer = $Valeur['name'];
				return $name_manufacturer;
			}
		}
		catch (Erreur $e) { echo $e -> RetourneErreur('Impossible de sélectionner le fabricant / marque'); }
	}

	// Nom des attributs
	function f_attribute_name($id_product_attribute, $id_lang) {

		// Nombre d'attributs
		try {
			$Resulats = $this->mysql->TabResSQL("
			SELECT COUNT(".$this->ps_."attribute_lang.name) as nbre FROM ".$this->ps_."product_attribute
			LEFT JOIN ".$this->ps_."product_attribute_combination
			ON ".$this->ps_."product_attribute.id_product_attribute = ".$this->ps_."product_attribute_combination.id_product_attribute
			LEFT JOIN ".$this->ps_."attribute_lang
			ON ".$this->ps_."product_attribute_combination.id_attribute = ".$this->ps_."attribute_lang.id_attribute
			WHERE ".$this->ps_."attribute_lang.id_lang = $id_lang and ".$this->ps_."product_attribute.id_product_attribute=$id_product_attribute");

			foreach ($Resulats as $Valeur) {
				$nbre  = $Valeur['nbre'];
			}
		}
		catch (Erreur $e) { echo $e -> RetourneErreur('Impossible de sélectionner le nombre de déclinaisons'); }

		// Nom de la déclinaison
		$groupe_names 		= "";
		$attribute_name_t 	= "";

		try {
			$Resulats = $this->mysql->TabResSQL("
			SELECT ".$this->ps_."attribute_lang.name, ".$this->ps_."attribute_lang.id_attribute FROM ".$this->ps_."product_attribute
			LEFT JOIN ".$this->ps_."product_attribute_combination
			ON ".$this->ps_."product_attribute.id_product_attribute = ".$this->ps_."product_attribute_combination.id_product_attribute
			LEFT JOIN ".$this->ps_."attribute_lang
			ON ".$this->ps_."product_attribute_combination.id_attribute = ".$this->ps_."attribute_lang.id_attribute
			WHERE ".$this->ps_."attribute_lang.id_lang = $id_lang and ".$this->ps_."product_attribute.id_product_attribute=$id_product_attribute");

			foreach ($Resulats as $Valeur_att) {
				$name 			  = $Valeur_att['name'];
				$id_attribute	  = $Valeur_att['id_attribute'];
				$attribute_name_t = $name.$attribute_name_t;

					// Nom du groupe + Nom déclinaison

					try {
						$Resulats = $this->mysql->TabResSQL("
						select ".$this->ps_."attribute_group_lang.name
						from ".$this->ps_."attribute
						LEFT JOIN ".$this->ps_."attribute_group_lang
						ON ".$this->ps_."attribute.id_attribute_group = ".$this->ps_."attribute_group_lang.id_attribute_group
						where ".$this->ps_."attribute.id_attribute=$id_attribute
						and ".$this->ps_."attribute_group_lang.id_lang=$id_lang
						group by ".$this->ps_."attribute_group_lang.name");

						foreach ($Resulats as $Valeur) {
							$groupe_name = $Valeur['name'];
							
							if ($nbre>1) {
								$groupe_names = $groupe_names." ".$groupe_name.":$name";
							} else {
								$groupe_names = $groupe_names.$groupe_name.":".$attribute_name_t." ";
							}
						}
					}
					catch (Erreur $e) { echo $e -> RetourneErreur('Impossible de sélectionner le nom du groupe'); }
			}
			return "(".$groupe_names.")";
		}

		catch (Erreur $e) {
			echo $e -> RetourneErreur('Impossible de sélectionner le nom de l\'attribut');
		}
	}
}

?>
