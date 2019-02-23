<?php


class TCategory extends TBase{


	public $tname = 'category';

	/**
	 * (non-PHPdoc)
	 * @see HBase::init()
	 */
	public function init(){}


	public function outcome(){
		$data = array(
			'FamilyID' => 1
		);
		return $this->testApi('api/category/outcome/', $data);
	}


	public function importCat(){
die('too risky to run !');
       
		$data = array(
			'FamilyID' => 6
		);
		return $this->testApi('api/category/importCat/', $data);
	}


	// old


	public function login(){
		//	$Email, $Password, $Ip, $DeviceID
		$data = array(
			'Email' => 'florin@exchangemania.com',
			'Password' => '123123',
			'Lang' => 'ro',
			'DeviceID' => 'AB:34:32:P4:9U'
		);
		return $this->testApi('api/user/login/', $data);
	}


	public function resetPwd(){
		//	$Email
		$data = array(
			'Email' => 'florin@exchangemania.com'
		);
		return $this->testApi('api/user/resetPwd/', $data);
	}


	public function doLogout(){
		$data = array(
			'LoginID' => 1
		);
		return $this->testApi('api/user/doLogout/', $data);
	}


	public function isLoginValid(){
		$data = array(
			'LoginID' => 2
		);
		return $this->testApi('api/user/isLoginValid/', $data);
	}


	public function changePassword(){
		$data = array(
			'LoginID' => 2,
			'OldPass' => '123123',
			'NewPass' => '123123'
		);
		return $this->testApi('api/user/changePassword/', $data);
	}


	public function getUserInfo(){
		$data = array(
			'LoginID' => 2
		);
		return $this->testApi('api/user/getUserInfo/', $data);
	}

}