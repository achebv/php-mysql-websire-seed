<?php

class ABase extends AMain implements IAApp{


	/**
	 * api name
	 */
	//private $mname = 'base';


	public function checkCrud($callCrud){
		echo $callCrud.":::";
	}

}
