<?php

/**
 * Business Logic object
 * @author achebv
 */
class BUser extends BBase {


	public $bname = 'user';


	private $valid;


	private $_df = 'Y-m-d H:i:s';


	public function init(){
		$this->valid = $this->storeObject('HValid', 'hValid');
	}


	/**
	 * Register a user in database
	 * @param $Email
	 * @param $Password
	 * @param $Name
	 * @return array : response [error, msg, user_id]
	 */
	public function signUp($FamilyName, $Email, $Password, $Name){
		$result = array('error' => false, 'msg' => '');
		//	validation : begin
		$this->valid->filled_out(array($Name, $Email, $Password), false);
		if(!$this->valid->isValid()){
			$result['error'] = true;
			$result['msg'] = $this->getDictio('Toate campurile sunt obligatorii.');
			return $result;
		}
		$this->valid->valid_email($Email, false);
		if(!$this->valid->isValid()){
			$result['error'] = true;
			$result['msg'] = $this->getDictio('Adresa de email nu este valida.');
			return $result;
		}
		$userDb = DaoUser::finder()->findByEmail($Email);
		if($userDb instanceof DboUser){
			$result['error'] = true;
			$result['msg'] = $this->getDictio('Adresa de email exista deja.');
			return $result;
		}
		//	validation : end
		// add family
		$fam = new DaoFamily();
		$fam->Name = $FamilyName;
		$fam->IsActive = 0;
		$fam->save();
		// add user
		$user = new DaoUser();
		$user->Email = $Email;
		$user->Password = sha1($Password);
		$user->Name = $Name;
		$user->FamilyID = $fam->FamilyID;
		$user->IsOwner = 1;
		$user->save();
	// send notification email to me
		$t = $this->storeObject('Tools', 'tools');
	//	send email
		$mail = $t->sendEmail(REGISTER_APPROVAL, "BUGET APP: Cont nou.",
			array(
				'content' => " Familia: $FamilyName <br /> " .
					" Email: $Email <br />" .
					" Name: $Name <br /> "
			)
		);
		$result['msg'] = $this->getDictio('Contul a fost creat. Dupa aprobare veti fi notificat prin email.');
		return $result;
	}


	/**
	 * Execute a login action
	 * @param $Email
	 * @param $Password
	 * @return array
	 */
	public function login($Email, $Password){
		$result = array('error' => true, 'msg' => '');

		$this->valid->valid_email($Email, false);
		if(!$this->valid->isValid()){
			$result['error'] = true;
			$result['msg'] = $this->getDictio('error.invalid_email');
			return $result;
		}

		$userDb = DaoUser::finder()->findByEmail($Email);
		if($userDb instanceof DboUser){
			if($userDb->Password == sha1($Password)){

				//	check if login exist

				$criteria = new ModelActiveRecordCriteria();
				$criteria->OrdersBy = array(
					'LoginID' => 'DESC'
				);
				$criteria->Condition = " UserID = {$userDb->UserID} ";
				$criteria->Limit = 1;
				$loginDb = DaoLogin::finder()->find($criteria);

				/*if($loginDb instanceof DboLogin){
					if(strtotime($loginDb->EndTime) > strtotime(date($this->_df))){
						$result['error'] = true;
						$result['msg'] = $this->getDictio('error.login_on_device_exist');
						return $result;
					}
				}*/

				$tryLogin = $this->isLogged($userDb->UserID);
				$loginObj = new DaoLogin();
				if(!$tryLogin['error']){
					$loginObj = DaoLogin::finder()->findByLoginID($tryLogin['LoginID']);
				}

				//	add or extend login
				$start_date = date($this->_df);
				$loginObj->UserID = $userDb->UserID;
				$loginObj->SessionID = session_id();
				$loginObj->StartTime = $start_date;
				$loginObj->EndTime = date($this->_df, strtotime($start_date) + 86400 * 1);
				$loginObj->save();
				$result['error'] = false;
				$result['LoginID'] = $loginObj->LoginID;
			//	$result['UserID'] = $loginObj->UserID;
				$result['msg'] = $this->getDictio('success.login_ok');
				return $result;
			}
		}

		$result['error'] = true;
		$result['msg'] = $this->getDictio('error.invalid_login');
		return $result;

	}


