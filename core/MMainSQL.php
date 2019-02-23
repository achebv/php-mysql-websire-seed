
<?php 

/**
 * Database management and access class
 * This is a very basic level of abstraction
 * 	@version 090704-01
 * 	@author Florin
 */
abstract class MMainSQL extends Registry{


	/**
	 * Private error flag
	 */
	private $hasError = false;


	/**
	 * Record of the last query
	 */
	protected $last;
	
	
	private $connection;
	
	
	private $pk = array();	// table => name=>value


	/**
	 * Base contruct
	 */
    public function __construct(){
    	$result = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$result){
			die('Error connecting to host: ' . " DB_HOST, DB_USER, DB_PASS, DB_NAME ");
		}
		$result->autocommit(true);
		$this->connection = $result;
    }


    /**
     * Close the active connection
     * @return void
     */
    public function closeConnection()
    {
    	$this->connection->close();
    }


    /**
     * Delete records from the database
     * @param String the table to remove rows from
     * @param String the condition for which rows are to be removed
     * @param int the number of rows to be removed
     * @return void
     */
    public function deleteRecords( $condition, $limit = '' )
    {
    	$table = $this->mname;
    	$limit = ( $limit == '' ) ? '' : ' LIMIT ' . $limit;
    	$delete = "DELETE FROM `{$table}` WHERE {$condition} {$limit}";
    	$this->executeQuery( $delete );
    }


    /**
     * Update records in the database
     * @param String the table
     * @param array of changes field => value
     * @param String the condition
     * @return bool
     */
    public function updateRecords($changes, $condition )
    {
    	$table = $this->mname;
    	$update = "UPDATE `" . $table . "` SET ";
    	foreach( $changes as $field => $value )
    	{
    		if(is_numeric($value)){
    			$update .= "`" . $field . "`={$value},";
    		}else{
    			$update .= "`" . $field . "`='{$value}',";
    		}
    	}

    	// remove our trailing ,
    	$update = substr($update, 0, -1);
    	if( $condition != '' )
    	{
    		$update .= " WHERE " . $condition;
    	}
    //	echo $update ." ::: <br />";
    	$this->executeQuery( $update );
    	return true;
    }

    
    private function insertMultiple($data){
    	$t = $this->get('t');
    	$insert = "INSERT INTO {$this->mname} (";
    	$fields = "";
    	$values = "";
    	$x=0;
    	foreach($data as $d){
    		$fields = "";
    		$values .= "(";
    		foreach($d as $f=>$v){
    			if($x==0){
    				$fields  .= "`$f`,";
    			}
    			$values .= ( is_numeric( $v ) && ( intval( $v ) == $v ) ) ? $v."," : "'" .addslashes($v). "',";
    		}
    		$x++;
	    	$fields = substr($fields, 0, -1);
    		$values = substr($values, 0, -1) . '),';
	    	$insert .= $fields;
    	}
    	
    	$values = substr($values, 0, -1);
    	$insert .= ') VALUES ' . $values;
    	$this->executeQuery( $insert );
    	return true;
    }
    
    
    private function insertSimple($data){
    	$fields = "";
    	$values = "";
    	foreach($data as $f=>$v){
    		$fields  .= "`$f`,";
    		$values .= ( is_numeric( $v ) && ( intval( $v ) == $v ) ) ? $v."," : ("'" . addslashes($v) . "',");
    	}
    //	remove our trailing ,
    	$fields = substr($fields, 0, -1);
    //	remove our trailing ,
    	$values = substr($values, 0, -1);
    	$insert = "INSERT INTO `{$this->mname}` ({$fields}) VALUES ({$values})";
    	$this->executeQuery( $insert );
    	return true;
    }
    

    /**
     * Insert records into the database
     * @param String the database table
     * @param array data to insert field => value
     * @return bool
     */
    public function insertRecords( $data )
    {
    	$t = $this->get('t');
    //	$t::prt($data);
    	if($t::isMultiArray($data)){
    		return $this->insertMultiple($data);
    	}else{
    		return $this->insertSimple($data);
    	}
    	return true;
    }
    
    
    public function setPk($name, $value){
    	$this->pk[$this->mname][$name] = $value;
    }
    
    public function setPkVal($value){
    	$this->pk[$this->mname][$this->pk] = $value;
    }
    
    
    public function save($data){
    	$pkArr = $this->pk[$this->mname];
    	$pkName = array_keys($pkArr);
    	$pkName = $pkName[0];
    	$pkVal = array_values($pkArr);
    	$pkVal = $pkVal[0];
    	if($pkVal>0){
    		$this->updateRecords($data, $pkName . "=" . $pkVal ." ");
    	}else{
    		$this->insertRecords($data);
    		$this->setPk($pkName, $this->getLastInsertId());
    	}
    }
    
    
    public function getPkValue(){
    	$v = array_values($this->pk[$this->mname]); 
    	return $v[0];
    }
    
    
    public function getAll(){
     $sql = "SELECT * FROM `{$this->mname}` ";
    	$this->executeQuery($sql, MYSQLI_STORE_RESULT);
    	return $this->last;
    }
    
    public function getAllByWhere($w){
    	$sql = "SELECT * FROM `{$this->mname}` WHERE {$w} ";
    	$this->executeQuery($sql, MYSQLI_STORE_RESULT);
    	return $this->last;
    }
    
    
    public function getByWhere($w){
    	return $this->getAllByWhere($w . " LIMIT 1");
    }
    
    
    public function resultToObject($result = null){
    	if($result==null){
    		$result = $this->last;
    	}
    	$o = $result->fetch_object();
    	$result->close();
    	return $o;
    }
    
    public function freeResult($result){
    	$result->close();
    }
    
    public function free($result){
    	$this->freeResult($result);
    }
    
    /**
     * Get last inserted id
     *
     * @return int
     */
    public function getLastInsertId(){
    	return $this->connection->insert_id;
    }


    /**
     * Get last error message
     *
     * @return string
     */
    public function getError(){
    	return $this->connection->error;
    }


    /**
     * test if error has occured
     *
     * @return bool
     */
    public function isError(){
    	return $this->hasError;
    }


    /**
     * Execute a query string
     * @param String the query
     * @return void
     */
    public function executeQuery( $queryStr )
    {
    	if(SQL_DEBUG){
    		t::log(" - executeQuery::: $queryStr <br /> \\n ");
    	}
    	if(($result = $this->connection->query($queryStr))==TRUE){
               $this->setQryCount('r');
          //	$this->connection->close();
         //	if(($result = $this->connection->multi_query($queryStr))==TRUE){
               $this->last = $result;
		}else{
			$this->hasError = true;
		}
		if($this->isError()) die($this->getError()."::".$queryStr);
    }


    /**
     * Get the rows from the most recently executed query, excluding cached queries
     * @return array
     */
    public function getRows($object = false)
    {
    	  return $object ? $this->last->fetch_object() : $this->last->fetch_array(MYSQLI_ASSOC);
    }


    /**
     * Gets the number of affected rows from the previous query
     * @return int the number of affected rows
     */
    public function affectedRows()
    {
    	return $this->connection->affected_rows;
    }


    /**
     * Sanitize data
     * @param String the data to be sanitized
     * @return String the sanitized data
     */
    public function sanitizeData( $data )
    {
    	return $this->connection->real_escape_string( $data );
    }


    /**
     * Deconstruct the object
     * close all of the database connections
     */
    public function __deconstruct()
    {
    	$this->connection->close();
    }

}