<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2014-03-31 01:43:29
 */
class DboUserInput extends ModelActiveRecord{

	const TABLE = 'user_input';

	protected $pk = 'UserInputID';

	public $UserInputID;
	public $Key;
	public $Value;

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
//	we have UserInputID as Pk
	public function getPk(){
		return "UserInputID";
	}
}