<?php 

/**
 * 
 * User repository object
 * @author achebv
 *
 */
class RSuper extends RBase {
	
	
	public $rname = 'super';
	
	
	function getField(){
		return array(
			'Username',
			'Password'
		);
	}
	
	
}