<?php 

/**
 * Common Db tasks must be here 
 * 		-	Eg: Sanitization
 * @author florin
 */
class ModelDbDriver extends ModelMySqlDriver {


	private $tName = '';	//	alias for 'table_name'
	
	
	private $pKeyName = '';	//	alias for 'primary key' name
	
	
	private $pKeyValue = 0;	//	alias for 'primary key' value
	
	
	protected $link;
	
	
	function __contruct(){
	//	php < 5.3	
		$this->tName = $this->getTable();
	//	php > 5.3	
	//	$this->tName = $this::TABLE;
	}
	
	
	public function getTName(){
		return $this->tName;
	}
	

	public function setPKeyName($p){
		$this->pKeyName = $p;
	}
	
	
	public function setPKeyValue($pv){
		$this->pKeyValue = $pv;
	}
	
	
	public function getPKeyName(){
		return $this->pKeyName;
	}
	
	
	public function getPKeyValue(){
		return $this->pKeyValue;
	}


}