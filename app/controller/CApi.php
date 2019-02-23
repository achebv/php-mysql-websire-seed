<?php

class CApi extends CBase{


	protected $cname = "api";


	private $_segmentStart = 1;


	private $_params = 1;


	private $_crudCall = '';


	public function init(){ $this->setResponseFormat("json"); }


	/**
	 * Common tasks
	 */
	public function before(){
		$s = $this->get('segments');
		$this->error = false;
		$params = array();
		for ($i = $this->_segmentStart; $i<count($s); $i++){
			$params[] = $this->getSegment($i);
		}
		if(!isset($params[0])){ return false; }
		$this->storeObject('A'.ucfirst($params[0]), 'a'.$params[0]);
		$apiClass = $this->get('a'.$params[0]);
		if(!isset($params[1])){ return false; }
		$apiMethod = $params[1];
		unset($params[0]);
		unset($params[1]);
// 		t::p(call_user_func_array(array($apiClass, $apiMethod), $params));
		if(!method_exists($apiClass, $apiMethod)){
			$this->msg = "Method '$apiMethod' not found in class '". get_class($apiClass) ."'.";
			$this->error = true;
			return;
		}

		$r = new ReflectionMethod($apiClass, $apiMethod);
		$classParamsObjs = $r->getParameters();
		$classParams = array();
		foreach($classParamsObjs as $classParamsObj){
			$classParams[] = $classParamsObj->getName();
		}
		$callParams = array_keys($this->getAllFromPost());

		/*t::p($classParams);
		t::p($callParams);*/

		if($classParams === $callParams){
			$data = call_user_func_array(array($apiClass, $apiMethod), $this->getAllFromPost());
			if(is_array($data)){
				foreach($data as $key => $value){
					$this->$key = $value;
				}
			}
		}else{
			$this->msg = "Cannot call. Check POST parameters.";
			$this->error = true;
		}
		//	call_user_func_array(array($apiClass, "checkCrud"), array($this->_crudCall));
	}


	/**
	 * Index action
	 */
	public function index(){
		$this->api = "mother earth is not my mom, can be your mom.";
	}


	//	connections
	//	@experimental
	/*protected function create(){
		$this->_crudCall = "C";
	//	$this->parameters = $this->_params;
	}

	protected function read(){
		$this->_crudCall = "R";
	//	$this->parameters = $this->_params;
	}

	protected function update(){
		$this->_crudCall = "U";
	//	$this->parameters = $this->_params;
	}

	protected function delete(){
		$this->_crudCall = "D";
	//	$this->parameters = $this->_params;
	}*/

}