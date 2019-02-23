<?php


class AUser extends ABase{


	/**
	 * 	Register a user in database
	 * @param $Email
	 * @param $Password
	 * @param $Name
	 * @return array
	 */
	public function signUp($FamilyName, $Email, $Password, $Name){
		$bu = $this->storeObject('BUser', 'bUser');
		return $bu->signUp($FamilyName, $Email, $Password, $Name);
	}


	/**
	 * 	Do a login
	 * @param $email
	 * @param $password
	 * @return mixed
	 */
	public function login($email, $password, $RememberMe){
		$bu = $this->storeObject('BUser', 'bUser');
		return $bu->login($email, $password);
	}


	/**
	 * 	Reset password
	 * @param $Email
	 * @return mixed
	 */
	public function resetPwd($Email){
		$bu = $this->storeObject('BUser', 'bUser');
		return $bu->resetPwd($Email);
	}


	/**
	 * Terminate a login session
	 * @param $LoginID
	 * @return mixed
	 */
	public function doLogout($LoginID){
		$bu = $this->storeObject('BUser', 'bUser');
		return $bu->doLogout($LoginID);
	}


	/**
	 * Check if the loginID is Valid
	 * @param $LoginID
	 * @return mixed
	 */
	public function isLoginValid($LoginID){
		$bu = $this->storeObject('BUser', 'bUser');
		return $bu->isLoginValid($LoginID);
	}


	/**
	 * Change a user password
	 * @param $LoginID
	 * @param $OldPass
	 * @param $NewPass
	 * @return mixed
	 */
	public function changePassword($LoginID, $OldPass, $NewPass){
		$bu = $this->storeObject('BUser', 'bUser');
		return $bu->changePassword($LoginID, $OldPass, $NewPass);
	}


	/**
	 * get user information
	 * @param $LoginID
	 * @return mixed
	 */
	public function getUserInfo($LoginID){
		$bu = $this->storeObject('BUser', 'bUser');
		return $bu->getUserInfo($LoginID);
	}

}