<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2015-01-02 11:16:14
 */
class DboItem extends ModelActiveRecord{

	const TABLE = 'item';

	protected $pk = 'ItemID';

	public $ItemID;
	public $CategoryID;
	public $Price;
	public $Description;
	public $UserID;
	public $DateCreated;

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
//	we have ItemID as Pk
	public function getPk(){
		return "ItemID";
	}
}