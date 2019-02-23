<?php

/**
 * Microsoft SQL Driver
 * @author florin
 *
 *
 *	limit	50
 *	start	63
 *	SELECT * FROM (SELECT TOP 50 * FROM (SELECT TOP 113 * FROM [vendorcheckin_dev].[dbo].[GT_Airlines] WHERE Deleted = 0 AND CountryID LIKE '233' ORDER BY [AirlineName] ASC, [CountryID] ASC) as [__inner top table__] ORDER BY [AirlineName] DESC, [CountryID] DESC) as [__outer top table__] ORDER BY [AirlineName] ASC, [CountryID] ASC
 *
 *	limit	50
 *	start	34
 *	SELECT * FROM (SELECT TOP 50 * FROM (SELECT TOP 65 * FROM [vendorcheckin_dev].[dbo].[GT_Airlines] WHERE Deleted = 0 AND CountryID LIKE '233' ORDER BY [AirlineName] DESC, [CountryID] DESC) as [__inner top table__] ORDER BY [AirlineName] ASC, [CountryID] ASC) as [__outer top table__] ORDER BY [AirlineName] DESC, [CountryID] DESC
 *
 *	SELECT * FROM
 *			(SELECT row_number() OVER (ORDER BY AirportID) AS rownum, * FROM GT_Airports ) AS A
 *		WHERE A.rownum BETWEEN (@Offset) AND (@Offset + @Limit)
 *
 *
 */
class ModelMySqlDriver implements ModelDriverTemplate{


	private $db;


	private $sql = '';


	private $top = '';


	private $whereField;


	private $link = NULL;


	private $sqlData = null;


	private $isSingleData = false;


	private $logSQL = MODEL_SQL_LOG;


	private $pkVal = 0;


	private $fields = '*';


	public function setFields($f){
		if(is_array($f)){
			if(array_keys($f) !== range(0, count($f) - 1)){
				$s = '';
				foreach($f as $fld=>$alias){
					if(is_numeric($fld)){
						$fld = $alias;
					}
					$s .= " $fld as $alias, ";
				}
				$f = substr($s, 0, strlen(trim($s)));
			}else{
				$f = implode(', ' , $f);
			}
		}
		$this->fields = $f;
	}


	public function __construct(){
		t::log("construct on db model ", "SQL");
		if(!$this->link){
		//	$this->link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
			$connInfo = isset($_SESSION['APPLICATION_DB_INFO']) ? $_SESSION['APPLICATION_DB_INFO'] : array();
			if(!empty($connInfo)){
				$connInfo = json_decode($connInfo);
				$DB_NAME = $connInfo->DB_NAME;
				$this->link = new mysqli($connInfo->DB_HOST, $connInfo->DB_USER, $connInfo->DB_PASS, $connInfo->DB_NAME);
			}else{
				$DB_NAME = DB_NAME;
				$this->link = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				t::log("construct on db model CONNECTION ", "SQL");
			}
		}
		if (!$this->link) {
    		die('Unable to connect or select database! ' . $DB_NAME);
		}
	}


	public function initSql($type){
		switch($type){
			case 'delete':
				$this->sql = "DELETE FROM {$this->getTName()} ";
				break;
			case 'insert':
				$this->sql = "INSERT INTO {$this->getTName()} (";
				break;
			case 'update':
				$this->sql = "UPDATE {$this->getTName()} SET ";
				break;
			case 'findAll':
				$this->sql = "SELECT {$this->fields} FROM {$this->getTName()} ";
				break;
			case 'findComplex':
				/**
				 * add group by
				 */
				$this->sql = "SELECT {$this->fields} FROM {$this->getTName()} [WHERE] [Order] [Limit] [Offset] ";
				break;
			case 'find':
			default:
				$this->sql = "SELECT {$this->top} * FROM {$this->getTName()} ";
				break;
		}
	}


	public function appendSql($s){
		$this->sql .= $s;
	}


	public function setupWhere($w){
		if(strlen(trim($w))>0){
			$this->sql .= ' WHERE ' . $w;
		}
	}


	public function getSql(){
		return $this->sql;
	}


	public function setSql($s){
		$this->sql = $s;
	}


	public function setLimit($l){
		$this->top = " TOP {$l} ";
	}


	public function setSingleData($s){
		$this->isSingleData = $s;
	}


	public function IsSingleData(){
		return $this->isSingleData;
	}


	public function clearLimit(){
		$this->top = '';
	}


	public function getPkVal(){
		return $this->pkVal;
	}


	private function disableStrictMode(){
		/*if(isset($_SESSION['MySQL_STRICT_DISABLE'])){
			return;
		}
		$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
		if (!$link || !mysqli_select_db($link, DB_NAME)) {
    		throw new Exception('Unable to connect or select database!');
		}*/
		/*$strictRs = mysqli_query($link, "select @@global.sql_mode as mode");
		$rRs = mysql_fetch_assoc($strictRs);
    	mysql_free_result($strictRs);
    	if(strlen(trim($rRs['mode']))>0){
    		mysql_query("SET @@global.sql_mode= ''", $this->link);
    	}
    	$_SESSION['MySQL_STRICT_DISABLE'] = true;*/
	}


	public function doSql(){
//		if(DISABLE_MySQL_STRICT_MODE){
//			$this->disableStrictMode();
//		}
		if(!$this->link){
			throw new Exception('Database connection problem');
		}
		if($this->logSQL){
			$this->logSQL($this->getSql());
		}
		$result = array();
		$no = 0;
		if(($rs = $this->link->query($this->getSql()))==TRUE){

		}else{
			$no = $this->link->errno;
			$er = $this->link->error;
		}

		if($no>0){
			die('error: ' . $no . ' : '. $this->getSql());
			echo "Give up on: <b>" . $this->getSql() . "</b></br><pre>";
			throw new Exception($er, $no);
		}

		if(gettype($rs)!='boolean'){
			if($rs->num_rows > 0){
				while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {
					$result[] = $row;
				}
			}
			$rs->free();
		}else{
			 $this->pkVal = mysqli_insert_id($this->link);
		}
		$this->sqlData = ($this->isSingleData==true) ? (isset($result[0])?$result[0]:$result) : $result;
		return $this->sqlData;
	}


	public function logSql($sql){
		echo $sql . "<br />";
	}

}