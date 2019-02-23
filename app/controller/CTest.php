<?php

class CTest extends CBase{


	protected $cname = "test";


	private $_segmentStart = 1;


	private $_params = 1;


	/**
	 * Default Controllers initialization
	 * @see ICApp::init()
	 */
	public function init(){
		$this->setResponseFormat('json');
	}


	/**
	 * Common tasks
	 */
	public function before(){
		$s = $this->get('segments');
		$params = array();
		for ($i = $this->_segmentStart; $i<count($s); $i++){
			$params[] = $this->getSegment($i);
		}
		if(!isset($params[0])){ return false; }
		$this->storeObject('T'.ucfirst($params[0]), 't'.$params[0]);
		$apiClass = $this->get('t'.$params[0]);
		if(!isset($params[1])){ return false; }
		$apiMethod = $params[1];
		unset($params[0]);
		unset($params[1]);
// 		t::p(call_user_func_array(array($apiClass, $apiMethod), $params));
		if(!method_exists($apiClass, $apiMethod)){
			die("Method '$apiMethod' not found in class '". get_class($apiClass) ."'.");
		}
		//	call_user_func_array(array($apiClass, "checkCrud"), array($this->_crudCall));
		echo call_user_func_array(array($apiClass, $apiMethod), $this->getAllFromPost());
		exit();
// 		$this->result = $apiClass->$apiMethod(array_values($params));
	}


	/**
	 * Index action
	 */
	public function index(){
		$this->testName = "index";
	}



}