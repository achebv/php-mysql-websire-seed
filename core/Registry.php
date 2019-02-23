<?php

/**
 * The Registry object
 * Implements Singleton design patterns
 * @version 1
 * @author achebv (office[at]fsoft[dot]ro)
 */
class Registry {


	/**
	 * Flag for query count
	 * @access private
	 */
	private static $qryCount = array(
		'c' => 0,
		'r' => 0,
		'u' => 0,
		'd' => 0
	);


	/**
	 * Our array of objects
	 * @access private
	 */
	private static $objects = array();


	/**
	 * Our array of settings
	 * @access private
	 */
	private static $settings = array();


	/**
	 * The instance of the
	 * @access private
	 */
	private static $instance;


	/**
	 * Private constructor to prevent it being created directly
	 * @access private
	 */
	private function __construct(){}


	/**
	 * prevent cloning of the object: issues an E_USER_ERROR if this is attempted
	 */
	private function __clone(){
		trigger_error( 'Cloning the  is not permitted', E_USER_ERROR );
	}


	/**
	 * singleton method used to access the object
	 * @access public
	 * @return
	 */
	public static function singleton(){
		if( !isset( self::$instance ) ){
			$obj = __CLASS__;
			self::$instance = new $obj;
		}
		return self::$instance;
	}


	/**
	 * the only public function
	 */
	public function runPage(){
		self::storeCoreObjects(
			array(
				//	'lang' => 'Lang',
				't' => 'Tools'
			)
		);
		self::storeSetting(self::setLangs(), 'all_lang');
		self::storeSetting(self::setSegments(), 'segments');
		self::storeSetting($this->parseRouting(), 'route_pages');
		self::storeSetting(array('super', 'test', 'api', 'home'), 'accept_pages');
		$page = self::getCurrentPage();
		self::storePage($page[0], $page[0]);
		self::getObject($page[0])->render($page[1], $page[2]);
	}


	/**
	 * Routing system
	 */
	private function parseRouting(){
		$xml = simplexml_load_file(APP_PATH . 'routing.xml'); // old
//		$systemLang = $this->getSetting('system_lang');
//		$xml = simplexml_load_file(APP_PATH . 'misc/routing/' . $systemLang . '/routing.xml');
		$r = array();
		foreach($xml->language as $language){
			$lang = '';
			foreach($language->attributes() as $k=>$v) {
				$lang = (string)$v;
			}
			foreach($language->key as $key){
				$name = '';
				foreach($key->attributes() as $k1=>$v1) {
					$name = (string)$v1;
				}
				foreach($key->map as $map){
					$mapArr = $map;
					$r[$lang][$name][] = (string)$mapArr[0];
				}
			}
		}
		return $r;
	}


	public function getQryCount(){
		return self::$qryCount;
	}


	public function setQryCount($item){
		self::$qryCount[$item]++;
	}


	protected function getDictio($item){
		$item_value = self::get('lang');
		if(empty($item_value[$item]['value'])){
			return $item;
		}else{
			return $item_value[$item]['value'];
		}
	}

	/**
	 * Register helpers to registry
	 */
	protected function registerObjects($objs){
		//	$objs['model'] = 'base';
		//	self::get('t')->prt($objs, true);
		foreach ($objs as $k => $obj){
			if(is_array($obj)){
				foreach ($obj as $o){
					self::storeObject( strtoupper($k[0]). ucfirst($o), strtolower($k[0] . $o) );
				}
			}else{
				self::storeObject( strtoupper($k[0]). ucfirst($obj), strtolower($k[0] . $obj) );
			}
		}
	}


	/**
	 * Include my i18n files
	 */
	public function loadI18n($fromFile = true){
		global $lang;
		$lang = array();
		if($fromFile){
			$xml = simplexml_load_file(APP_PATH . 'l18n/' . SKIN_NAME . '/' . $this->get('system_lang') . '/langKeys.xml');
			foreach($xml as $element){
				foreach((array)$element->attributes() as $value){
					$lang[(string)$value['name']]['value'] = (string)$element;
					$lang[(string)$value['name']]['tiptext'] = isset($value['tiptext']) ? $value['tiptext'] : '';
				}
			}
		}
		self::storeSetting($lang, "lang");
		unset($lang);
	}


	/**
	 * get current page
	 */
	private function getCurrentPage(){
		$s = self::getSetting('segments');
		//	self::get('t')->prt($s, true);
		$currentPage = 'C';
		$currentAction = DEFAULT_ACTION;
		$t = count($s);
		$routeSegment = 0;
		if($t>0){
			$a_pages = self::getSetting('accept_pages');
			if($t>1){
				$currentAction = $s[1];
			}
			if(in_array($s[0], $a_pages)){
				$currentPage = 'C'.ucfirst($s[0]);
			}else{
				$currentPage = 'C'.ucfirst($s[0]);
				$r_pages = self::getSetting('route_pages');
				$lang = self::getSetting('system_lang');
				$r_pages = $r_pages[$lang];
				$inRoute = false;
				$fnd = false;
				foreach ($r_pages as $c => $rules){
					if($fnd){ break; }
					//	t::p($rules);
					$routeSegment = 0;
					if(is_array($rules)){
						foreach ($rules as $rule){
							if($rule==$s[0]){
								$currentPage = 'C' . $c;
								$inRoute = true;
								$fnd = true;
								break;
							}
							$routeSegment++;
							//			echo $routeSegment.":::";
						}
					}else{
						if($rules==$s[0]){
							$currentPage = 'C' . $c;
							$inRoute = true;
							break;
						}
					}
				}
				//	continue
				if(!$inRoute){
					$currentPage = 'C' . ucfirst(DEFAULT_CONTROLLER);
					$currentAction = $s[0];
				}
			}
		}else{
			$currentPage .= ucfirst(DEFAULT_CONTROLLER);
		}
//      t::p($currentPage);
//      t::p($currentAction);
//      t::p($routeSegment,1);
		//	self::get('t')->prt(array($currentPage, $currentAction), true);
		return array($currentPage, $currentAction, $routeSegment);
	}


