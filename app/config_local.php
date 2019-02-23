<?php

//	@TODO make a setting
error_reporting(E_ALL);
date_default_timezone_set("Europe/Helsinki");
ini_set('dispay_errors', 'on');


define('DB_DRIVER', "MySQL");    //	PostgreSQL, MySQL
define('WEB_NAME', 'ds');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

define('DB_NAME', 'exp');

//	define( "MODEL_USE_SRC", true);


define ('PN', '/');
define ('PT', '/' . PN);
