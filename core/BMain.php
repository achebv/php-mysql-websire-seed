<?php 

abstract class BMain extends Registry{
	
	private $_objects = array();
	
	
	function __construct(){
		$this->init();
		parent::registerObjects($this->_objects);
	}

	
	/**
	 * Set objects for register them
	 */
	public function register($arr){
		$this->_objects = array_merge($this->_objects, $arr);
	}
	
}