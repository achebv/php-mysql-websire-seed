<?php


class Tools{


	private $app;


	/**
	 * Constructor...
	 */
    function __construct() {

	}


	public static function isMultiArray($a){
		foreach($a as $v) if(is_array($v)) return TRUE;
		return FALSE;
	}


	public static function prt($v, $end = false){
		echo '<pre>';
		print_r($v);
		echo '</pre>';
		if($end){
			exit();
		}
	}


	/** Read a XML file.
	 * @param $file_name
	 * @param string $xml_elem
	 * @return array
	 */
	public function readXmlFile($file_name, $xml_elem = ''){
		$xml = simplexml_load_file(APP_PATH . 'misc/xml/'.$file_name.'.xml');
		$items = count($xml);
		$r = array();
		for($i=0;$i<$items;$i++){
			$elements = (array)$xml->{$xml_elem}[$i];
			foreach($elements as $element){
				$r[$element['key']] = $element;
			}
		}
		return $r;
	}


	/**
	 * 		Send email in system
	 *
	 * @param string $to
	 * @param string $subject
	 * @param Mixed [string/array] $body
	 * @param string $template
	 * @param string $Name
	 * @param string $from
	 */
	public function sendEmail($to, $subject, $body, $template='index', $Name="", $from=''){
		return true;
		if(empty($from)){
			$from = NO_REPLY;
		}
		$mailTplFile = APP_PATH . 'email/' . $template . '.html';
		if(file_exists($mailTplFile)){
			$fc = file_get_contents($mailTplFile);
			$ba = array();
			if(!is_array($body)){
				$ba['content'] = $body;
				$body = $ba;
			}
			$body['WEBSITE'] = WEBSITE;
			foreach ($body as $k=>$v){
				$fc = str_replace('['.$k.']', $v, $fc);
			}
			$body = $fc;
		}
		$header = "From: ". $Name . " <" . $from . ">\r\n";
		$header .= "Content-type: text/html\r\n";
		return mail($to, $subject, $body, $header);
	}


	/**
	 * Format a phrase to a seo url construction !
	 */
	public function makeSeoLink($str) {
		$str = str_replace('  ', ' ', $str);
		$str = str_replace('"', '', $str);
		$str = str_replace('/', SEO_SEPARATOR, $str);
		$str = str_replace(' ', SEO_SEPARATOR, $str);
		$str = str_replace(' ', SEO_SEPARATOR, $str);
		$str = str_replace(' ', SEO_SEPARATOR, $str);
		$str = str_replace(',', '', $str);
		$str = str_replace(', ', SEO_SEPARATOR, $str);
		$str = str_replace('&nbsp;', SEO_SEPARATOR, $str);
		return strtolower($str).SEO_EXT;
	}


	/**
	 * Insert an element into array at specific position
	 *
	 * @param unknown_type $array
	 * @param unknown_type $insert
	 * @param unknown_type $position
	 * @return unknown
	 */
	public function array_insert(&$array, $insert, $position = -1) {
	     $position = ($position == -1) ? (count($array)) : $position ;
	     if($position != (count($array))) {
	          $ta = $array;
	          for($i = $position; $i < (count($array)); $i++) {
	               if(!isset($array[$i])) {
	                    die(print_r($array, 1)."\r\nInvalid array: All keys must be numerical and in sequence.");
	               }
	               $tmp[$i+1] = $array[$i];
	               unset($ta[$i]);
	          }
	          $ta[$position] = $insert;
	          $array = $ta + $tmp;
	          //print_r($array);
	     } else {
	          $array[$position] = $insert;
	     }

	     ksort($array);
	     return true;
	}


	/**
	 * Return the link for a page
	 *
	 * @param String $name
	 * @param Int $index
	 * @param string $lang
	 * @return String
	 */
	public function openPage($name, $index = 0, $lang = NULL) {
		if(!$lang){
			$lang = $this->app->getObject('lang')->getSystemLang();
		}
		$arr_data = '';
		$links = $this->app->getSetting('route');

	//	$this->prt($links[$lang]);
		$gasit = FALSE;
		foreach ($links[$lang]  as $key => $val){
			if($key == $name){
				if(is_array($val)){
					$gasit = TRUE;
					$arr_data = $val[$index];
				}else{
					$gasit = TRUE;
					$arr_data = $val;
				}
				break;
			}
			if($gasit){
				break;
			}
		}
		return $arr_data;
	}

  	public function generateRandomString(){
		// Create a random string, leaving out 'o' to avoid confusion with '0'
		$char = strtoupper(substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 4));
		// Concatenate the random string onto the random numbers
		// The font 'Anorexia' doesn't have a character for '8', so the numbers will only go up to 7
		// '0' is left out to avoid confusion with 'O'
		$str = rand(1, 7) . rand(1, 7) . $char;
		return $str;
	}


	/*
	 * return an array whose elements are shuffled in random order.
	 */
	function arrayShuffle($my_array = array()) {
	  $copy = array();
	  while (count($my_array)) {
	    // takes a rand array elements by its key
	    $element = array_rand($my_array);
	    // assign the array and its value to an another array
	    $copy[$element] = $my_array[$element];
	    //delete the element from source array
	    unset($my_array[$element]);
	  }
	  return $copy;
	}


	public function getIp() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		return $ip;
	}


	/**
	 * Captcha id generator
	 */
	public function captchaReset(){
		// Create a random string, leaving out 'o' to avoid confusion with '0'
		$char = strtoupper(substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 4));
		// Concatenate the random string onto the random numbers
		// The font 'Anorexia' doesn't have a character for '8', so the numbers will only go up to 7
		// '0' is left out to avoid confusion with 'O'
		$str = rand(1, 7) . rand(1, 7) . $char;
		$_SESSION['captcha_id'] = $str;
	}


	/**
	 * Function to cut a string
	 * @param string $str
	 * @param int $n
	 * @param string $delim
	 * @return string
	 */
	public function cutString($str, $n, $delim='...') {
		$len = strlen($str);
		if ($len > $n) {
			preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
			return rtrim($matches[1]) . $delim;
		} else {
			return $str;
		}
	}


}
?>