	/**
	 * read all languages folder
	 */
	private function setLangs(){
		$flags=array();
		if (($handle = opendir(APP_PATH . 'l18n/' . SKIN_NAME))==true) {
			while (false !== ($file = readdir($handle))) {
				if($file!='.' && $file!='..' && $file!='.svn' && !is_file($file) ){$flags[] = $file;}
			}closedir($handle);
		}
		return $flags;
	}


	/**
	 * set system lang
	 */
	private function setLang($segments){
		$flags = self::getSetting('all_lang');
		$l = DEFAULT_LANG;
		if(isset($segments[0]) && in_array( $segments[0], $flags)){
			$l = $segments[0];
		}else{
			$l = isset($_SESSION['system_lang']) ? $_SESSION['system_lang'] : DEFAULT_LANG;
		}
		self::storeSetting($l, 'system_lang');
	}


	/**
	 * read url segments and store them
	 */
	private function setSegments(){
		$_PX = explode("/", $_SERVER["REQUEST_URI"] . '/');
		$segments = array();
		foreach($_PX as $item){
			if(strlen(trim($item))>0){
				if($item!=WEB_NAME){
					$segments[] = str_replace(SEO_EXT, '', $item);
				}
			}
		}
		self::setLang($segments);
		if(count($segments)>0){
			if(MULTILANG){
				$flags = self::getSetting('all_lang');
				if(in_array($segments[0], $flags)){
					unset($segments[0]);
				}
			}
		}
		//	self::get('t')->prt($segments, true);
		return array_merge($segments);
	}


	/**
	 * get all objects
	 */
	public function getObjects(){
		return self::$objects;
	}


	/**
	 * Function for store framework classes
	 */
	private function storeCoreObjects($obj){
		foreach ($obj as $key=>$val){
			$this->storeObject( $val, $key );
		}
	}

	public function get($key){
		$key = strtolower($key);
		$found = 0;
		if(isset(self::$objects[$key])){
			$found++;
			$inst = self::getObject($key);
		}
		if(isset(self::$settings[$key])){
			$found++;
			$inst = self::getSetting($key);
		}
		if($found>1){
			trigger_error("<pre>You try to get 1: $key ->  I find it in objects and in settings! What are you doing ?!", E_USER_ERROR);
		}
		if($found<1){
			trigger_error("<pre>You try to get 2: $key ->  I didn't find this key! What are you doing ?!", E_USER_ERROR);
		}
		return $inst;
	}



	/**
	 * Set object
	 * @param unknown_type $object
	 * @param unknown_type $key
	 */
	private function setObject($object, $key){
		self::$objects[$key] = new $object(self::$instance);
	}


	/**
	 * Stores an object in the
	 * @param String $object the name of the object
	 * @param String $key the key for the array
	 * @return void
	 */
	public function storeObject( $object, $key )
	{
		self::$objects[ $key ] = new $object( self::$instance );
		return self::$objects[ $key ];
	}


	private function storePage( $page, $key ){
		self::$objects[ $key ] = new $page( self::$instance );
	}

	/**
	 * Gets an object from the
	 * @param String $key the array key
	 * @return object
	 */
	public function getObject( $key )
	{
		if( is_object ( self::$objects[ $key ] ) )
		{
			return self::$objects[ $key ];
		}
	}


	/**
	 * Stores settings in the
	 * @param String $data
	 * @param String $key the key for the array
	 * @return void
	 */
	private function storeSetting( $data, $key ){
		self::$settings[ $key ] = $data;
	}



	/**
	 * Gets a setting from the
	 * @param String $key the key in the array
	 * @return void
	 */
	public function getSetting($key){
		return self::$settings[$key];
	}


	/**
	 * Get a value from post
	 * @param string $name
	 * @return string, int, null
	 */
	public function getPost($name){
		return isset($_POST[$name]) ? $_POST[$name] : NULL;
	}


	/**
	 * Get all values from post
	 */
	public function getAllFromPost($exclude = array()){
		$exclude[] = 'ts';
		$exclude[] = 'fn';
		$exclude[] = 'renderBox';
		$return = array();
		if(isset($_POST) && !empty($_POST)){
			foreach ($_POST as $k=>$v){
				if(!in_array($k, $exclude)){
					$return[$k] = $v;
					$this->$k = $v;
				}
			}
		}
		return $return;
	}


	public function openPage($page, $index = 0, $hasParams = false, $lang = NULL) {
		$page = ucfirst($page);
		$links = self::get('route_pages');
		$accept_pages = self::get('accept_pages');
		if(in_array( lcfirst($page), $accept_pages)){
			return WEB_PATH . lcfirst($page) .'/';
		}
		$lang = self::get('system_lang');
		if(MULTILANG){
			$link = WEB_PATH . $lang .'/';
		}else{
			$link = WEB_PATH;
		}
		$pageName = isset($links[$lang][$page]) ? $links[$lang][$page] : "[$page]";
		if(is_array($pageName)){
			$link .= $pageName[$index];
		}else{
			$link .= $pageName;
		}
		if(strlen(SEO_EXT)>0){
			return $hasParams ? ($link.'/') : ($link.SEO_EXT);
		}else{
			return $link.'/';
		}
	}


}