<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2015-01-02 11:16:14
 */
class DboFamily extends ModelActiveRecord{

	const TABLE = 'family';

	protected $pk = 'FamilyID';

	public $FamilyID;
	public $Name;
	public $IsActive;

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
//	we have FamilyID as Pk
	public function getPk(){
		return "FamilyID";
	}
}