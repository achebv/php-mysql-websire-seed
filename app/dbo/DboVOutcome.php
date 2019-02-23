<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2015-01-02 11:16:14
 */
class DboVOutcome extends ModelActiveRecord{

	const TABLE = 'v_outcome';

	protected $pk = 'ParentName';

	public $ParentName;
	public $Name;
	public $CategoryID;
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
//	we have ParentName as Pk
	public function getPk(){
		return "ParentName";
	}
}