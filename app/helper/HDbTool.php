<?php 
/**
 * 
 * Database tools class:
 * 	- back-up database / restore
 * 	- repopulate for dev / production
 * 	- Model creation
 * 
 * @author fsoft.ro
 *
 */
class HDbTool extends HBase{
	
	
	public $hname = 'dbTool';
	

	public function init(){
		$this->register(array(
			'helper' => array('file', 'dbInit')
			)
		);
	}
	
	
	public function initDb(){
		$this->get('hdbInit')->runTask();
	}
	
	
	/**
	 * Back-up my database
	 */
	public function backup(){
		$pref = date("Ymd_His") . '_';
		t::log("starting dbTask");
		//	1
		$ex1 = '"'.DB_DUMP.'mysqldump.exe" --user='.DB_USER.' --password='.DB_PASS.' --no-create-info --no-create-db --databases '.DB_NAME.' > ' . APP_PATH . 'misc/sql/backup/' . $pref . 'db_data.sql';
		exec($ex1, $out);
		t::log("back-up data: $ex1 --- " . json_encode($out));
		//	2
		$ex2 = '"'.DB_DUMP.'mysqldump.exe" --user='.DB_USER.' --password='.DB_PASS.' --no-data --no-create-db --databases '.DB_NAME.' > ' . APP_PATH . 'misc/sql/backup/' . $pref . 'db_schema.sql';
		exec($ex2, $out);
		t::log("back-up schema: $ex2 --- " . json_encode($out));
		//	3
		$ex3 = '"'.DB_DUMP.'mysqldump.exe" --user='.DB_USER.' --password='.DB_PASS.' --no-create-db --databases '.DB_NAME.' > ' . APP_PATH . 'misc/sql/backup/' . $pref . 'db_full.sql';
		exec($ex3, $out);
		t::log("back-up full: $ex3 --- " . json_encode($out));
	} 
	
	/**
	 * Restore from db file
	 */
	public function restore($file='db', $backup=true){
		if($backup){
			$this->backup();
		}
		/**
		 * @todo : check if database exists and set flag
		 */
		$mb = $this->storeObject('MBase', 'mBase');
		$useDbName = $mb->dbExist();
		$rest = '"'.DB_DUMP.'mysql.exe" --user='.DB_USER.' --password='.DB_PASS.' ' . ($useDbName ? DB_NAME : '' ) . ' < ' . APP_PATH . 'misc/sql/deploy/'.$file;
		exec($rest, $out );
		t::log("restore db ( $rest ) : " . json_encode($out));
	}
	
	
	/**
	 * 	Create DBO and DAO
	 */
	public function dbo(){
		$s = 'do'.DB_DRIVER;
		$objects = $this->$s();
		$s1 = '';
		$directory = APP_PATH . "app/dao/";
		$this->get('hfile')->remove(APP_PATH . 'app/dbo/');
		foreach($objects as $className=>$vals){
			//	$this->tools->prt($vals);
			$s1 .= "starting create object $className... <br />";
			$this->generateDBO($className, $vals['cols'], isset($vals['pk'])?$vals['pk']:'');
			if(!class_exists('DAO' . $this->getClassSuffix($className))){
				$this->generateDAO($className, $vals['cols'], isset($vals['pk'])?$vals['pk']:'');
			}
			if(!class_exists('M' . $this->getClassSuffix($className))){
				$this->createModel($className, $vals['cols'], isset($vals['pk'])?$vals['pk']:'');
			}
			$s1 .= "...end<br />";
		}
		t::log($s1);
		return $s;
	}
	