	/**
	 * Reset user password and terminate all logins
	 * @param $Email
	 * @return array
	 */
	public function resetPwd($Email){
		$result = array('error' => true, 'msg' => '');

		$this->valid->valid_email($Email, false);
		if(!$this->valid->isValid()){
			$result['error'] = true;
			$result['msg'] = $this->getDictio('error.invalid_email');
			return $result;
		}

		$userDb = DaoUser::finder()->findByEmail($Email);
		if($userDb instanceof DboUser){
			//	expire all logins
			$logins = DaoLogin::finder()->findAllByUserID($userDb->UserID);
			if(count($logins)>0){
				foreach($logins as $login){
					if(strtotime($login->EndTime) > strtotime(date($this->_df))){
						$login->EndTime = date($this->_df);
						$login->save();
					}
				}
			}

			$t = $this->storeObject('Tools', 'tools');
			$newPass = $t->generateRandomString();
			$userDb->Password = sha1($newPass);
			$userDb->save();

			//	send email
			$mail = $t->sendEmail($Email, $this->getDictio('email.pass_reset_subj'),
					array(
						'content' => $this->getDictio('email.pass_reset_subj') . ' ' . $newPass
					)
			);
			if(!$mail || IsLocal){
				$result['pwd'] = $newPass;
			}
			$result['error'] = false;
			$result['msg'] = $this->getDictio('resetPwd.ok');
			return $result;
		}

	}


	/**
	 * execute a login expiration
	 * @param $LoginID
	 * @return array
	 */
	public function doLogout($LoginID){
		$result = array('error' => true, 'msg' => '');
		$daoLogin = DaoLogin::finder()->findByLoginID($LoginID);
		$result['error'] = !($daoLogin instanceof DboLogin);
		if(!$result['error']){
			if(strtotime($daoLogin->EndTime) > strtotime(date($this->_df))){
				$daoLogin->EndTime = date($this->_df);
				$daoLogin->save();
				$result['msg'] = $this->getDictio('logout.ok');
			}else{
				$result['msg'] = $this->getDictio('logout.already');
			}
		}else{
			$result['msg'] = $this->getDictio('logout.failed');
		}
		return $result;
	}


	/**
	 * Check if the UserID is Logged on this deviceID
	 * @param $UserID
	 * @return array
	 */
	private function isLogged($UserID){
		$result = array('error' => true);
		$criteria = new ModelActiveRecordCriteria();
		$criteria->OrdersBy = array(
			'LoginID' => 'DESC'
		);
		$criteria->Condition = " UserID = {$UserID} ";
		$criteria->Limit = 1;
		$loginDb = DaoLogin::finder()->find($criteria);

		$result['error'] = !($loginDb instanceof DboLogin);
		if(!$result['error']){
			$result['error'] = !(strtotime($loginDb->EndTime) > strtotime(date($this->_df)));
			$result['LoginID'] = $loginDb->LoginID;
		}
		return $result;
	}


	/**
	 * Change a user password
	 * @param $LoginID
	 * @param $OldPass
	 * @param $NewPass
	 * @return array
	 */
	public function changePassword($LoginID, $OldPass, $NewPass){
		$result = array('error' => true);
		$checkLogin = $this->isLoginValid($LoginID);
		if(!$checkLogin['error']){
			$UserID = $checkLogin['data']->UserID;
			$userDb = DaoUser::finder()->findByUserIDAndPassword($UserID, sha1($OldPass));
			if($userDb instanceof DboUser){
				$userDb->Password = sha1($NewPass);
				$userDb->save();
				$result['error'] = false;
				$result['msg'] = $this->getDictio('changePassword.ok');
			}else{
				$result['msg'] = $this->getDictio('changePassword.wrongOldPassword');
			}
		}else{
			$result['msg'] = $this->getDictio('changePassword.invalid_login_id');
		}
		return $result;
	}


	/**
	 * Get user Information by UserID
	 * @param $LoginID
	 * @return array
	 */
	public function getUserInfo($LoginID){
		$result = array('error' => true);

		$login = $this->isLoginValid($LoginID);
		if($login['error']){
			$result['msg'] = $this->getDictio('getUserInfo.invalid_login_id');
			return $result;
		}

		$loginData = $login['data'];
		$userDb = DaoUser::finder()->findByUserID($loginData->UserID);
		if($userDb instanceof DboUser){
			$result['error'] = false;
			$result['msg'] = $this->getDictio('userInfo.ok');
			unset($userDb->Password);
			unset($userDb->UserID);
			unset($userDb->rownum);
			$userDb->Dob = strtotime($userDb->Dob);
			$result['userData'] = $userDb;
		}else{
			$result['msg'] = $this->getDictio('userInfo.failed');
		}
		return $result;
	}


}