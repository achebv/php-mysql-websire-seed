<?php

class CHome extends CBase{


	public $cname = "home";

	/**
	 * Default Controllers initialization
	 * @see ICApp::init()
	 */
	public function init(){
		$this->jsFiles[] = 'js/app/' . $this->cname;
	}


	/**
	 * Common tasks
	 */
	public function before(){

	}


	/**
	 * Index action
	 */
	public function index(){
		//	render home page

	}





}