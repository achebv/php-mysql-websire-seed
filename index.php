<?php

 header('Access-Control-Allow-Origin: *');
 header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Authorization");

header("Access-Control-Allow-Headers: X-Requested-With");

	require_once ('app/config.php');

	if(DEV_MODE){
		$starttime1 = microtime();
		$startarray = explode(" ", $starttime1);
		$starttime1 = $startarray[1] + $startarray[0];
	}

	ob_start();
//	first and foremost, start our sessions
	session_start();

//	add config file !
	$app = Registry::singleton();
	$app->runPage();

	if(DEV_MODE){
		$endtime = microtime();
		$endarray = explode(" ", $endtime);
		$endtime = $endarray[1] + $endarray[0];
		$totaltime = $endtime - $starttime1;
		$totaltime = round($totaltime,5);
		$statQry = $app->getQryCount();
	//	print_r($statQry);
		define('LOADED_TIME', " <div class='console'>Page was loaded in $totaltime seconds... </div>");
	//	die(LOADED_TIME);
	}

	class t{
		public static function p($v, $f=false){
			echo "<pre>";print_r ($v);echo "</pre>";if($f)	die('force stop');
		}
		public static function log($message, $level='INFO'){
			if(!DEV_MODE) return;
			if(file_exists(APP_PATH . 'misc/log/debug/')){
				$d = date('y_m_d_H');
				$myFile = APP_PATH . 'misc/log/debug/' . $d . ".log";
				$fh = fopen($myFile, 'a+') or die("can't open file");
				$stringData = $level . ' at ' . date('H:i:s')." - {$message}\n";
				fwrite($fh, $stringData);
				fclose($fh);
			}else{
				die("Location not found: " . APP_PATH . 'misc/log/sql/');
			}
		}
	}

