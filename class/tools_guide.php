<?php
//
// Affiche des informations liées à l'article
//

class tool_guides extends My_sql {
	// Préfix des tables
	public function __construct($ps_, $mysql) {
		$this->ps_ = $ps_;
		$this->mysql = $mysql; // Récupère la connexion en cours
	}

	// Met à jour le paramètre du guide
	function f_update_value($parameter_name, $parameter_value, $parameter_guide) {
		try {
			$resulats = $this->mysql->ExecuteSQL("UPDATE `".$this->ps_."guide_parameter` SET `parameter_value` = '$parameter_value' WHERE parameter_name = '$parameter_name' AND parameter_guide='$parameter_guide'");
		}
		catch (Erreur $e){ echo $e -> RetourneErreur('Impossible de modifier la valeur de $parameter_name'); }
	}

	// Retourne une valeur de parametre
	function f_get_value($parameter_name, $parameter_guide) {
		try {
			$resulats = $this->mysql->TabResSQL("SELECT parameter_value FROM `".$this->ps_."guide_parameter` WHERE parameter_name ='$parameter_name' and parameter_guide='$parameter_guide'");

			//MàJ de table si version précédente du module installée
			if (empty($resulats) && in_array($parameter_name, array('displayCatalog', 'onMutu', 'onMutuStep', 'onMutuOnceaDay'))) {
				$this->mysql->ExecuteSQL("INSERT INTO ".$this->ps_."guide_parameter (`id_parameter` ,`parameter_name` ,`parameter_value`,`parameter_guide`) VALUES (NULL , 'displayCatalog', '','$parameter_guide')");
				$this->mysql->ExecuteSQL("INSERT INTO ".$this->ps_."guide_parameter (`id_parameter` ,`parameter_name` ,`parameter_value`,`parameter_guide`) VALUES (NULL , 'onMutu', '','$parameter_guide')");
				$this->mysql->ExecuteSQL("INSERT INTO ".$this->ps_."guide_parameter (`id_parameter` ,`parameter_name` ,`parameter_value`,`parameter_guide`) VALUES (NULL , 'onMutuStep', '','$parameter_guide')");
				$this->mysql->ExecuteSQL("INSERT INTO ".$this->ps_."guide_parameter (`id_parameter` ,`parameter_name` ,`parameter_value`,`parameter_guide`) VALUES (NULL , 'onMutuOnceaDay', 'on','$parameter_guide')");
				return 0;
			}

			if (empty($resulats) && in_array($parameter_name, array('destDir'))) {
				$this->mysql->ExecuteSQL("INSERT INTO ".$this->ps_."guide_parameter (`id_parameter` ,`parameter_name` ,`parameter_value`,`parameter_guide`) VALUES (NULL , 'destDir', '','$parameter_guide')");
				return;
			}

			if (empty($resulats) && in_array($parameter_name, array('cronKey'))) {
				$cronKey = md5(time());
				$this->mysql->ExecuteSQL("INSERT INTO ".$this->ps_."guide_parameter (`id_parameter` ,`parameter_name` ,`parameter_value`,`parameter_guide`) VALUES (NULL , 'cronKey', '$cronKey', '$parameter_guide')");
				return $cronKey;
			}

			if (empty($resulats) && in_array($parameter_name, array('id_group'))) {
				$this->mysql->ExecuteSQL("INSERT INTO ".$this->ps_."guide_parameter (`id_parameter` ,`parameter_name` ,`parameter_value`,`parameter_guide`) VALUES (NULL , 'id_group', '','$parameter_guide')");
				return;
			}

			return $resulats[0]['parameter_value'];
		}
		catch (Erreur $e){ echo $e -> RetourneErreur('Impossible de modifier la valeur de $parameter_name'); }
	}

	// Ajoute les catégories à mémoriser
    function f_get_insert_category($id_category, $parameter_guide) {
		try {
		// INSERT
			$resulats = $this->mysql->ExecuteSQL("INSERT INTO ".$this->ps_."guide_parameter (`id_parameter` ,`parameter_name` ,`parameter_value`,`parameter_guide`)VALUES (NULL , 'id_catego', '$id_category','$parameter_guide')");
		}
		catch (Erreur $e){ echo $e -> RetourneErreur('Impossible d\'inserer les id des categories'); }
	}

	// Supprimer les catégories à mémoriser
	function f_get_delete_category($parameter_guide) {
		try {
			// INSERT
			$resulats = $this->mysql->ExecuteSQL("DELETE FROM ".$this->ps_."guide_parameter WHERE parameter_name='id_catego' and parameter_guide='$parameter_guide'");
		}
		catch (Erreur $e){ echo $e -> RetourneErreur('Impossible de supprimer les id des catégories'); }
    }
}

?>