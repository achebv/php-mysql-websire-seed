<?php

/**
 * Auto Generated Dbo
 *		-	generated at: 2015-01-02 11:16:14
 */
class DboUser extends ModelActiveRecord{

	const TABLE = 'user';

	protected $pk = 'UserID';

	public $UserID;
	public $Email;
	public $Password;
	public $Name;
	public $DateCreated;
	public $FamilyID;
	public $IsOwner;

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
//	we have UserID as Pk
	public function getPk(){
		return "UserID";
	}
}