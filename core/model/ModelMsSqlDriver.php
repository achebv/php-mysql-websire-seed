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
class ModelMsSqlDriver implements ModelDriverTemplate{
	
	
	private $db;

	
	private $sql = '';
	
	
	private $top = '';
	

	private $whereField;

	
	private $link = NULL;
	
	
	private $sqlData = null;
	
	
	private $isSingleData = false;
	
	
	private $showSql = false;
	
	
	private $pkVal = 0;
	
	
	public function setShowSql($s){
		$this->showSql = $s;
	}
		
	
	public function __construct(){
		if(!$this->link){
			$this->link = mssql_connect('10.0.0.90', 'sa', ',sa_server1.');
		}
		if (!$this->link || !mssql_select_db('vendorcheckin_dev', $this->link)) {
    		die('Unable to connect or select database!');
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
				$this->sql = "SELECT * FROM {$this->getTName()} ";
				break;
			case 'findComplex':
				$this->sql = "SELECT * FROM 
					(SELECT row_number() OVER (ORDER BY [ORDER]) AS rownum, * FROM {$this->getTName()} [WHERE]) as X
						WHERE rownum BETWEEN [Offset] AND [Offset] + [Limit]";
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
	

	public function doSql(){
		if(!$this->link){
			throw new Exception('Database connection problem');
		}
		if($this->showSql){
			echo $this->getSql()." <br />";
		}
		$data = mssql_query( $this->getSql(), $this->link);
		$result = array();   
		if(gettype($data)!='boolean'){
			do {
		    	while (($row = mssql_fetch_assoc($data))==true){
	    	    	$result[] = $row;   
				}
			}while ( mssql_next_result($data) );
		//	Clean up
			mssql_free_result($data);
		}else{
			$data = mssql_query( "SELECT SCOPE_IDENTITY() as PKey", $this->link);
			$row = mssql_fetch_assoc($data);
			$this->pkVal = $row['PKey'];
		}
		$this->sqlData = ($this->isSingleData==true) ? $result[0] : $result;
		return $this->sqlData;
	}
	
}