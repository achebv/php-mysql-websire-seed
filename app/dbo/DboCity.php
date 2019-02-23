<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2014-03-31 01:43:29
 */
class DboCity extends ModelActiveRecord{

	const TABLE = 'city';

	protected $pk = 'CityID';

	public $CityID;
	public $CountyID;
	public $Long;
	public $Lat;
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
//	we have CityID as Pk
	public function getPk(){
		return "CityID";
	}
}