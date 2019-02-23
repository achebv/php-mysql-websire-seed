<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2015-01-02 11:16:14
 */
class DboLogin extends ModelActiveRecord{

	const TABLE = 'login';

	protected $pk = 'LoginID';

	public $LoginID;
	public $UserID;
	public $Lang;
	public $SessionID;
	public $StartTime;
	public $EndTime;

	public static function finder( $fields = '*', $className = __CLASS__ ){
		return parent::finder($fields, $className);
	}


	public static function strictFields( $strict ){
		return parent::strictFields($strict);
	}


	/**
	 * get table name.
	 */
	public function getTable(){
		return self::TABLE;
	}
//	we have LoginID as Pk
	public function getPk(){
		return "LoginID";
	}
}