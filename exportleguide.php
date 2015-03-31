<?php

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Export LeGuide 1.61b                        	                                                                                            //
	//       Exports de catalogue vers comparateurs    	                                                                                                //
	//       Module original de M1bs largement amélioré grâce aux contributeurs du forum Prestashop                                                     //
	//       Voir fichier CHANGELOG.txt			     	                                                                                                //
	//       https://www.prestashop.com/forums/topic/22501-module-prestashop-exportleguidecom-export-csv-pour-exportleguide-et-autres-comparateurs/     //
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


if (!defined('_PS_VERSION_'))
	exit;

class exportleguide extends Module {
	function __construct() {
		$this->name = 'exportleguide';
		$this->tab = 'smart_shopping';
		$this->version = '1.61b';
		$this->author = 'Communauté';

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Export LeGuide');
		$this->description = $this->l('Exportez vos produits vers LeGuide, Shopmania, Tigoon, Kelkoo, Shoppydoo, Shopzilla, Shopping !');
	}

	function install() {
		if (parent::install() == false)
			return false;

		return true;
	}

	public function getContent() {
		$output = '<h2>'.$this->displayName.'</h2>';
		return $output.$this->displayForm();
	}

	public function getName() {
		$output = $this->name;
		return $output;
	}

	public function displayForm() {
		$output = '';
		ob_start();
		include('guide-script.php');
		$output = ob_get_clean();
		return $output;
		ob_end_clean();
	}

	public function uninstall() {
		$res = Db::getInstance()->execute('
			DROP TABLE `'._DB_PREFIX_.'guide_parameter`');
		Configuration::deleteByName('guide');
		return parent::uninstall();
	}

	function cronTask($catalogue='') {
		$_GET['auto']=$catalogue;
		include('guide-script.php');
	}
}
?>
