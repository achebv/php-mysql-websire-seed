<?php

class HAuth extends HBase implements IHAuth{

	public $hname = 'auth';

	private $resp = array();

	/**
	 * test if the user is logged
	 * @return bool
	 */
	public function IsLogged(){
		return false;
	}

	public function LoginUser()
	{
		// TODO: Implement LoginUser() method.
	}

	public function LogoutUser()
	{
		// TODO: Implement LogoutUser() method.
	}

	public function ResetPwd()
	{
		// TODO: Implement ResetPwd() method.
	}

	public function RegisterUser()
	{
		// TODO: Implement RegisterUser() method.
	}

	public function RegisterSuper()
	{
		// TODO: Implement RegisterSuper() method.
	}


	/** Authenticate a super user
	 * @param array $params
	 * @return array
	 */
	public function LoginSuper($params = array()){
		$mb = $this->storeObject('MBase', 'mBase');
		$localLogin = !$mb->dbExist();
		$isLogged = ($params['Username']==SUPER_USER && sha1($params['Password'])==SUPER_PASS);
		if(!$localLogin){
			$ds = null;
			if(class_exists("DaoSuper")){
				$ds = new DaoSuper();
			}
			if($ds instanceof DaoSuper){
                if(method_exists($ds, 'login')){
                    $isLogged = $ds->login($params);
                }
			}
		}
		if($isLogged){
			$this->storeSuper($params);
		}else{
			$this->resp['err'] = true;
			$this->resp['msg'] = "Combinatia User/Parola este gresita.";
		}
		return $this->resp;
	}


	private function storeSuper($params){
		$rs = $this->storeObject('RSuper', 'rSuper');
		$rs->Username = $params['Username'];
		$rs->Password = sha1($params['Password']);
		$rs->save();
		$this->resp['err'] = false;
		$this->resp['msg'] = "Va rugam asteptati cateva momente.";
		$this->resp['callFn'] = array('Common.reload()');
	}

	/** Check if super user is logged
	 * @return bool
	 */
	public function IsSuper(){
		$rs = $this->storeObject('RSuper', 'rSuper');
		return strlen(trim($rs->Username)) > 0;
	}

	/**
	 * logout a super user
	 */
	public function LogoutSuper()
	{
		$rs = $this->storeObject('RSuper', 'rSuper');
		$rs->delete();
		header("Location: " . $this->openPage('Super', 0, true));
		exit();
	}


}