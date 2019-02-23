<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2014-03-31 01:43:29
 */
class DboCountry extends ModelActiveRecord{

	const TABLE = 'country';

	protected $pk = 'CountryID';

	public $CountryID;
	public $Name;

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
//	we have CountryID as Pk
	public function getPk(){
		return "CountryID";
	}
}