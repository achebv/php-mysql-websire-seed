<?php

class TBase extends TMain{


	/**
	 * Controller name
	 */
	private $hname = 'base';


	public function all(){}


	/**
	 * Common method to test a post from api class
	 * @param $url
	 * @param $data
	 * @return mixed
	 */
	protected function testApi($url, $data = array()){
		$params = http_build_query($data);
		$ch = curl_init( WEB_PATH . $url);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
		curl_close ($ch);
		return $result;
	}

}