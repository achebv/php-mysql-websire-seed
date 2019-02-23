<?php


abstract class CBase extends CMain implements ICApp{

	/**
	 * Add stuff to javascript
	 */
	private $AppClient = array();

	/**
	 * Controller name
	 */
	protected $cname = 'base';


	/**
	 * My default css files
	 */
	public $cssFiles = array();

	/**
	 * My default js files
	 */
	public $jsFiles = array();

	/**
	 * data for views
	 */
	protected $_data = array();


	protected $request = array();


	protected $isPagePublic = false;


	public $isLogged = false;


	public $userInfo = array();


	public function __construct(){
        $this->cssFiles[] = 'css/reset';
        $this->cssFiles[] = 'css/common';

		/*
        $this->jsFiles[] = 'js/jqwidgets/jqxcore';
        $this->jsFiles[] = 'js/jqwidgets/jqxexpander';
        $this->jsFiles[] = 'js/jqwidgets/jqxinput';
		*/
    }


	/**
	 * default action for application
	 * @see CMain::main()
	 */
	public function main(){

		//  get authentication manager
		$this->auth = $this->storeObject('HAuth', 'hauth');

		switch($this->cname){
			case "super":

				break;
			default:
				/*if($this->auth->IsSuper()){
					$this->auth->LogoutSuper();
				}
				if(!$this->isPagePublic && !$this->auth->IsLogged()){
					header("Location: " . $this->openPage('Login'));
					return;
				}*/
				break;
		}

		/*
		if($this->cname=='super'){
			if($this->auth->IsLogged()){
				$this->auth->LogoutUser();
			}
			if(!$this->auth->IsSuper()){
				if($this->getSegment(1)!='login'){
					header("Location: " . $this->openPage('Super', 0, true) . 'login/');
				}
				return;
			}
		}else{
			if($this->cname!='home' && !$this->auth->IsLogged()){
				header("Location: " . $this->openPage('Home'));
				return;
			}
		}*/
		parent::main();
		$item_value = $this->get('lang');
		$this->addToAppClient('Dictio', $item_value);

		/*if (!$this->auth->IsLogged() && !$this->isPagePublic && $this->cname != 'admin') {
			header("Location: " . $this->openPage('MyAccount'));
			return;
		}*/
	}


	public function dbExist(){
		$mb = $this->storeObject('MBase', 'mBase');
		return $mb->dbExist();
	}

	/**
	 * point to auth manager
	 */
	public function auth(){
		$this->setResponseFormat('json');
		$response = $this->auth->{ucfirst($this->getSegment(2))}($this->getAllFromPost());
		if(!is_array($response)){
			$this->err = true;
			$this->msg = "Eroare, va rugam reveniti.";
			return;
		}
		foreach($response as $key => $value){
			$this->$key = $value;
		}
	}


	/**
	 * Add javascript code to frontend
	 * @param $key
	 * @param $value
	 */
	public function addToAppClient($key, $value){
		$this->AppClient[$key] = $value;
	}


	/** Get value from AppClient
	 * @param $key
	 * @return string
	 */
	public function getKeyFromAppClient($key){
		return isset($this->AppClient[$key]) ? $this->AppClient[$key] : '';
	}


	/** Get entire AppClient
	 * @return array
	 */
	public function getAppClient(){
		return $this->AppClient;
	}


	/** Check if a page is submitted
	 * @return bool
	 */
	protected function isPostBack(){
		return isset($_POST) && !empty($_POST);
	}


	/**
	 * Get template for window
	 */
	protected function winTpl(){
		$this->setResponseFormat('partial', $this->getPost('winTpl'));
	}


	/**
	 * @param $file_name
	 * @param $xml_elem
	 * @param bool $multiple
	 * @return array
	 */
	public function readXmlList($file_name, $xml_elem, $multiple = false){
		$r = array();
		$file = APP_PATH . 'misc/xml/'.$file_name.'.xml';
		if(!file_exists($file)){ return $r; }
		$xml = simplexml_load_file($file);
		foreach($xml->$xml_elem as $zone){
			$lang = '';
			foreach((array)$zone->attributes() as $elem){
				if($multiple){
					$r[(string)$elem['key']][] = (string)$elem['name'];
				}else{
					$r[(string)$elem['key']] = (string)$elem['name'];
				}
			}
		}
		return $r;
	}


	/**
	 * @param string $n
	 * @param mixed $v
	 */
	protected function _set($n, $v){
		$this->_data[$n] = $v;
	}


	/**
	 *
	 * @param string $k
	 * @return Ambigous <object, string:>
	 */
	protected function _get($k){
		return isset($this->_data[$k]) ? $this->_data[$k] : $k;
	}


}