	/**
	* MySQL database driver
	*/
	private function doMySQL(){
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if ($mysqli->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$str_q = "SELECT TABLE_NAME AS class,
						GROUP_CONCAT(DISTINCT CONCAT_WS('', COLUMN_NAME) ORDER BY ORDINAL_POSITION SEPARATOR ', ') AS vars
					FROM information_schema.COLUMNS
					WHERE TABLE_SCHEMA='".DB_NAME."'
					GROUP BY TABLE_NAME
					ORDER BY TABLE_NAME";
		$objects = array();
		$result = $mysqli->query($str_q);
		while(($row = $result->fetch_array())==true){
			$objects[$row[0]] = array('cols' => $row[1], 'pk' => substr($row[1], 0, strpos($row[1], ',')));
		}
		return $objects;
	}
	
	
	/**
	 * PostgreSQL database driver
	 */
	private function doPostgreSQL(){
		//	options='--client_encoding=UTF8'
		$dbconn = pg_connect("host=".DB_HOST." port=5432 dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS);
	
		//	get all columns
		$str_q = "SELECT c.table_name as className, textcat_all(c.column_name || ', ') as cols
						FROM information_schema.COLUMNS c
							WHERE c.table_catalog = '".DB_NAME."' AND c.table_schema = 'public'
							GROUP BY c.table_name order by c.table_name asc";
		$result = pg_query($dbconn, $str_q);
		$objects = array();
		while (($row = pg_fetch_row($result))==true) {
			$objects[$row[0]] = array('cols' => substr($row[1], 0, strlen($row[1])-2));
		}
	
		//	get pk-s
		$str_q = "SELECT table_name, column_name FROM information_schema.constraint_column_usage
						WHERE table_catalog = '".DB_NAME."' AND table_schema = 'public'
						ORDER BY table_name ASC";
		$result = pg_query($dbconn, $str_q);
		while (($row = pg_fetch_row($result))==true) {
			if(isset($objects[$row[0]])){
				$objects[$row[0]] = array_merge($objects[$row[0]], array('pk' => $row[1]));
			}
		}
		return $objects;
	}
	


	/**
	 * Phisicaly create classes
	 * @param String $className
	 * @param Array $cols
	 * @param String $pk
	 */
	private function generateDAO($className, $cols, $pk=''){
		$CName = $this->getClassSuffix($className);
		$dbo_dir = "app/dao";
		$dbo_file = $dbo_dir."/Dao{$CName}.php";
		if(!is_dir($dbo_dir)) if(!mkdir($dbo_dir, 0777)) return "<span style='color:red'>ERROR 1</span>";
		if(!is_writeable($dbo_dir)) if(!chmod($dbo_dir, 0777)) return "<span style='color:red'>ERROR 2</span>";
		if(!$fp = fopen($dbo_file, 'wb')) return "<span style='color:red'>ERROR 3</span>";
		if(!flock($fp, LOCK_EX)){ fclose($fp); return "<span style='color:red'>ERROR 4</span>"; }

		$cols = trim($cols);
	//	$cols = substr($cols, 0, strlen($cols)-1);
		$cols = explode(', ', $cols);
		$cols = str_replace(',', '', $cols);
		$t = date('Y-m-d H:i:s');


		$x = <<<FOO
<?php

/**
 * Auto Generated Dao
 *		-	generated at: {$t}
 */
class Dao{$CName} extends Dbo{$CName}{



}
FOO;

		if(!fwrite($fp, $x)){ fclose($fp); return "<span style='color:red'>ERROR 5</span>"; }
		chmod($dbo_file, 0777);

		flock($fp, LOCK_UN);
		fclose($fp);

		return "<span style='color:green'>OK</span>";
	}


	private function getClassSuffix($className){
		$pieces = explode('_', $className);
		if(count($pieces)>1){
			$CName = '';
			foreach($pieces as $p){
				$CName .= ucfirst($p);
			}
		}else{
			$CName = ucfirst($className);
		}
		return $CName;
	}

	/**
	 * Phisicaly create classes
	 * @param String $className
	 * @param Array $cols
	 * @param String $pk
	 */
	private function generateDBO($className, $cols, $pk=''){
		$CName = $this->getClassSuffix($className);
		$dbo_dir = "app/dbo";
		$dbo_file = $dbo_dir."/Dbo{$CName}.php";
		if(!is_dir($dbo_dir)) if(!mkdir($dbo_dir, 0777)) return "<span style='color:red'>ERROR 1</span>";
		if(!is_writeable($dbo_dir)) if(!chmod($dbo_dir, 0777)) return "<span style='color:red'>ERROR 2</span>";
		if(!$fp = fopen($dbo_file, 'wb')) return "<span style='color:red'>ERROR 3</span>";
		if(!flock($fp, LOCK_EX)){ fclose($fp); return "<span style='color:red'>ERROR 4</span>"; }

		$cols = trim($cols);
	//	$cols = substr($cols, 0, strlen($cols)-1);
		$cols = explode(', ', $cols);
		$cols = str_replace(',', '', $cols);
		$t = date('Y-m-d H:i:s');


		$x = <<<FOO
<?php

/**
 * Auto Generated Dbo
 *		-	generated at: {$t}
 */
class Dbo{$CName} extends ModelActiveRecord{

	const TABLE = '{$className}';

FOO;
		if(strlen(trim($pk))>0){
			$x .= <<<FOO

	protected \$pk = '{$pk}';

FOO;
		}
			$x .= <<<FOO

FOO;
		foreach($cols as $i)
			$x .= <<<FOO

	public \$$i;
FOO;
				$x .= <<<FOO


	public static function finder( \$fields = '*', \$className = __CLASS__ ){
		return parent::finder(\$fields, \$className);
	}


	public static function strictFields( \$strict ){
		return parent::strictFields(\$strict);
	}


	/**
	 * get table name.
	 */
	public function getTable(){
		return self::TABLE;
	}

FOO;
		if(strlen(trim($pk))>0){
			$x .= <<<FOO
//	we have $pk as Pk
	public function getPk(){
		return "$pk";
	}
FOO;
		}
			$x .= <<<FOO

FOO;

		$x .= <<<FOO

}
FOO;

		if(!fwrite($fp, $x)){ fclose($fp); return "<span style='color:red'>ERROR 5</span>"; }
		chmod($dbo_file, 0777);

		flock($fp, LOCK_UN);
		fclose($fp);

		return "<span style='color:green'>OK</span>";
	}


	public function createModel($className, $cols, $pk=''){
		$CName = $this->getClassSuffix($className);
		$dbo_dir = "app/model";
		$dbo_file = $dbo_dir."/M{$CName}.php";
		if(!is_dir($dbo_dir)) if(!mkdir($dbo_dir, 0777)) return "<span style='color:red'>ERROR 1</span>";
		if(!is_writeable($dbo_dir)) if(!chmod($dbo_dir, 0777)) return "<span style='color:red'>ERROR 2</span>";
		if(!$fp = fopen($dbo_file, 'wb')) return "<span style='color:red'>ERROR 3</span>";
		if(!flock($fp, LOCK_EX)){ fclose($fp); return "<span style='color:red'>ERROR 4</span>"; }
		$cols = trim($cols);
		//	$cols = substr($cols, 0, strlen($cols)-1);
		$cols = explode(', ', $cols);
		$cols = str_replace(',', '', $cols);
		$t = date('Y-m-d H:i:s');

		$x = <<<FOO
<?php

/**
 * Auto Generated Model Class
 *		-	generated at: {$t}
 */
class M{$CName} extends MBase{

	public \$mname = '{$className}';

FOO;
		if(strlen(trim($pk))>0){
			$x .= <<<FOO

	public \$pk = '{$pk}';

FOO;
		}
		$x .= <<<FOO

FOO;

		$x .= <<<FOO

}
FOO;

		if(!fwrite($fp, $x)){ fclose($fp); return "<span style='color:red'>ERROR 5</span>"; }
		chmod($dbo_file, 0777);

		flock($fp, LOCK_UN);
		fclose($fp);

		return "<span style='color:green'>OK</span>";
	}

}