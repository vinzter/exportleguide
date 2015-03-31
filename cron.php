<?php
if (isset($_GET['cronKey'])) {
	include(dirname(__FILE__).'/../../config/config.inc.php');
	include(dirname(__FILE__).'/../../init.php');
	include(dirname(__FILE__).'/exportleguide.php');
	$exportleguide = new exportleguide();
	$params = Db::getInstance()->ExecuteS('
		SELECT parameter_value
		FROM '._DB_PREFIX_.'guide_parameter
		WHERE parameter_name = \'cronKey\'');
	$cronKey = htmlentities(strip_tags($_GET['cronKey']));

	if (!empty($params[0]['parameter_value']) AND $params[0]['parameter_value'] == $cronKey) {
		$catalogue = htmlentities(strip_tags($_GET['auto']));
		$exportleguide->cronTask($catalogue);
	}
}

?>