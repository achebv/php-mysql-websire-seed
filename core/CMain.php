<?php

abstract class CMain extends Registry{


	private $_data = array();


	private $_responseFormat = 'html';


	private $_objects = array();


	private $_indexRoute = 0;


	private $_partialView = '';


	private $_excludeFromResponse = array('cname', 'renderBox', 'auth');


	private $_action = "";


	public $renderBox = "";


	/**
	 * All requests should be render... sometimes
	 */
	public function render($m, $idxRoute){
		$this->_indexRoute = $idxRoute;
		$_action = $m;
		$s = $this->get('segments');
		$this->loadI18n();
	//	if(!method_exists($this, $m)){
	//		echo '<pre>';
	//		trigger_error("No Method <b>{$m}</b> in controller <b>" . get_class($this). '</b>');
	//		exit();
	//	}
		$this->main();
		if(method_exists($this, 'before')){
			if($this->before()===false){
				return $this->getResponse();
			}
		}
		if(method_exists($this, $m)){
			$this->$m();
		}
		$this->request = $this->openPage($this->cname, $this->getRouteIndex(), true);
		$this->addToAppClient('routeIndex', $this->getRouteIndex());
		$this->addToAppClient('renderBox', $this->renderBox);
		$this->getResponse();
	}


	public function getAction(){
		return $this->_action;
	}


	/**
	 * get segment by index
	 * @param unknown_type $index
	 */
	public function getSegment($index){
		$s = $this->get('segments');
		return isset($s[$index]) ? $s[$index] : "";
	}


	/**
	 * get Routing index...
	 */
	public function getRouteIndex(){
		return $this->_indexRoute;
	}


	/**
	 * Send it to browser...
	 */
	private function getResponse(){
		$format = '_'. $this->_responseFormat;
		$this->$format();
	}


	/**
	 * Response format for client
	 * @param string $f
	 * @param array or string $excludeFromPost
	 */
	public function setResponseFormat($f, $excludeFromPost = array()){
		if($f=='json' && is_array($excludeFromPost)){
			$this->getAllFromPost($excludeFromPost);
		}
		if($f=='partial' && !is_array($excludeFromPost) ){
			$this->_partialView = $excludeFromPost;
		}
		$this->_responseFormat = $f;
	}


	/**
	 * get repository
	 * @param class $c
	 * @param fields $p
	 */
	public function getRep($c, $p = '*'){
		$rep = $this->get('r' . $c);
		if($p=='*'){
			return $rep->getFields();
		}else if (is_array($p)){
			$t = array();
			$all = $rep->getFields();
			foreach($p as $prop){
				$t[$prop] = $all[$prop];
			}
			return $t;
		}else{
			return $rep->$p;
		}
	}


	/**
	 * Controll all requests...
	 * 		- register objects
	 * 		- secure them... for example with this function
	 */
	protected function main(){
		$this->init();
	//	$this->get('t')->prt($this->_objects, true);
		parent::registerObjects($this->_objects);
	}


	/**
	 * render the respopnse for HTML file
	 */
	protected function _html(){
		include_once (APP_PATH . 'app/view/' . SKIN_NAME . '/V' .ucfirst($this->cname).'.phtml');
	}

	protected function _partial(){
		if(strlen(trim($this->_partialView)) < 1){
			trigger_error("Please specify a partial view.");
			die();
		}
        if(file_exists(APP_PATH . 'app/view/' . SKIN_NAME . '/_winTpl/' . $this->_partialView .'.phtml')){
            include_once (APP_PATH . 'app/view/' . SKIN_NAME . '/_winTpl/' . $this->_partialView .'.phtml');
        }else{
            include_once (APP_PATH . 'app/view/' . SKIN_NAME . '/' .$this->cname . '/' . $this->_partialView .'.phtml');
        }
	}

	/**
	 * render the respopnse for HTML file
	 */
	protected function _json(){
		header('Access-Control-Allow-Origin: *');
		$toExclude = array_merge($this->_excludeFromResponse, isset($_POST) ? array_keys($_POST) : array());
		foreach ($toExclude as $exc){
			if(isset($this->_data[$exc])){	unset($this->_data[$exc]);	}
		}
		$this->_data['success'] = true;
		echo json_encode($this->_data);
		exit();
	}


	/**
	 * Set objects for register them
	 */
	public function register($arr){
		$this->_objects = array_merge_recursive($this->_objects, $arr);
	}





	/**
	* Translate the page
	* @param request_uri $path
	* @param String $lang
	*/
	protected function translatePage($cname, $index=0, $lang){
		$links = self::get('route_pages');
		$returningItems = $links[$lang][ucfirst($cname)];
		$linksLang = is_array($returningItems) ? $returningItems[$index] : $returningItems;
		return WEB_PATH . $lang . '/' . $linksLang . SEO_EXT;
	}



    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }


    public function getData() {
    	if(isset($this->_data['cname'])){ unset($this->_data['cname']); }
    	return $this->_data;
    }


    public function __get($name) {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }


    public function __isset($name) {
        return isset($this->_data[$name]);
    }


    public function __unset($name) {
        unset($this->_data[$name]);
    }


}