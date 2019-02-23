<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2015-01-02 11:16:14
 */
class DboCategory extends ModelActiveRecord{

	const TABLE = 'category';

	protected $pk = 'CategoryID';

	public $CategoryID;
	public $Name;
	public $ParentID;
	public $FamilyID;

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
//	we have CategoryID as Pk
	public function getPk(){
		return "CategoryID";
	}
}