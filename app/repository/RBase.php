<?php 

class RBase{
	
	
	public $rname;
	
	
	private $_fields = array();
	
	
	private $_filled = false;
	
	
	private $_collection = array();

	
	function __construct(){
		$this->_filled = isset($_SESSION[$this->rname]);
		$f = $this->getField();
		$s = isset($_SESSION[$this->rname]) ? unserialize($_SESSION[$this->rname]) : array();
		foreach($f as $k){
			$this->_fields[$k] = $this->_filled ? (isset($s[$k]) ? $s[$k] : '') : '';
		}
	}
	
	
	public function addOrUpdate($object){
		$this->_collection[$key] = $object;
	}
	
	
	public function get($key){
		return $this->_collection[$key];
	}
	
	public function save(){
		$_SESSION[$this->rname] = serialize($this->_fields);
	}
	
	public function delete(){
		unset($_SESSION[$this->rname]);
	}
	
	
	function __get($name){
		if(!isset($this->_fields[$name])){
			$this->_fields[$name] = '';
		}
		return $this->_fields[$name];
	}
	
	
	function __set($name, $value){
		$this->_fields[$name] = $value;
	}
	
	
	function getFields(){
		return $this->_fields;
	}
	
	
}