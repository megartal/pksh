<?php 
if (!file_exists('vendor/config.php')) {
	die("Rename vendor/config-dist.php in config.php");
}

if (file_exists('config.php')) {
	require_once('config.php');
} else {	
	die("config file is required");
}
require_once(DIR_SYSTEM . 'startup.php');
define('DIR_OPENCART', str_replace('\'', '/', realpath(DIR_APPLICATION . '../')) . '/');
// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);
// Database 
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

$query2   = $db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."vendor_description` (
  `vendor_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keyword` varchar(255) NOT NULL,
  PRIMARY KEY (`vendor_id`,`language_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$query3   = $db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."vendor` (
  `vendor_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(96) NOT NULL,
  `telephone` varchar(32) NOT NULL,
  `fax` varchar(32) NOT NULL,
  `address` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `password` varchar(40) NOT NULL,
  `code` varchar(40) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `vendor_group_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL,  
  `status` tinyint(1) NOT NULL,
  `product_status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`vendor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$query4  = $db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."vendor_to_layout` (
  `vendor_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  PRIMARY KEY (`vendor_id`,`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$query   = $db->query("SHOW COLUMNS FROM `".DB_PREFIX."product` LIKE 'vendor_id'");
if(!$query->num_rows){
	$query1   = $db->query("ALTER TABLE  `".DB_PREFIX."product` ADD  `vendor_id` INT NOT NULL");
	echo 'vendor_id column ADDED on table '.DB_PREFIX.'product </br>';
} else {
	echo 'vendor_id column already exists on table '.DB_PREFIX.'product </br>';
}

$query   = $db->query("SHOW COLUMNS FROM `".DB_PREFIX."order_product` LIKE 'vendor_id'");
if(!$query->num_rows){
	$query1   = $db->query("ALTER TABLE  `".DB_PREFIX."order_product` ADD `vendor_id` INT");
	echo 'vendor_id column ADDED on table '.DB_PREFIX.'order_product </br>';
} else {
	echo 'vendor_id column already exists on table '.DB_PREFIX.'order_product </br>';
}

$query   = $db->query("SHOW COLUMNS FROM `".DB_PREFIX."order_product` LIKE 'order_status_id'");
if(!$query->num_rows){
	$query1   = $db->query("ALTER TABLE  `".DB_PREFIX."order_product` ADD  `order_status_id` INT NOT NULL");

	echo 'order_status_id column ADDED on table '.DB_PREFIX.'order_product </br>';
} else {
	echo 'order_status_id column already exists on table '.DB_PREFIX.'order_product </br>';
}


write_config_files();

exit('SUCCESS');

function write_config_files() {
	if (!is_writable(DIR_OPENCART . 'vendor/config.php')) {
		die('Warning: vendor/config.php needs to be writable for this module to be installed!');
	}
	$output  = '<?php' . "\n";
	$output .= '// HTTP' . "\n";
	$output .= 'define(\'HTTP_SERVER\', \'' . HTTP_SERVER . 'vendor/\');' . "\n";
	$output .= 'define(\'HTTP_CATALOG\', \'' . HTTP_SERVER . '\');' . "\n";
	$output .= 'define(\'HTTP_IMAGE\', \'' . HTTP_SERVER . 'image/\');' . "\n\n";

	$output .= '// HTTPS' . "\n";
	$output .= 'define(\'HTTPS_SERVER\', \'' . HTTPS_SERVER . 'vendor/\');' . "\n";
	$output .= 'define(\'HTTPS_CATALOG\', \'' . HTTPS_SERVER . '\');' . "\n";
	$output .= 'define(\'HTTPS_IMAGE\', \'' . HTTPS_SERVER . 'image/\');' . "\n\n";

	$output .= '// DIR' . "\n";
	$output .= 'define(\'DIR_APPLICATION\', \'' . DIR_OPENCART . 'vendor/\');' . "\n";
	$output .= 'define(\'DIR_SYSTEM\', \'' . DIR_OPENCART . 'system_vendor/\');' . "\n";
	$output .= 'define(\'DIR_DATABASE\', \'' . DIR_OPENCART . 'system/database/\');' . "\n";
	$output .= 'define(\'DIR_LANGUAGE\', \'' . DIR_OPENCART . 'vendor/language/\');' . "\n";
	$output .= 'define(\'DIR_TEMPLATE\', \'' . DIR_OPENCART . 'vendor/view/template/\');' . "\n";
	$output .= 'define(\'DIR_CONFIG\', \'' . DIR_OPENCART . 'system/config/\');' . "\n";
	$output .= 'define(\'DIR_IMAGE\', \'' . DIR_OPENCART . 'image/\');' . "\n";
	$output .= 'define(\'DIR_CACHE\', \'' . DIR_OPENCART . 'system/storage/cache/\');' . "\n";
	$output .= 'define(\'DIR_DOWNLOAD\', \'' . DIR_OPENCART . 'system/storage/download/\');' . "\n";
	$output .= 'define(\'DIR_LOGS\', \'' . DIR_OPENCART . 'system/storage/logs/\');' . "\n";
	$output .= 'define(\'DIR_UPLOAD\', \'' . DIR_OPENCART . 'system/storage/upload/\');' . "\n";
	$output .= 'define(\'DIR_MODIFICATION\', \'' . DIR_OPENCART . 'system/modvendor/\');' . "\n";
	$output .= 'define(\'DIR_CATALOG\', \'' . DIR_OPENCART . 'catalog/\');' . "\n\n";

	$output .= '// DB' . "\n";
	$output .= 'define(\'DB_DRIVER\', \'' .   DB_DRIVER . '\');' . "\n";
	$output .= 'define(\'DB_HOSTNAME\', \'' . DB_HOSTNAME . '\');' . "\n";
	$output .= 'define(\'DB_USERNAME\', \'' . DB_USERNAME . '\');' . "\n";
	$output .= 'define(\'DB_PASSWORD\', \'' . DB_PASSWORD . '\');' . "\n";
	$output .= 'define(\'DB_DATABASE\', \'' . DB_DATABASE . '\');' . "\n";
	$output .= 'define(\'DB_PREFIX\', \''   . DB_PREFIX . '\');' . "\n";
	$output .= '?>';

	$file = fopen(DIR_OPENCART . 'vendor/config.php', 'w');

	fwrite($file, $output);

	fclose($file);
}
?>