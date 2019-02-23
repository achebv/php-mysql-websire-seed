<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2014-03-31 01:43:29
 */
class DboListValue extends ModelActiveRecord{

	const TABLE = 'list_value';

	protected $pk = 'ListValueID';

	public $ListValueID;
	public $ListKey;
	public $ListValue;
	public $ListGroup;

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
//	we have ListValueID as Pk
	public function getPk(){
		return "ListValueID";
	}
}