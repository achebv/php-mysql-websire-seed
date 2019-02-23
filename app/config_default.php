<?php

//	@TODO make a setting
error_reporting(E_ALL);
date_default_timezone_set("Europe/Helsinki");
ini_set('dispay_errors', 'on');


define('DB_DRIVER', "MySQL");    //	PostgreSQL, MySQL
define('WEB_NAME', 'buget_api');
define('DB_HOST', 'localhost');
define('DB_USER', 'extjs_client');
define('DB_PASS', 'cl!3nt!!');

define('DB_NAME', 'sa_server');

//	define( "MODEL_USE_SRC", true);


define ('PN', '/');
define ('PT', '/' . PN);
