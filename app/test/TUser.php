<?php


class TUser extends TBase{


	public $tname = 'user';

	/**
	 * (non-PHPdoc)
	 * @see HBase::init()
	 */
	public function init(){}


	public function signUp(){
		$data = array(
			'FamilyName' => 'Popescu',
			'Email' => 'florin1s@exchangemania.com',
			'Password' => '123123',
			'Name' => 'Ion'
		);
		return $this->testApi('api/user/signUp/', $data);
	}


	public function login(){
		//	$Email, $Password, $Ip, $DeviceID
		$data = array(
			'email' => 'florin@exchangemania.com',
			'password' => '123123'
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