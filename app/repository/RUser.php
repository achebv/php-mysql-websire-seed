<?php 

/**
 * 
 * User repository object
 * @author achebv
 *
 */
class RUser extends RBase{
	
	
	public $rname = 'user';
	
	
	function getField(){
		return array(
			'UserID',
			'Username',
			'Password',
			'Group'
		);
	}
	
}