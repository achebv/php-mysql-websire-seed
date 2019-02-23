<?php

	define("APP_PATH", str_replace('app', '', str_replace('\\', '/', dirname(__FILE__))));

	define('DOMAIN_ADDRESS', (strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/')) . (isset($_SERVER['HTTPS']) ?
			($_SERVER['HTTPS'] == "on" ? 's://' : '://') : '://') . $_SERVER['SERVER_NAME'])) .
			((strlen($_SERVER['SERVER_PORT']) > 0 && $_SERVER['SERVER_PORT'] != '80') ? (':' . $_SERVER['SERVER_PORT']) : '') . '/');

	/**
	 * Application specific
	 */
	include_once 'config_app.php';

	/**
	 * Define MODE : server vs Local to include setings
	 */
	define('IsLocal', strpos(DOMAIN_ADDRESS, 'localhost')!==false);
	include_once 'config_'.(IsLocal?'local':'server').'.php';

	define('WEB_PATH', DOMAIN_ADDRESS . (strlen(WEB_NAME) > 0 ? WEB_NAME . '/' : ''));
	define('WEB_ROOT', WEB_PATH . 'web/');

	//	true = development , false = production
 	if (!defined("DEV_MODE")) { define('DEV_MODE', false);  }
	if (!defined("SQL_DEBUG")) { define('SQL_DEBUG', false); }
	if (!defined("AUTOCREATE_OBJECTS")) { define('AUTOCREATE_OBJECTS', false); }
	if (!defined("MODEL_SQL_LOG")) { define('MODEL_SQL_LOG', false); }
	if (!defined("MODEL_USE_SRC")) { define('MODEL_USE_SRC', false); }

	define('DEFAULT_CONTROLLER', 'home');
	define('DEFAULT_ACTION', 'index');

//	We will use this to ensure scripts are not called from outside of the framework
	define( 'APP', TRUE );
	define( 'SEO_EXT', '.html' );			//	.html
	define( 'SEO_SEPARATOR', '-' );
	define( 'TEMPLATE_PATH', 'app/view/' );

	define( 'SKIN_NAME', 'default' );		//	default
	if (!defined("CLIENT_MIN")) { define("CLIENT_MIN", ''); }	//	-min
	if (!defined("BUILD_NO")) {	define("BUILD_NO", '?v1'); }	//	?'.time());

	if(!defined('DB_DUMP')){
		define ('DB_DUMP', "D:/xampp/mysql/bin/");
	}
	define ("DISABLE_MySQL_STRICT_MODE", true);

	/**
	 * Now I know that I use
	 */
	function __autoload($class_name) {
		$classesDir = array (
			APP_PATH . 'app/api/',
			APP_PATH . 'app/test/',
			APP_PATH . 'app/bl/',
	    	APP_PATH . 'app/controller/',
	    	APP_PATH . 'app/helper/',
			APP_PATH . 'app/dao/',
			APP_PATH . 'app/dbo/',
			APP_PATH . 'app/model/',
	    	APP_PATH . 'app/repository/'
		);
		$classesDir[] = APP_PATH . 'core' . (MODEL_USE_SRC ? '-src' : '') . '/';
		$classesDir[] = APP_PATH . 'core' . (MODEL_USE_SRC ? '-src' : '') . '/model/';
		foreach ($classesDir as $directory) {
	        if (file_exists($directory . $class_name . '.php')) {
				require_once ($directory . $class_name . '.php');
				return;
	        }else{
			//	echo $directory . $class_name . '.php<br />';
			}
	    }
	}